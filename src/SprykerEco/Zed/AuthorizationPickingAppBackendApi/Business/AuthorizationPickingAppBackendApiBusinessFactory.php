<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business;

use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiDependencyProvider;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders\ScopeFinder;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders\ScopeFinderInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Grant\AuthCodeGrantType;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Processor\AuthorizationRequestProcessor;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Processor\AuthorizationRequestProcessorInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\ScopeProvider;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\ScopeProviderInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\UserProvider;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\UserProviderInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\AuthCodeRepository;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\AuthCodeRepositoryInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\ClientRepository;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\ScopeRepository;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\UserRepository;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator\UserValidator;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator\UserValidatorInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External\AuthorizationPickingAppBackendApiToYamlAdapterInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToUserFacadeInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig getConfig()
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface getEntityManager()
 */
class AuthorizationPickingAppBackendApiBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Processor\AuthorizationRequestProcessorInterface
     */
    public function createAuthorizationRequestProcessor(): AuthorizationRequestProcessorInterface
    {
        return new AuthorizationRequestProcessor(
            $this->createAuthCodeGrantType(),
            $this->createUserValidator(),
            $this->getConfig(),
        );
    }

    /**
     * @return \League\OAuth2\Server\Grant\GrantTypeInterface
     */
    public function createAuthCodeGrantType(): GrantTypeInterface
    {
        $authCodeGrantType = new AuthCodeGrantType(
            $this->getConfig(),
            $this->createAuthCodeRepository(),
        );

        $authCodeGrantType->setClientRepository($this->createClientRepository());
        $authCodeGrantType->setScopeRepository($this->createScopeRepository());

        return $authCodeGrantType;
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator\UserValidatorInterface
     */
    public function createUserValidator(): UserValidatorInterface
    {
        return new UserValidator(
            $this->createUserRepository(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\AuthCodeRepositoryInterface
     */
    public function createAuthCodeRepository(): AuthCodeRepositoryInterface
    {
        return new AuthCodeRepository(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \League\OAuth2\Server\Repositories\ClientRepositoryInterface
     */
    public function createClientRepository(): ClientRepositoryInterface
    {
        return new ClientRepository(
            $this->getRepository(),
        );
    }

    /**
     * @return \League\OAuth2\Server\Repositories\ScopeRepositoryInterface
     */
    public function createScopeRepository(): ScopeRepositoryInterface
    {
        return new ScopeRepository(
            $this->getRepository(),
            $this->createScopeProvider(),
            $this->createScopeFinder(),
        );
    }

    /**
     * @return \League\OAuth2\Server\Repositories\UserRepositoryInterface
     */
    public function createUserRepository(): UserRepositoryInterface
    {
        return new UserRepository(
            $this->createUserProvider(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\UserProviderInterface
     */
    public function createUserProvider(): UserProviderInterface
    {
        return new UserProvider(
            $this->getUserFacade(),
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToUserFacadeInterface
     */
    public function getUserFacade(): AuthorizationPickingAppBackendApiToUserFacadeInterface
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::FACADE_USER);
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\ScopeProviderInterface
     */
    public function createScopeProvider(): ScopeProviderInterface
    {
        return new ScopeProvider(
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders\ScopeFinderInterface
     */
    public function createScopeFinder(): ScopeFinderInterface
    {
        return new ScopeFinder(
            $this->getConfig(),
            $this->getYamlAdapter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External\AuthorizationPickingAppBackendApiToYamlAdapterInterface
     */
    public function getYamlAdapter(): AuthorizationPickingAppBackendApiToYamlAdapterInterface
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::ADAPTER_YAML);
    }
}

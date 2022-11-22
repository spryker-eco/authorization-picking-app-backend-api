<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories;

use Generated\Shared\Transfer\OauthScopeFindTransfer;
use Generated\Shared\Transfer\OauthScopeRequestTransfer;
use Generated\Shared\Transfer\OauthScopeTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Entities\ScopeEntity;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface
     */
    protected AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository;

    /**
     * @var array<\Spryker\Zed\OauthExtension\Dependency\Plugin\OauthScopeProviderPluginInterface>
     */
    protected $scopeProviderPlugins;

    /**
     * @var array<\Spryker\Glue\OauthExtension\Dependency\Plugin\ScopeFinderPluginInterface>
     */
    protected $scopeFinderPlugins;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository
     * @param array<\Spryker\Zed\OauthExtension\Dependency\Plugin\OauthScopeProviderPluginInterface> $scopeProviderPlugins
     * @param array<\Spryker\Glue\OauthExtension\Dependency\Plugin\ScopeFinderPluginInterface> $scopeFinderPlugins
     */
    public function __construct(
        AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository,
        array $scopeProviderPlugins = [],
        array $scopeFinderPlugins = []
    ) {
        $this->authorizationRepository = $authorizationRepository;
        $this->scopeProviderPlugins = $scopeProviderPlugins;
        $this->scopeFinderPlugins = $scopeFinderPlugins;
    }

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     * @param string|null $applicationName
     *
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface|null
     */
    public function getScopeEntityByIdentifier(string $identifier, ?string $applicationName = null): ?ScopeEntityInterface
    {
        foreach ($this->scopeFinderPlugins as $scopeFinderPlugin) {
            $oauthScopeFindTransfer = (new OauthScopeFindTransfer())->setIdentifier($identifier)->setApplicationName($applicationName);

            if ($scopeFinderPlugin->isServing($oauthScopeFindTransfer) && $scopeFinderPlugin->findScope($oauthScopeFindTransfer)) {
                return $this->createScopeEntity($identifier);
            }
        }

        $scopeEntityTransfer = $this->authorizationRepository->findScopeByIdentifier($identifier);
        if (!$scopeEntityTransfer) {
            return null;
        }

        return $this->createScopeEntity($identifier);
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     * @param string $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     * @param string|null $userIdentifier
     * @param string|null $applicationName
     *
     * @return array<\League\OAuth2\Server\Entities\ScopeEntityInterface>
     */
    public function finalizeScopes(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        ?string $userIdentifier = null,
        ?string $applicationName = null
    ): array {
        $oauthScopeRequestTransfer = $this->mapOauthScopeRequestTransfer($scopes, $grantType, $clientEntity, $userIdentifier, $applicationName);
        $providedScopes = $this->getProvidedScopes($oauthScopeRequestTransfer);

        return $this->mapScopeEntities($providedScopes);
    }

    /**
     * @param array $scopes
     * @param string $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     * @param string|null $userIdentifier
     * @param string|null $applicationName
     *
     * @return \Generated\Shared\Transfer\OauthScopeRequestTransfer
     */
    protected function mapOauthScopeRequestTransfer(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        ?string $userIdentifier = null,
        ?string $applicationName = null
    ): OauthScopeRequestTransfer {
        $oauthScopeRequestTransfer = (new OauthScopeRequestTransfer())
            ->setGrantType($grantType)
            ->setClientId($clientEntity->getIdentifier())
            ->setClientName($clientEntity->getName())
            ->setRequestApplication($applicationName);

        if ($userIdentifier) {
            $oauthScopeRequestTransfer->setUserIdentifier($userIdentifier);
        }

        foreach ($scopes as $scope) {
            $authScopeTransfer = new OauthScopeTransfer();
            $authScopeTransfer->setIdentifier($scope->getIdentifier());
            $oauthScopeRequestTransfer->addScope($authScopeTransfer);
        }

        return $oauthScopeRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthScopeRequestTransfer $oauthScopeRequestTransfer
     *
     * @return array<\Generated\Shared\Transfer\OauthScopeTransfer>
     */
    protected function getProvidedScopes(OauthScopeRequestTransfer $oauthScopeRequestTransfer): array
    {
        $providedScopes = [];
        foreach ($this->scopeProviderPlugins as $scopeProviderPlugin) {
            if (!$scopeProviderPlugin->accept($oauthScopeRequestTransfer)) {
                continue;
            }

            $providedScopes[] = $scopeProviderPlugin->getScopes($oauthScopeRequestTransfer);
        }

        return $providedScopes ? array_merge(...$providedScopes) : [];
    }

    /**
     * @param array<\Generated\Shared\Transfer\OauthScopeTransfer> $providedScopes
     *
     * @return array
     */
    protected function mapScopeEntities(array $providedScopes): array
    {
        $scopes = [];
        foreach ($providedScopes as $oauthScopeTransfer) {
            $scope = new ScopeEntity();
            $scope->setIdentifier($oauthScopeTransfer->getIdentifier());
            $scopes[$oauthScopeTransfer->getIdentifier()] = $scope;
        }

        return $scopes;
    }

    /**
     * @param string $scopeIdentifier
     *
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Entities\ScopeEntity
     */
    protected function createScopeEntity(string $scopeIdentifier): ScopeEntity
    {
        return (new ScopeEntity())->setIdentifier($scopeIdentifier);
    }
}

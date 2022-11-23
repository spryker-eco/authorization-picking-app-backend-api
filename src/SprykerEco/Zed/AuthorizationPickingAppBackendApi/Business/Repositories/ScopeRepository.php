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
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Entities\ScopeEntity;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders\ScopeFinderInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\ScopeProviderInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface
     */
    protected AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\ScopeProviderInterface
     */
    protected ScopeProviderInterface $scopeProvider;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders\ScopeFinderInterface
     */
    protected ScopeFinderInterface $scopeFinder;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\ScopeProviderInterface $scopeProvider
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders\ScopeFinderInterface $scopeFinder
     */
    public function __construct(
        AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository,
        ScopeProviderInterface $scopeProvider,
        ScopeFinderInterface $scopeFinder
    ) {
        $this->authorizationRepository = $authorizationRepository;
        $this->scopeProvider = $scopeProvider;
        $this->scopeFinder = $scopeFinder;
    }

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface|null
     */
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        $oauthScopeFindTransfer = (new OauthScopeFindTransfer())->setIdentifier($identifier);

        if ($this->scopeFinder->find($oauthScopeFindTransfer)) {
            return $this->createScopeEntity($identifier);
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
     *
     * @return array<\League\OAuth2\Server\Entities\ScopeEntityInterface>
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        $oauthScopeRequestTransfer = $this->mapOauthScopeRequestTransfer($scopes, $grantType, $clientEntity, $userIdentifier);
        $providedScopes = $this->scopeProvider->provide($oauthScopeRequestTransfer);

        return $this->mapScopeEntities($providedScopes);
    }

    /**
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     * @param string $grantType
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     * @param string|null $userIdentifier
     *
     * @return \Generated\Shared\Transfer\OauthScopeRequestTransfer
     */
    protected function mapOauthScopeRequestTransfer(
        array $scopes,
        string $grantType,
        ClientEntityInterface $clientEntity,
        ?string $userIdentifier = null
    ): OauthScopeRequestTransfer {
        $oauthScopeRequestTransfer = (new OauthScopeRequestTransfer())
            ->setGrantType($grantType)
            ->setClientId($clientEntity->getIdentifier())
            ->setClientName($clientEntity->getName());

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
     * @param array<\Generated\Shared\Transfer\OauthScopeTransfer> $providedScopes
     *
     * @return array<\League\OAuth2\Server\Entities\ScopeEntityInterface>
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
        $scopeEntity = new ScopeEntity();
        $scopeEntity->setIdentifier($scopeIdentifier);

        return $scopeEntity;
    }
}

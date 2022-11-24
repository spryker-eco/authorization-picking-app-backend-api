<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use DateTimeImmutable;
use Generated\Shared\Transfer\AuthCodeTransfer;
use Generated\Shared\Transfer\SpyOauthClientEntityTransfer;
use Generated\Shared\Transfer\SpyOauthScopeEntityTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiPersistenceFactory getFactory()
 */
class AuthorizationPickingAppBackendApiRepository extends AbstractRepository implements AuthorizationPickingAppBackendApiRepositoryInterface
{
    /**
     * @param string $identifier
     *
     * @return \Generated\Shared\Transfer\SpyOauthClientEntityTransfer|null
     */
    public function findClientByIdentifier(string $identifier): ?SpyOauthClientEntityTransfer
    {
        $query = $this->getFactory()
            ->getOauthClientPropelQuery() # OauthClientQuery was injected through DependencyProvider - spy_oauth_client is defined in another module and dependency resolved through DependencyProvider
            ->filterByIdentifier($identifier);

        return $this->buildQueryFromCriteria($query)->findOne();
    }

    /**
     * @param string $identifier
     *
     * @return \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer|null
     */
    public function findScopeByIdentifier(string $identifier): ?SpyOauthScopeEntityTransfer
    {
        $query = $this->getFactory()
            ->getOauthScopePropelQuery() # OauthScopeQuery was injected through DependencyProvider - spy_oauth_scope is defined in another module and dependency resolved through DependencyProvider
            ->filterByIdentifier($identifier);

        return $this->buildQueryFromCriteria($query)->findOne();
    }

    /**
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer|null
     */
    public function findAuthCode(ClientEntityInterface $client, array $scopes = []): ?AuthCodeTransfer
    {
        $scopeIdentifiers = [];
        foreach ($scopes as $scope) {
            $scopeIdentifiers[] = $scope->getIdentifier();
        }

        $scopes = sprintf('["%s"]', implode('", "', $scopeIdentifiers));

        $authCodeEntity = $this->getFactory()
            ->getOauthAuthCodePropelQuery() # OauthAuthCodeQuery was injected through DependencyProvider - spy_oauth_auth_code is defined in another module and dependency resolved through DependencyProvider
            ->filterByFkOauthClient($client->getIdentifier())
            ->filterByExpirityDate(['min' => new DateTimeImmutable('now')], Criteria::GREATER_EQUAL)
            ->filterByScopes($scopes)
            ->orderByIdOauthAuthCode(Criteria::DESC)
            ->findOne();

        if ($authCodeEntity === null) {
            return null;
        }

        return $this->getFactory()->createAuthCodeMapper()->mapAuthCodeEntityToAuthCodeTransfer($authCodeEntity, new AuthCodeTransfer());
    }
}

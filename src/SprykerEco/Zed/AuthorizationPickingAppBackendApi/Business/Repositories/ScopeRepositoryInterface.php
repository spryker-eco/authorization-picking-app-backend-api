<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface as LeagueScopeRepositoryInterface;

interface ScopeRepositoryInterface extends LeagueScopeRepositoryInterface
{
    /**
     * @param string $identifier The scope identifier
     * @param string|null $applicationName
     *
     * @return \League\OAuth2\Server\Entities\ScopeEntityInterface|null
     */
    public function getScopeEntityByIdentifier(string $identifier, ?string $applicationName = null): ?ScopeEntityInterface;

    /**
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
    ): array;
}

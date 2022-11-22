<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Generated\Shared\Transfer\AuthCodeTransfer;
use Generated\Shared\Transfer\SpyOauthClientEntityTransfer;
use Generated\Shared\Transfer\SpyOauthScopeEntityTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiPersistenceFactory getFactory()
 */
interface AuthorizationPickingAppBackendApiRepositoryInterface
{
    /**
     * @param string $identifier
     *
     * @return \Generated\Shared\Transfer\SpyOauthClientEntityTransfer|null
     */
    public function findClientByIdentifier(string $identifier): ?SpyOauthClientEntityTransfer;

    /**
     * @param string $identifier
     *
     * @return \Generated\Shared\Transfer\SpyOauthScopeEntityTransfer|null
     */
    public function findScopeByIdentifier(string $identifier): ?SpyOauthScopeEntityTransfer;

    /**
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer|null
     */
    public function findAuthCode(ClientEntityInterface $client, array $scopes = []): ?AuthCodeTransfer;

    /**
     * @param string $codeId
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer|null
     */
    public function findAuthCodeByCodeId(string $codeId): ?AuthCodeTransfer;
}

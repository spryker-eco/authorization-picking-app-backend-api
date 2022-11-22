<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories;

use Generated\Shared\Transfer\AuthCodeTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface as LeagueAuthCodeRepositoryInterface;

interface AuthCodeRepositoryInterface extends LeagueAuthCodeRepositoryInterface
{
    /**
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer|null
     */
    public function findAuthCode(ClientEntityInterface $client, array $scopes = []): ?AuthCodeTransfer;
}

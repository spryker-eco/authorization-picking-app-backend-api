<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories;

use Generated\Shared\Transfer\SpyOauthClientEntityTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Entities\ClientEntity;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface
     */
    protected AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository;

    /**
     * @var array<\Generated\Shared\Transfer\SpyOauthClientEntityTransfer|null>
     */
    protected static $authorizationClientEntityTransferCache = [];

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository
     */
    public function __construct(AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository)
    {
        $this->authorizationRepository = $authorizationRepository;
    }

    /**
     * @param string $clientIdentifier The client's identifier
     *
     * @return \League\OAuth2\Server\Entities\ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $oauthClientEntityTransfer = $this->findClientEntityTransfer($clientIdentifier);

        if (!$oauthClientEntityTransfer) {
            return null;
        }

        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($oauthClientEntityTransfer->getIdentifier());
        $clientEntity->setName($oauthClientEntityTransfer->getName());
        $clientEntity->setRedirectUri($oauthClientEntityTransfer->getRedirectUri());

        return $clientEntity;
    }

    /**
     * @param string $clientIdentifier The client's identifier
     * @param string|null $clientSecret The client's secret (if sent)
     * @param string|null $grantType The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $oauthClientEntityTransfer = $this->findClientEntityTransfer($clientIdentifier);

        if (!$oauthClientEntityTransfer) {
            return false;
        }

        return true;
    }

    /**
     * @param string $clientIdentifier The client's identifier
     *
     * @return \Generated\Shared\Transfer\SpyOauthClientEntityTransfer|null
     */
    protected function findClientEntityTransfer(string $clientIdentifier): ?SpyOauthClientEntityTransfer
    {
        if (!isset(static::$authorizationClientEntityTransferCache[$clientIdentifier])) {
            static::$authorizationClientEntityTransferCache[$clientIdentifier] = $this->authorizationRepository
                ->findClientByIdentifier($clientIdentifier);
        }

        return static::$authorizationClientEntityTransferCache[$clientIdentifier];
    }
}

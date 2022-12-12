<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories;

use Generated\Shared\Transfer\OauthUserTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Entities\UserEntity;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\UserProviderInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\UserProviderInterface
     */
    protected UserProviderInterface $userProvider;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers\UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $grantType The grant type used
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     *
     * @return \League\OAuth2\Server\Entities\UserEntityInterface|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        $oauthUserTransfer = $this->createOauthUserTransfer($username, $password, $clientEntity);
        $oauthUserTransfer = $this->findUser($oauthUserTransfer);

        if ($oauthUserTransfer && $oauthUserTransfer->getIsSuccess() && $oauthUserTransfer->getUserIdentifier()) {
            return new UserEntity($oauthUserTransfer->getUserIdentifier());
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthUserTransfer $oauthUserTransfer
     *
     * @return \Generated\Shared\Transfer\OauthUserTransfer|null
     */
    protected function findUser(OauthUserTransfer $oauthUserTransfer): ?OauthUserTransfer
    {
        $oauthUserTransfer = $this->userProvider->provide($oauthUserTransfer);

        if ($oauthUserTransfer->getIsSuccess()) {
            return $oauthUserTransfer;
        }

        return null;
    }

    /**
     * @param string $username
     * @param string $password
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     *
     * @return \Generated\Shared\Transfer\OauthUserTransfer
     */
    protected function createOauthUserTransfer(
        string $username,
        string $password,
        ClientEntityInterface $clientEntity
    ): OauthUserTransfer {
        $oauthUserTransfer = new OauthUserTransfer();
        $oauthUserTransfer
            ->setIsSuccess(false)
            ->setUsername($username)
            ->setPassword($password)
            ->setClientId($clientEntity->getIdentifier())
            ->setClientName($clientEntity->getName());

        return $oauthUserTransfer;
    }
}

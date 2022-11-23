<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;

class AuthorizationPickingAppBackendApiToUserFacadeBridge implements AuthorizationPickingAppBackendApiToUserFacadeInterface
{
    /**
     * @var \Spryker\Zed\User\Business\UserFacadeInterface
     */
    protected $userFacade;

    /**
     * @param \Spryker\Zed\User\Business\UserFacadeInterface $userFacade
     */
    public function __construct($userFacade)
    {
        $this->userFacade = $userFacade;
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    public function hasActiveUserByUsername(string $username): bool
    {
        return $this->userFacade->hasActiveUserByUsername($username);
    }

    /**
     * @param \Generated\Shared\Transfer\UserCriteriaTransfer $userCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    public function findUser(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        return $this->userFacade->findUser($userCriteriaTransfer);
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function isValidPassword(string $password, string $hash): bool
    {
        return $this->userFacade->isValidPassword($password, $hash);
    }
}

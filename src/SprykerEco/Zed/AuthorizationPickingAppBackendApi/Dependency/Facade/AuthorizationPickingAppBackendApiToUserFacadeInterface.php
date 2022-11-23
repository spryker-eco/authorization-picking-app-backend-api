<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface AuthorizationPickingAppBackendApiToUserFacadeInterface
{
    /**
     * @param string $username
     *
     * @return bool
     */
    public function hasActiveUserByUsername(string $username): bool;

    /**
     * @param \Generated\Shared\Transfer\UserCriteriaTransfer $userCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    public function findUser(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer;

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function isValidPassword(string $password, string $hash): bool;
}

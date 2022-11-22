<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer;

interface AuthorizationPickingAppBackendApiEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer $oauthAuthCodeEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer
     */
    public function saveCode(SpyOauthAuthCodeEntityTransfer $oauthAuthCodeEntityTransfer): SpyOauthAuthCodeEntityTransfer;
}

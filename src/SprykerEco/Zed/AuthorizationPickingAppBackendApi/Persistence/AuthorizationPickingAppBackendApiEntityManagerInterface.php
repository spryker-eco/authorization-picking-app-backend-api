<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer;

interface AuthorizationPickingAppBackendApiEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer $oauthCodeFlowAuthCodeEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer
     */
    public function saveCode(SpyOauthCodeFlowAuthCodeEntityTransfer $oauthCodeFlowAuthCodeEntityTransfer): SpyOauthCodeFlowAuthCodeEntityTransfer;
}

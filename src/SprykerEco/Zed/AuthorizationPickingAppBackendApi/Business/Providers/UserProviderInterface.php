<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers;

use Generated\Shared\Transfer\OauthUserTransfer;

interface UserProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OauthUserTransfer $oauthUserTransfer
     *
     * @return \Generated\Shared\Transfer\OauthUserTransfer
     */
    public function provide(OauthUserTransfer $oauthUserTransfer): OauthUserTransfer;
}

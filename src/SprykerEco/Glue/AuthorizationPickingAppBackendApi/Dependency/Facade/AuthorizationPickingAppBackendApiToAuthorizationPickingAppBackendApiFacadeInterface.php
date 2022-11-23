<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Glue\AuthorizationPickingAppBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\OauthRequestTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;

interface AuthorizationPickingAppBackendApiToAuthorizationPickingAppBackendApiFacadeInterface
{
    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    public function authorize(
        OauthRequestTransfer $oauthRequestTransfer
    ): OauthResponseTransfer;
}

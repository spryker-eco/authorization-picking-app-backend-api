<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Glue\AuthorizationPickingAppBackendApi\Dependency\Facade;

use Generated\Shared\Transfer\OauthRequestTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;

class AuthorizationPickingAppBackendApiToAuthorizationPickingAppBackendApiFacadeBridge implements AuthorizationPickingAppBackendApiToAuthorizationPickingAppBackendApiFacadeInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\AuthorizationPickingAppBackendApiFacadeInterface
     */
    protected $authorizationPickingAppBackendApiFacade;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\AuthorizationPickingAppBackendApiFacadeInterface $authorizationPickingAppBackendApiFacade
     */
    public function __construct($authorizationPickingAppBackendApiFacade)
    {
        $this->authorizationPickingAppBackendApiFacade = $authorizationPickingAppBackendApiFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    public function authorize(
        OauthRequestTransfer $oauthRequestTransfer
    ): OauthResponseTransfer {
        return $this->authorizationPickingAppBackendApiFacade->authorize($oauthRequestTransfer);
    }
}

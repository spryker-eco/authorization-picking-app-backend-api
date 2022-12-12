<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business;

use Generated\Shared\Transfer\OauthRequestTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;

interface AuthorizationPickingAppBackendApiFacadeInterface
{
    /**
     * Specification:
     * - Process an authorization code request.
     * - Requires `OauthRequestTransfer.responseType` to be provided.
     * - Requires `OauthRequestTransfer.username` to be provided.
     * - Requires `OauthRequestTransfer.password` to be provided.
     * - Requires `OauthRequestTransfer.clientId` to be provided.
     * - Requires `OauthRequestTransfer.codeChallenge`, `OauthRequestTransfer.codeChallengeMethod` to be provided for PKCE checks. (Configurable)
     * - Saves issued authorization code in database for auditing.
     * - Returns `OauthRequestTransfer` expanded with a new authorization code when the authorization request is valid.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    public function authorize(OauthRequestTransfer $oauthRequestTransfer): OauthResponseTransfer;
}

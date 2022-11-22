<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator;

use Generated\Shared\Transfer\OauthRequestTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

interface UserValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     *
     * @return \League\OAuth2\Server\Entities\UserEntityInterface
     */
    public function validate(OauthRequestTransfer $oauthRequestTransfer, ClientEntityInterface $client): UserEntityInterface;
}

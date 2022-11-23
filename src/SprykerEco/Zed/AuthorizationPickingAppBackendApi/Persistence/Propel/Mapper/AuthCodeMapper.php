<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\AuthCodeTransfer;
use Orm\Zed\Oauth\Persistence\SpyOauthAuthCode;

class AuthCodeMapper
{
    /**
     * @param \Orm\Zed\Oauth\Persistence\SpyOauthAuthCode $authCodeEntity
     * @param \Generated\Shared\Transfer\AuthCodeTransfer $authCodeTransfer
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer
     */
    public function mapAuthCodeEntityToAuthCodeTransfer(
        SpyOauthAuthCode $authCodeEntity,
        AuthCodeTransfer $authCodeTransfer
    ): AuthCodeTransfer {
        $authCodeTransfer->fromArray($authCodeEntity->toArray(), true);

        /** @var string $expirityDate */
        $expirityDate = $authCodeEntity->getExpirityDate();
        $authCodeTransfer->setExpiresAt($expirityDate);

        return $authCodeTransfer;
    }
}

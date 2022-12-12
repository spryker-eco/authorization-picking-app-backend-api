<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\AuthCodeTransfer;
use Orm\Zed\OauthCodeFlow\Persistence\SpyOauthCodeFlowAuthCode;

class AuthCodeMapper
{
    /**
     * @param \Orm\Zed\OauthCodeFlow\Persistence\SpyOauthCodeFlowAuthCode $authCodeEntity
     * @param \Generated\Shared\Transfer\AuthCodeTransfer $authCodeTransfer
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer
     */
    public function mapAuthCodeEntityToAuthCodeTransfer(
        SpyOauthCodeFlowAuthCode $authCodeEntity,
        AuthCodeTransfer $authCodeTransfer
    ): AuthCodeTransfer {
        $authCodeTransfer->fromArray($authCodeEntity->toArray(), true);

        /** @var string $expirityDate */
        $expirityDate = $authCodeEntity->getExpirityDate();
        $authCodeTransfer->setExpiresAt($expirityDate);

        return $authCodeTransfer;
    }
}

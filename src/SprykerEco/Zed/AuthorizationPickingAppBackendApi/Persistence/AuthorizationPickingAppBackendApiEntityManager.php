<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiPersistenceFactory getFactory()
 */
class AuthorizationPickingAppBackendApiEntityManager extends AbstractEntityManager implements AuthorizationPickingAppBackendApiEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer $oauthAuthCodeEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer
     */
    public function saveCode(SpyOauthAuthCodeEntityTransfer $oauthAuthCodeEntityTransfer): SpyOauthAuthCodeEntityTransfer
    {
        /** @var \Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer $oauthAuthCodeEntityTransfer */
        $oauthAuthCodeEntityTransfer = $this->save($oauthAuthCodeEntityTransfer);

        return $oauthAuthCodeEntityTransfer;
    }
}

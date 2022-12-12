<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiPersistenceFactory getFactory()
 */
class AuthorizationPickingAppBackendApiEntityManager extends AbstractEntityManager implements AuthorizationPickingAppBackendApiEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer $oauthCodeFlowAuthCodeEntityTransfer
     *
     * @return \Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer
     */
    public function saveCode(SpyOauthCodeFlowAuthCodeEntityTransfer $oauthCodeFlowAuthCodeEntityTransfer): SpyOauthCodeFlowAuthCodeEntityTransfer
    {
        /** @var \Generated\Shared\Transfer\SpyOauthCodeFlowAuthCodeEntityTransfer $oauthCodeFlowAuthCodeEntityTransfer */
        $oauthCodeFlowAuthCodeEntityTransfer = $this->save($oauthCodeFlowAuthCodeEntityTransfer);

        return $oauthCodeFlowAuthCodeEntityTransfer;
    }
}

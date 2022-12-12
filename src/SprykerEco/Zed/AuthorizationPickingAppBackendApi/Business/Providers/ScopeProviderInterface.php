<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers;

use Generated\Shared\Transfer\OauthScopeRequestTransfer;

interface ScopeProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OauthScopeRequestTransfer $oauthScopeRequestTransfer
     *
     * @return array<\Generated\Shared\Transfer\OauthScopeTransfer>
     */
    public function provide(OauthScopeRequestTransfer $oauthScopeRequestTransfer): array;
}

<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders;

use Generated\Shared\Transfer\OauthScopeFindTransfer;

interface ScopeFinderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OauthScopeFindTransfer $oauthScopeFindTransfer
     *
     * @return string|null
     */
    public function find(OauthScopeFindTransfer $oauthScopeFindTransfer): ?string;
}

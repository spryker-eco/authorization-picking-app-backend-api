<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class AuthorizationPickingAppBackendApiConfig extends AbstractBundleConfig
{
    /**
     * Specification:
     * - Sets the interval for how long is the auth code is valid, this will be feed to \DateTime object.
     *
     * @api
     *
     * @return string
     */
    public function getAuthCodeTTL(): string
    {
        return 'PT10M';
    }
}

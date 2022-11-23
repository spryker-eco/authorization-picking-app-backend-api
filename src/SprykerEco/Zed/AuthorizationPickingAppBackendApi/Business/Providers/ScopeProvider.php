<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers;

use Generated\Shared\Transfer\OauthScopeRequestTransfer;
use Generated\Shared\Transfer\OauthScopeTransfer;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig;

class ScopeProvider implements ScopeProviderInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig
     */
    protected AuthorizationPickingAppBackendApiConfig $config;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig $config
     */
    public function __construct(AuthorizationPickingAppBackendApiConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthScopeRequestTransfer $oauthScopeRequestTransfer
     *
     * @return array<\Generated\Shared\Transfer\OauthScopeTransfer>
     */
    public function provide(OauthScopeRequestTransfer $oauthScopeRequestTransfer): array
    {
        $scopes = $oauthScopeRequestTransfer->getDefaultScopes()->getArrayCopy();

        foreach ($this->config->getUserScopes() as $scope) {
            $oauthScopeTransfer = new OauthScopeTransfer();
            $oauthScopeTransfer->setIdentifier($scope);
            $scopes[] = $oauthScopeTransfer;
        }

        return $scopes;
    }
}

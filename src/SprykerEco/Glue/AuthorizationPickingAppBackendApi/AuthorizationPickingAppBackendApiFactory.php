<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Glue\AuthorizationPickingAppBackendApi;

use Spryker\Glue\Kernel\Backend\AbstractBackendApiFactory;
use SprykerEco\Glue\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToAuthorizationPickingAppBackendApiFacadeInterface;

/**
 * @method \SprykerEco\Glue\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig getConfig()
 */
class AuthorizationPickingAppBackendApiFactory extends AbstractBackendApiFactory
{
    /**
     * @return \SprykerEco\Glue\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToAuthorizationPickingAppBackendApiFacadeInterface
     */
    public function getAuthorizationPickingAppBackendApiFacade(): AuthorizationPickingAppBackendApiToAuthorizationPickingAppBackendApiFacadeInterface
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::FACADE_AUTHORIZATION_PICKING_APP_BACKEND_API);
    }
}

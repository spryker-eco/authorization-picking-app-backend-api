<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Glue\AuthorizationPickingAppBackendApi;

use Spryker\Glue\Kernel\Backend\AbstractBackendApiFactory;

/**
 * @method \SprykerEco\Glue\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig getConfig()
 */
class AuthorizationPickingAppBackendApiFactory extends AbstractBackendApiFactory
{
    /**
     * @return void
     */
    public function createAuthCodeProcessor(): void
    {
    }

//    /**
//     * @return \Spryker\Glue\OauthBackendApi\Dependency\Facade\OauthBackendApiToAuthenticationFacadeInterface
//     */
//    public function getAuthenticationFacade(): OauthBackendApiToAuthenticationFacadeInterface
//    {
//        return $this->getProvidedDependency(OauthBackendApiDependencyProvider::FACADE_AUTHENTICATION);
//    }
//
//    /**
//     * @return \Spryker\Glue\OauthBackendApi\Dependency\Service\OauthBackendApiToOauthServiceInterface
//     */
//    public function getOauthService(): OauthBackendApiToOauthServiceInterface
//    {
//        return $this->getProvidedDependency(OauthBackendApiDependencyProvider::SERVICE_OAUTH);
//    }
//
//    /**
//     * @return \Spryker\Glue\OauthBackendApi\Dependency\Service\OauthBackendApiToUtilEncodingServiceInterface
//     */
//    public function getUtilEncodingService(): OauthBackendApiToUtilEncodingServiceInterface
//    {
//        return $this->getProvidedDependency(OauthBackendApiDependencyProvider::SERVICE_UTIL_ENCODING);
//    }
//
//    /**
//     * @return \Spryker\Glue\OauthBackendApi\Dependency\Facade\OauthBackendApiToOauthFacadeInterface
//     */
//    public function getOauthFacade(): OauthBackendApiToOauthFacadeInterface
//    {
//        return $this->getProvidedDependency(OauthBackendApiDependencyProvider::FACADE_OAUTH);
//    }
}

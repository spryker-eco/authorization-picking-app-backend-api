<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceBridge;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig getConfig()
 */
class AuthorizationPickingAppBackendApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addUtilEncodingService($container);

//        $container = $this->addOauthUserProviderPlugins($container);
//        $container = $this->addScopeProviderPlugins($container);
//        $container = $this->addOauthUserIdentifierFilterPlugins($container);
//        $container = $this->addScopeFinderPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new AuthorizationPickingAppBackendApiToUtilEncodingServiceBridge(
                $container->getLocator()->utilEncoding()->service(),
            );
        });

        return $container;
    }

//    /**
//     * @param \Spryker\Zed\Kernel\Container $container
//     *
//     * @return \Spryker\Zed\Kernel\Container
//     */
//    protected function addOauthUserProviderPlugins(Container $container): Container
//    {
//        $container->set(static::PLUGINS_OAUTH_USER_PROVIDER, function (Container $container) {
//            return $this->getOauthUserProviderPlugins();
//        });
//
//        return $container;
//    }
//
//    /**
//     * @param \Spryker\Zed\Kernel\Container $container
//     *
//     * @return \Spryker\Zed\Kernel\Container
//     */
//    protected function addScopeProviderPlugins(Container $container): Container
//    {
//        $container->set(static::PLUGIN_SCOPE_PROVIDER, function (Container $container) {
//            return $this->getScopeProviderPlugins();
//        });
//
//        return $container;
//    }
//
//    /**
//     * @param \Spryker\Zed\Kernel\Container $container
//     *
//     * @return \Spryker\Zed\Kernel\Container
//     */
//    protected function addScopeFinderPlugins(Container $container): Container
//    {
//        $container->set(static::PLUGINS_SCOPE_FINDER, function (Container $container) {
//            return $this->getScopeFinderPlugins();
//        });
//
//        return $container;
//    }
//
//    /**
//     * @param \Spryker\Zed\Kernel\Container $container
//     *
//     * @return \Spryker\Zed\Kernel\Container
//     */
//    protected function addOauthUserIdentifierFilterPlugins(Container $container): Container
//    {
//        $container->set(static::PLUGINS_OAUTH_USER_IDENTIFIER_FILTER, function () {
//            return $this->getOauthUserIdentifierFilterPlugins();
//        });
//
//        return $container;
//    }
//
//    /**
//     * @return array<\Spryker\Zed\OauthExtension\Dependency\Plugin\OauthUserProviderPluginInterface>
//     */
//    protected function getUserProviderPlugins(): array
//    {
//        return [];
//    }
//
//    /**
//     * @return array<\Spryker\Zed\OauthExtension\Dependency\Plugin\OauthUserProviderPluginInterface>
//     */
//    protected function getOauthUserProviderPlugins(): array
//    {
//        return [];
//    }
//
//    /**
//     * @return array<\Spryker\Zed\OauthExtension\Dependency\Plugin\OauthScopeProviderPluginInterface>
//     */
//    protected function getScopeProviderPlugins(): array
//    {
//        return [];
//    }
//
//    /**
//     * @return array<\Spryker\Glue\OauthExtension\Dependency\Plugin\ScopeFinderPluginInterface>
//     */
//    protected function getScopeFinderPlugins(): array
//    {
//        return [];
//    }
}

<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi;

use Orm\Zed\Oauth\Persistence\SpyOauthAuthCodeQuery;
use Orm\Zed\Oauth\Persistence\SpyOauthClientQuery;
use Orm\Zed\Oauth\Persistence\SpyOauthScopeQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External\AuthorizationPickingAppBackendApiToYamlAdapter;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToUserFacadeBridge;
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
     * @var string
     */
    public const ADAPTER_YAML = 'ADAPTER_YAML';

    /**
     * @var string
     */
    public const FACADE_USER = 'FACADE_USER';

    /**
     * @var string
     */
    public const PROPEL_QUERY_OAUTH_AUTH_CODE = 'PROPEL_QUERY_OAUTH_AUTH_CODE';

    /**
     * @var string
     */
    public const PROPEL_QUERY_OAUTH_CLIENT = 'PROPEL_QUERY_OAUTH_CLIENT';

    /**
     * @var string
     */
    public const PROPEL_QUERY_OAUTH_SCOPE = 'PROPEL_QUERY_OAUTH_SCOPE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addYamlAdapter($container);
        $container = $this->addUserFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);
        $container = $this->addOauthAuthCodePropelQuery($container);
        $container = $this->addOauthClientPropelQuery($container);
        $container = $this->addOauthScopePropelQuery($container);

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

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addYamlAdapter(Container $container): Container
    {
        $container->set(static::ADAPTER_YAML, function () {
            return new AuthorizationPickingAppBackendApiToYamlAdapter();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUserFacade(Container $container): Container
    {
        $container->set(static::FACADE_USER, function (Container $container) {
            return new AuthorizationPickingAppBackendApiToUserFacadeBridge($container->getLocator()->user()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOauthAuthCodePropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_OAUTH_AUTH_CODE, $container->factory(function () {
            return SpyOauthAuthCodeQuery::create();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOauthClientPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_OAUTH_CLIENT, $container->factory(function () {
            return SpyOauthClientQuery::create();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOauthScopePropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_OAUTH_SCOPE, $container->factory(function () {
            return SpyOauthScopeQuery::create();
        }));

        return $container;
    }
}

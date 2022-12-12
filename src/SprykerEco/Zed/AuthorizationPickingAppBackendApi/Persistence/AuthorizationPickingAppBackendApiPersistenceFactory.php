<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Orm\Zed\Oauth\Persistence\SpyOauthClientQuery;
use Orm\Zed\Oauth\Persistence\SpyOauthScopeQuery;
use Orm\Zed\OauthCodeFlow\Persistence\SpyOauthCodeFlowAuthCodeQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiDependencyProvider;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\Propel\Mapper\AuthCodeMapper;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig getConfig()
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface getRepository()
 */
class AuthorizationPickingAppBackendApiPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\OauthCodeFlow\Persistence\SpyOauthCodeFlowAuthCodeQuery
     */
    public function getOauthCodeFlowAuthCodePropelQuery(): SpyOauthCodeFlowAuthCodeQuery
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::PROPEL_QUERY_OAUTH_CODE_FLOW_AUTH_CODE);
    }

    /**
     * @return \Orm\Zed\Oauth\Persistence\SpyOauthClientQuery
     */
    public function getOauthClientPropelQuery(): SpyOauthClientQuery
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::PROPEL_QUERY_OAUTH_CLIENT);
    }

    /**
     * @return \Orm\Zed\Oauth\Persistence\SpyOauthScopeQuery
     */
    public function getOauthScopePropelQuery(): SpyOauthScopeQuery
    {
        return $this->getProvidedDependency(AuthorizationPickingAppBackendApiDependencyProvider::PROPEL_QUERY_OAUTH_SCOPE);
    }

    /**
     * @return \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\Propel\Mapper\AuthCodeMapper
     */
    public function createAuthCodeMapper(): AuthCodeMapper
    {
        return new AuthCodeMapper();
    }
}

<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence;

use Orm\Zed\Oauth\Persistence\SpyOauthAuthCodeQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

//use Spryker\Zed\Oauth\Persistence\Propel\Mapper\AuthCodeMapper;

/**
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig getConfig()
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface getRepository()
 */
class AuthorizationPickingAppBackendApiPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Oauth\Persistence\SpyOauthAuthCodeQuery
     */
    public function createAuthCodeQuery(): SpyOauthAuthCodeQuery
    {
        return SpyOauthAuthCodeQuery::create();
    }

//    /**
//     * @return \Spryker\Zed\Oauth\Persistence\Propel\Mapper\AuthCodeMapper
//     */
//    public function createAuthCodeMapper(): AuthCodeMapper
//    {
//        return new AuthCodeMapper();
//    }
}

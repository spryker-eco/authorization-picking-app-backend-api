<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerEcoTest\Zed\AuthorizationPickingAppBackendApi;

use Codeception\Actor;
use Generated\Shared\Transfer\OauthRequestTransfer;
use Orm\Zed\Oauth\Persistence\SpyOauthClientQuery;
use Orm\Zed\User\Persistence\SpyUserQuery;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class AuthorizationPickingAppBackendApiBusinessTester extends Actor
{
    use _generated\AuthorizationPickingAppBackendApiBusinessTesterActions;

    /**
     * @var string
     */
    protected const CLIENT_NAME = 'CLIENT_NAME';

    /**
     * @var string
     */
    protected const CLIENT_IDENTIFIER = 'CLIENT_IDENTIFIER';

    /**
     * @var string
     */
    protected const CLIENT_SECRET = 'CLIENT_SECRET';

    /**
     * @var string
     */
    protected const USERNAME = 'harald@spryker.com';

    /**
     * @var string
     */
    protected const PASSWORD = 'change123';

    /**
     * @var string
     */
    protected const CODE_CHALLENGE = 'vKAUt4sQg64ke2r7SXWre-ubGtIQPbiHqkkvN83qD8E';

    /**
     * @var string
     */
    protected const CODE_CHALLENGE_METHOD = 'S256';

    /**
     * @var string
     */
    protected const RESPONSE_TYPE_CODE = 'code';

    /**
     * @param string $username
     *
     * @return \Generated\Shared\Transfer\OauthRequestTransfer
     */
    public function createOauthRequestTransfer(string $username): OauthRequestTransfer
    {
        $oauthRequestTransfer = new OauthRequestTransfer();
        $oauthRequestTransfer
            ->setClientId(static::CLIENT_IDENTIFIER)
            ->setClientSecret(static::CLIENT_SECRET)
            ->setUsername($username)
            ->setPassword(static::PASSWORD)
            ->setResponseType(static::RESPONSE_TYPE_CODE)
            ->setCodeChallenge(static::CODE_CHALLENGE)
            ->setCodeChallengeMethod(static::CODE_CHALLENGE_METHOD);

        return $oauthRequestTransfer;
    }

    /**
     * @return void
     */
    public function createTestClient(): void
    {
        $oauthClientEntity = SpyOauthClientQuery::create()
            ->filterByIdentifier(static::CLIENT_IDENTIFIER)
            ->findOneOrCreate();

        $oauthClientEntity
            ->setName(static::CLIENT_NAME)
            ->setSecret(password_hash(static::CLIENT_SECRET, PASSWORD_BCRYPT))
            ->setIsConfidential(false)
            ->setRedirectUri('/')
            ->save();
    }

    /**
     * @return void
     */
    public function createTestUser(): void
    {
        $userEntity = SpyUserQuery::create()
            ->filterByUsername(static::USERNAME)
            ->findOneOrCreate();

        $userEntity
            ->setUsername(static::USERNAME)
            ->setPassword(password_hash(static::PASSWORD, PASSWORD_BCRYPT))
            ->setLastName('Schmidt')
            ->setFirstName('Harald')
            ->save();
    }
}

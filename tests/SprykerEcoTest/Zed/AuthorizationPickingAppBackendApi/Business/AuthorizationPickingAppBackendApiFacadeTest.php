<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AuthorizationPickingAppBackendApi;

use Codeception\Test\Unit;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group AuthorizationPickingAppBackendApi
 * @group Business
 * @group AuthorizationPickingAppBackendApiFacadeTest
 * Add your own group annotations below this line
 */
class AuthorizationPickingAppBackendApiFacadeTest extends Unit
{
    /**
     * @var \SprykerEcoTest\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiBusinessTester
     */
    protected $tester;

    /**
     * @var string
     */
    protected const USERNAME = 'harald@spryker.com';

    /**
     * @var string
     */
    protected const USERNAME_INVALID = 'fake@spryker.com';

    /**
     * @return void
     */
    public function testAuthorizeShouldReturnSuccessWhenValid(): void
    {
        //Arrange
        $this->tester->createTestClient();
        $this->tester->createTestUser();
        $oauthRequestTransfer = $this->tester->createOauthRequestTransfer(
            static::USERNAME,
        );

        //Act
        $oauthResponseTransfer = $this->tester
            ->getFacade()
            ->authorize($oauthRequestTransfer);

        //Assert
        $this->assertTrue($oauthResponseTransfer->getIsValid());
    }

    /**
     * @return void
     */
    public function testAuthorizeShouldReturnFailedWhenInvalid(): void
    {
        //Arrange
        $this->tester->createTestClient();
        $this->tester->createTestUser();
        $oauthRequestTransfer = $this->tester->createOauthRequestTransfer(
            static::USERNAME_INVALID,
        );

        //Act
        $oauthResponseTransfer = $this->tester
            ->getFacade()
            ->authorize($oauthRequestTransfer);

        //Assert
        $this->assertFalse($oauthResponseTransfer->getIsValid());
    }
}

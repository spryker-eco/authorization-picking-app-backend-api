<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Providers;

use Generated\Shared\Transfer\OauthUserTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserIdentifierTransfer;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToUserFacadeInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToUserFacadeInterface
     */
    protected AuthorizationPickingAppBackendApiToUserFacadeInterface $userFacade;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface
     */
    protected AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Facade\AuthorizationPickingAppBackendApiToUserFacadeInterface $userFacade
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        AuthorizationPickingAppBackendApiToUserFacadeInterface $userFacade,
        AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface $utilEncodingService
    ) {
        $this->userFacade = $userFacade;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthUserTransfer $oauthUserTransfer
     *
     * @return \Generated\Shared\Transfer\OauthUserTransfer
     */
    public function provide(OauthUserTransfer $oauthUserTransfer): OauthUserTransfer
    {
        if (!$this->userFacade->hasActiveUserByUsername($oauthUserTransfer->getUsernameOrFail())) {
            return $oauthUserTransfer;
        }

        $userCriteriaTransfer = (new UserCriteriaTransfer())
            ->setEmail($oauthUserTransfer->getUsername());

        $userTransfer = $this->userFacade->findUser($userCriteriaTransfer);
        if (!$userTransfer) {
            return $oauthUserTransfer;
        }

        $isValidPassword = $this->userFacade->isValidPassword($oauthUserTransfer->getPasswordOrFail(), $userTransfer->getPasswordOrFail());
        if (!$isValidPassword) {
            return $oauthUserTransfer->setIsSuccess(false);
        }

        $userIdentifierTransfer = (new UserIdentifierTransfer())->fromArray($userTransfer->toArray(), true);

        return $oauthUserTransfer
            ->setUserIdentifier($this->utilEncodingService->encodeJson($userIdentifierTransfer->toArray()))
            ->setIsSuccess(true);
    }
}

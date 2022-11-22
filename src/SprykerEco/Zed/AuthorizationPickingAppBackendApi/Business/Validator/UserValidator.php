<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator;

use Generated\Shared\Transfer\OauthRequestTransfer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserValidator implements UserValidatorInterface
{
    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_USERNAME = 'username';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_PASSWORD = 'password';

    /**
     * @var \League\OAuth2\Server\Repositories\UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @param \League\OAuth2\Server\Repositories\UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     *
     * @return \League\OAuth2\Server\Entities\UserEntityInterface
     */
    public function validate(OauthRequestTransfer $oauthRequestTransfer, ClientEntityInterface $client): UserEntityInterface
    {
        $username = $oauthRequestTransfer->getUsername();

        if ($username === null) {
            throw OAuthServerException::invalidRequest(static::REQUEST_PARAMETER_USERNAME);
        }

        $password = $oauthRequestTransfer->getPassword();

        if ($password === null) {
            throw OAuthServerException::invalidRequest(static::REQUEST_PARAMETER_PASSWORD);
        }

        $userEntity = $this->userRepository->getUserEntityByUserCredentials(
            $username,
            $password,
            //            $this->getIdentifier(),
            $client,
        );

        if (!($userEntity instanceof UserEntityInterface)) {
            throw OAuthServerException::invalidCredentials();
        }

        return $userEntity;
    }
}

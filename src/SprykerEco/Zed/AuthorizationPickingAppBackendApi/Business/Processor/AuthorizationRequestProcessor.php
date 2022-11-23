<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Processor;

use Generated\Shared\Transfer\OauthErrorTransfer;
use Generated\Shared\Transfer\OauthRequestTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ResponseInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator\UserValidatorInterface;

class AuthorizationRequestProcessor implements AuthorizationRequestProcessorInterface
{
    /**
     * @var string
     */
    protected const HEADER_PARAM_LOCATION = 'Location';

    /**
     * @var \League\OAuth2\Server\Grant\GrantTypeInterface
     */
    protected GrantTypeInterface $grantType;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator\UserValidatorInterface
     */
    protected UserValidatorInterface $userValidator;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig
     */
    protected AuthorizationPickingAppBackendApiConfig $config;

    /**
     * @param \League\OAuth2\Server\Grant\GrantTypeInterface $grantType
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Validator\UserValidatorInterface $userValidator
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig $config
     */
    public function __construct(
        GrantTypeInterface $grantType,
        UserValidatorInterface $userValidator,
        AuthorizationPickingAppBackendApiConfig $config
    ) {
        $this->grantType = $grantType;
        $this->userValidator = $userValidator;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    public function authorize(OauthRequestTransfer $oauthRequestTransfer): OauthResponseTransfer
    {
        try {
            $authorizationRequest = $this->createAuthorizationRequest($oauthRequestTransfer);

            $authRequest = $this->grantType->validateAuthorizationRequest($authorizationRequest);

            $authRequest = $this->validateUser($oauthRequestTransfer, $authRequest);

            $authorizationResponse = $this->grantType
                ->completeAuthorizationRequest($authRequest)
                ->generateHttpResponse(new Response());

            return $this->createOauthResponseTransfer($authorizationResponse);
        } catch (OAuthServerException $exception) {
            return $this->createErrorOauthResponseTransfer($exception);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     *
     * @return \GuzzleHttp\Psr7\ServerRequest
     */
    protected function createAuthorizationRequest(OauthRequestTransfer $oauthRequestTransfer): ServerRequest
    {
        $authorizeRequest = new ServerRequest('POST', '');
        $oauthRequestArray = $oauthRequestTransfer->toArray();
        $authorizeRequest = $authorizeRequest->withQueryParams($oauthRequestArray);

        return $authorizeRequest;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    protected function createOauthResponseTransfer(ResponseInterface $response): OauthResponseTransfer
    {
        $data = json_decode((string)$response->getBody(), true);
        $data = $this->expandDataWithHeaderParams($response, $data);

        return (new OauthResponseTransfer())
            ->fromArray($data, true)
            ->setIsValid(true);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array<string>|null $data
     *
     * @return array<string>
     */
    protected function expandDataWithHeaderParams(ResponseInterface $response, ?array $data): array
    {
        $locationHeader = $response->getHeader(static::HEADER_PARAM_LOCATION);

        if ($locationHeader !== []) {
            parse_str((string)parse_url($locationHeader[0], PHP_URL_QUERY), $params);

            foreach ($params as $key => $value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param \League\OAuth2\Server\Exception\OAuthServerException $exception
     *
     * @return \Generated\Shared\Transfer\OauthResponseTransfer
     */
    protected function createErrorOauthResponseTransfer(OAuthServerException $exception): OauthResponseTransfer
    {
        $oauthErrorTransfer = new OauthErrorTransfer();
        $oauthErrorTransfer
            ->setErrorType($exception->getErrorType())
            ->setMessage($exception->getMessage());

        return (new OauthResponseTransfer())
            ->setIsValid(false)
            ->setError($oauthErrorTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OauthRequestTransfer $oauthRequestTransfer
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authRequest
     *
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest
     */
    protected function validateUser(
        OauthRequestTransfer $oauthRequestTransfer,
        AuthorizationRequest $authRequest
    ): AuthorizationRequest {
        $userEntity = $this->userValidator->validate($oauthRequestTransfer, $authRequest->getClient());

        $authRequest->setUser($userEntity);
        $authRequest->setAuthorizationApproved($userEntity !== null);

        return $authRequest;
    }
}

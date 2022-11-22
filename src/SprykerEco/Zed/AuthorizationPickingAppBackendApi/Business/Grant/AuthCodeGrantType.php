<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Grant;

use DateInterval;
use DateTimeImmutable;
use League\OAuth2\Server\CodeChallengeVerifiers\PlainVerifier;
use League\OAuth2\Server\CodeChallengeVerifiers\S256Verifier;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use League\OAuth2\Server\ResponseTypes\RedirectResponse;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\AuthCodeRepositoryInterface;

class AuthCodeGrantType extends AuthCodeGrant implements GrantTypeInterface
{
    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_APPLICATION_NAME = 'request_application';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_CODE = 'code';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_STATE = 'state';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_SCOPE = 'scope';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_CLIENT_ID = 'client_id';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_REDIRECT_URI = 'redirect_uri';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_CODE_CHALLENGE = 'code_challenge';

    /**
     * @var string
     */
    protected const REQUEST_PARAMETER_CODE_CHALLENGE_METHOD = 'code_challenge_method';

    /**
     * @var \DateInterval
     */
    protected $authCodeTTL;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\AuthCodeRepositoryInterface
     */
    protected $authCodeRepository;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\ScopeRepositoryInterface
     */
    protected $scopeRepository;

    /**
     * @var bool
     */
    protected $requireCodeChallengeForPublicClients = true;

    /**
     * @var array<\League\OAuth2\Server\CodeChallengeVerifiers\CodeChallengeVerifierInterface>
     */
    protected $codeChallengeVerifiers = [];

    /**
     * @var string
     */
    protected $applicationContext;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig $config
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories\AuthCodeRepositoryInterface $authCodeRepository
     */
    public function __construct(
        AuthorizationPickingAppBackendApiConfig $config,
        AuthCodeRepositoryInterface $authCodeRepository,
        //        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->authCodeTTL = new DateInterval($config->getAuthCodeTTL());

        parent::__construct(
            $authCodeRepository,
            $refreshTokenRepository,
            $this->authCodeTTL,
        );

        if (in_array('sha256', hash_algos(), true)) {
            $s256Verifier = new S256Verifier();
            $this->codeChallengeVerifiers[$s256Verifier->getMethod()] = $s256Verifier;
        }

        $plainVerifier = new PlainVerifier();
        $this->codeChallengeVerifiers[$plainVerifier->getMethod()] = $plainVerifier;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     *
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request): AuthorizationRequest
    {
        $clientId = $this->getQueryStringParameter(
            static::REQUEST_PARAMETER_CLIENT_ID,
            $request,
            $this->getServerParameter('PHP_AUTH_USER', $request),
        );

        if ($clientId === null) {
            throw OAuthServerException::invalidRequest(static::REQUEST_PARAMETER_CLIENT_ID);
        }

        $clientEntity = $this->getClientEntityOrFail($clientId, $request);
        /** @var string|null $clientRedirectUri */
        $clientRedirectUri = $clientEntity->getRedirectUri();

        $redirectUri = $this->getQueryStringParameter(static::REQUEST_PARAMETER_REDIRECT_URI, $request);

        if ($redirectUri !== null) {
            if (!is_string($redirectUri)) {
                throw OAuthServerException::invalidRequest(static::REQUEST_PARAMETER_REDIRECT_URI);
            }

            $this->validateRedirectUri($redirectUri, $clientEntity, $request);
        } elseif ($clientRedirectUri === null) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidClient($request);
        }

//        //validate user
//        $userEntity = $this->validateUser($request, $clientEntity);

        //validate scopes
        $this->applicationContext = $this->getRequestParameter(static::REQUEST_PARAMETER_APPLICATION_NAME, $request);
        $scopes = $this->validateScopes(
            $this->getQueryStringParameter(static::REQUEST_PARAMETER_SCOPE, $request, $this->defaultScope),
            $redirectUri ?? $clientRedirectUri,
            $this->applicationContext,
        );

        $stateParameter = $this->getQueryStringParameter(static::REQUEST_PARAMETER_STATE, $request);

        $authorizationRequest = new AuthorizationRequest();
        $authorizationRequest->setGrantTypeId($this->getIdentifier());
        $authorizationRequest->setClient($clientEntity);
        $authorizationRequest->setRedirectUri($redirectUri);
//        $authorizationRequest->setUser($userEntity);
//        $authorizationRequest->setAuthorizationApproved($userEntity !== null);
        $authorizationRequest->setScopes($scopes);

        if ($stateParameter !== null) {
            $authorizationRequest->setState($stateParameter);
        }

        $authorizationRequest = $this->validateCodeChallenge($request, $authorizationRequest, $clientEntity);

        return $authorizationRequest;
    }

    /**
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authorizationRequest
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     * @throws \LogicException
     *
     * @return \League\OAuth2\Server\ResponseTypes\RedirectResponse
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest): RedirectResponse
    {
        if ($authorizationRequest->getUser() instanceof UserEntityInterface === false) {
            throw new LogicException('An instance of UserEntityInterface should be set on the AuthorizationRequest');
        }

        $finalRedirectUri = $authorizationRequest->getRedirectUri()
            ?? $this->getClientRedirectUri($authorizationRequest);

        $scopes = $this->scopeRepository->finalizeScopes(
            $authorizationRequest->getScopes(),
            $this->getIdentifier(),
            $authorizationRequest->getClient(),
            $authorizationRequest->getUser()->getIdentifier(),
            $this->applicationContext,
        );

        // The user approved the client, redirect them back with an auth code
        if ($authorizationRequest->isAuthorizationApproved() === true) {
            $authCode = $this->issueAuthCode(
                $this->authCodeTTL,
                $authorizationRequest->getClient(),
                $authorizationRequest->getUser()->getIdentifier(),
                $authorizationRequest->getRedirectUri(),
                $authorizationRequest->getScopes(),
            );

            $payload = [
                'client_id' => $authCode->getClient()->getIdentifier(),
                'redirect_uri' => $authCode->getRedirectUri(),
                'auth_code_id' => $authCode->getIdentifier(),
                'scopes' => $authCode->getScopes(),
                'user_id' => $authCode->getUserIdentifier(),
                'expire_time' => (new DateTimeImmutable())->add($this->authCodeTTL)->getTimestamp(),
                'code_challenge' => $authorizationRequest->getCodeChallenge(),
                'code_challenge_method' => $authorizationRequest->getCodeChallengeMethod(),
                'applicationContext' => $this->applicationContext,
            ];

            $jsonPayload = json_encode($payload);

            if ($jsonPayload === false) {
                throw new LogicException('An error was encountered when JSON encoding the authorization request response');
            }

            $response = new RedirectResponse();
            $response->setRedirectUri(
                $this->makeRedirectUri(
                    $finalRedirectUri,
                    [
                        'code' => $this->encrypt($jsonPayload),
                        'state' => $authorizationRequest->getState(),
                    ],
                ),
            );

            return $response;
        }

        // The user denied the client, redirect them back with an error
        throw OAuthServerException::accessDenied(
            'The user denied the request',
            $this->makeRedirectUri(
                $finalRedirectUri,
                [
                    'state' => $authorizationRequest->getState(),
                ],
            ),
        );
    }

    /**
     * @param array|string $scopes
     * @param string|null $redirectUri
     * @param string|null $applicationName
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     *
     * @return array<\League\OAuth2\Server\Entities\ScopeEntityInterface>
     */
    public function validateScopes($scopes, ?string $redirectUri = null, ?string $applicationName = null): array
    {
        if (is_string($scopes)) {
            $scopes = $this->convertScopesQueryStringToArray($scopes);
        }

        if (!is_array($scopes)) {
            throw OAuthServerException::invalidRequest(static::REQUEST_PARAMETER_SCOPE);
        }

        $validScopes = [];

        foreach ($scopes as $scopeItem) {
            $scope = $this->scopeRepository->getScopeEntityByIdentifier($scopeItem, $applicationName);

            if ($scope instanceof ScopeEntityInterface) {
                $validScopes[] = $scope;
            }
        }

        return $validScopes;
    }

    /**
     * @param \DateInterval $authCodeTTL
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     * @param string $userIdentifier
     * @param string|null $redirectUri
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     *
     * @return \League\OAuth2\Server\Entities\AuthCodeEntityInterface
     */
    protected function issueAuthCode(
        DateInterval $authCodeTTL,
        ClientEntityInterface $client,
        string $userIdentifier,
        ?string $redirectUri,
        array $scopes = []
    ): AuthCodeEntityInterface {
        $authCodeTransfer = $this->authCodeRepository->findAuthCode($client, $scopes);
        if ($authCodeTransfer !== null) {
            $authCode = $this->authCodeRepository->getNewAuthCode();
            $authCode->setClient($client);
            /** @var \DateTime $expiresAt */
            $expiresAt = $authCodeTransfer->getExpiresAtOrFail();
            $authCode->setExpiryDateTime(DateTimeImmutable::createFromMutable($expiresAt));
            $authCode->setIdentifier($authCodeTransfer->getIdentifierOrFail());
            $authCode->setUserIdentifier($userIdentifier);
            if ($redirectUri !== null) {
                $authCode->setRedirectUri($redirectUri);
            }

            foreach ($scopes as $scope) {
                $authCode->addScope($scope);
            }

            return $authCode;
        }

        return parent::issueAuthCode(
            $authCodeTTL,
            $client,
            $userIdentifier,
            $redirectUri,
            $scopes,
        );
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authorizationRequest
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $clientEntity
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     *
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest
     */
    protected function validateCodeChallenge(
        ServerRequestInterface $request,
        AuthorizationRequest $authorizationRequest,
        ClientEntityInterface $clientEntity
    ): AuthorizationRequest {
        $codeChallenge = $this->getQueryStringParameter(static::REQUEST_PARAMETER_CODE_CHALLENGE, $request);

        if ($codeChallenge !== null) {
            $codeChallengeMethod = $this->getQueryStringParameter(static::REQUEST_PARAMETER_CODE_CHALLENGE_METHOD, $request, 'plain');

            if (array_key_exists($codeChallengeMethod, $this->codeChallengeVerifiers) === false) {
                throw OAuthServerException::invalidRequest(
                    static::REQUEST_PARAMETER_CODE_CHALLENGE_METHOD,
                    'Code challenge method must be one of ' . implode(', ', array_map(
                        function ($method) {
                            return '`' . $method . '`';
                        },
                        array_keys($this->codeChallengeVerifiers),
                    )),
                );
            }

            // Validate code_challenge according to RFC-7636
            // @see: https://tools.ietf.org/html/rfc7636#section-4.2
            if (preg_match('/^[A-Za-z0-9-._~]{43,128}$/', $codeChallenge) !== 1) {
                throw OAuthServerException::invalidRequest(
                    static::REQUEST_PARAMETER_CODE_CHALLENGE,
                    'Code challenge must follow the specifications of RFC-7636.',
                );
            }

            $authorizationRequest->setCodeChallenge($codeChallenge);
            $authorizationRequest->setCodeChallengeMethod($codeChallengeMethod);
        } elseif ($this->requireCodeChallengeForPublicClients && !$clientEntity->isConfidential()) {
            throw OAuthServerException::invalidRequest(static::REQUEST_PARAMETER_CODE_CHALLENGE, 'Code challenge must be provided for public clients');
        }

        return $authorizationRequest;
    }

    /**
     * @param string $scopes
     *
     * @return array<string>
     */
    protected function convertScopesQueryStringToArray(string $scopes): array
    {
        return array_filter(explode(static::SCOPE_DELIMITER_STRING, trim($scopes)), function ($scope) {
            return $scope !== '';
        });
    }

    /**
     * Get the client redirect URI if not set in the request.
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authorizationRequest
     *
     * @return string
     */
    protected function getClientRedirectUri(AuthorizationRequest $authorizationRequest): string
    {
        return is_array($authorizationRequest->getClient()->getRedirectUri())
            ? $authorizationRequest->getClient()->getRedirectUri()[0]
            : $authorizationRequest->getClient()->getRedirectUri();
    }
}

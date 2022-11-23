<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Repositories;

use Generated\Shared\Transfer\AuthCodeTransfer;
use Generated\Shared\Transfer\SpyOauthAuthCodeEntityTransfer;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use LogicException;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Entities\AuthCodeEntity;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface
     */
    protected AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface
     */
    protected AuthorizationPickingAppBackendApiEntityManagerInterface $authorizationEntityManager;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface
     */
    protected AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface $authorizationEntityManager
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\Service\AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository,
        AuthorizationPickingAppBackendApiEntityManagerInterface $authorizationEntityManager,
        AuthorizationPickingAppBackendApiToUtilEncodingServiceInterface $utilEncodingService
    ) {
        $this->authorizationRepository = $authorizationRepository;
        $this->authorizationEntityManager = $authorizationEntityManager;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @return \Spryker\Zed\Oauth\Business\Model\League\Entities\AuthCodeEntity
     */
    public function getNewAuthCode(): AuthCodeEntity
    {
        return new AuthCodeEntity();
    }

    /**
     * @param \League\OAuth2\Server\Entities\AuthCodeEntityInterface $authCodeEntity
     *
     * @return void
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $userIdentifier = (string)$authCodeEntity->getUserIdentifier();

        /** @var string $encodedScopes */
        $encodedScopes = json_encode($authCodeEntity->getScopes());

        $authCodeEntityTransfer = new SpyOauthAuthCodeEntityTransfer();
        $authCodeEntityTransfer
            ->setIdentifier($authCodeEntity->getIdentifier())
            ->setUserIdentifier($userIdentifier)
            ->setExpirityDate($authCodeEntity->getExpiryDateTime()->format('Y-m-d H:i:s'))
            ->setFkOauthClient($authCodeEntity->getClient()->getIdentifier())
            ->setRedirectUri($authCodeEntity->getRedirectUri())
            ->setScopes($encodedScopes);

        $this->authorizationEntityManager->saveCode($authCodeEntityTransfer);
    }

    /**
     * @param string $codeId
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function revokeAuthCode(string $codeId): void
    {
        throw new LogicException('This grant does not use this method');
    }

    /**
     * @param string $codeId
     *
     * @throws \LogicException
     *
     * @return bool
     */
    public function isAuthCodeRevoked(string $codeId): bool
    {
        throw new LogicException('This grant does not use this method');
    }

    /**
     * @param \League\OAuth2\Server\Entities\ClientEntityInterface $client
     * @param array<\League\OAuth2\Server\Entities\ScopeEntityInterface> $scopes
     *
     * @return \Generated\Shared\Transfer\AuthCodeTransfer|null
     */
    public function findAuthCode(ClientEntityInterface $client, array $scopes = []): ?AuthCodeTransfer
    {
        return $this->authorizationRepository->findAuthCode($client, $scopes);
    }
}

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
use Spryker\Zed\Oauth\Business\Model\League\Entities\AuthCodeEntity;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface;

//TODO CHANGE DEPENDENCIES
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
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Persistence\AuthorizationPickingAppBackendApiEntityManagerInterface $authorizationEntityManager
// * @param \Spryker\Zed\Oauth\Dependency\Service\OauthToUtilEncodingServiceInterface $utilEncodingService
// * @param array<\Spryker\Zed\OauthExtension\Dependency\Plugin\OauthUserIdentifierFilterPluginInterface> $oauthUserIdentifierFilterPlugins
     */
    public function __construct(
        AuthorizationPickingAppBackendApiRepositoryInterface $authorizationRepository,
        AuthorizationPickingAppBackendApiEntityManagerInterface $authorizationEntityManager,
        //        OauthToUtilEncodingServiceInterface $utilEncodingService,
        //        array $oauthUserIdentifierFilterPlugins = []
    ) {
        $this->authorizationRepository = $authorizationRepository;
        $this->authorizationEntityManager = $authorizationEntityManager;
//        $this->utilEncodingService = $utilEncodingService;
//        $this->oauthUserIdentifierFilterPlugins = $oauthUserIdentifierFilterPlugins;
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
        $userIdentifier = $this->filterUserIdentifier($userIdentifier);

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

        $this->authorizationEntityManager->saveAuthCode($authCodeEntityTransfer);
    }

//    /**
//     * @param string $codeId
//     *
//     * @return void
//     */
//    public function revokeAuthCode($codeId): void
//    {
//        $this->authorizationEntityManager->deleteAuthCodeByIdentifier($codeId);
//    }
//
//    /**
//     * @param string $codeId
//     *
//     * @return bool
//     */
//    public function isAuthCodeRevoked($codeId): bool
//    {
//        return $this->oauthRepository->findAuthCodeByCodeId($codeId) === null;
//    }

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

    /**
     * @param string $userIdentifier
     *
     * @return string
     */
    protected function filterUserIdentifier(string $userIdentifier): string
    {
        $decodedUserIdentifier = $this->utilEncodingService->decodeJson($userIdentifier, true);

        if ($decodedUserIdentifier) {
            foreach ($this->oauthUserIdentifierFilterPlugins as $oauthUserIdentifierFilterPlugin) {
                $decodedUserIdentifier = $oauthUserIdentifierFilterPlugin->filter($decodedUserIdentifier);
            }
        }

        return (string)$this->utilEncodingService->encodeJson($decodedUserIdentifier);
    }
}

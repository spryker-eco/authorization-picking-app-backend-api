<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Glue\AuthorizationPickingAppBackendApi\Controller;

use Generated\Shared\Transfer\AuthCodeAttributesTransfer;
use Generated\Shared\Transfer\AuthCodeResponseAttributesTransfer;
use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\OauthRequestTransfer;
use Generated\Shared\Transfer\OauthResponseTransfer;
use Spryker\Glue\Kernel\Backend\Controller\AbstractBackendApiController;
use SprykerEco\Glue\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \SprykerEco\Glue\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiFactory getFactory()
 */
class AuthorizeResourceController extends AbstractBackendApiController
{
    /**
     * @param \Generated\Shared\Transfer\AuthCodeAttributesTransfer $authCodeAttributesTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function postAction(
        AuthCodeAttributesTransfer $authCodeAttributesTransfer,
        GlueRequestTransfer $glueRequestTransfer
    ): GlueResponseTransfer {
        $oauthRequestTransfer = (new OauthRequestTransfer())
            ->fromArray($authCodeAttributesTransfer->toArray(), true);

        $oauthResponseTransfer = $this->getFactory()->getAuthorizationPickingAppBackendApiFacade()->authorize($oauthRequestTransfer);

        $glueResponseTransfer = $this->mapAuthenticationAttributesToGlueResponseTransfer($oauthResponseTransfer);
        if (!$glueResponseTransfer->getHttpStatus()) {
            $glueResponseTransfer->setHttpStatus(Response::HTTP_OK);
        }

        return $glueResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthResponseTransfer $oauthResponseTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    protected function mapAuthenticationAttributesToGlueResponseTransfer(
        OauthResponseTransfer $oauthResponseTransfer
    ): GlueResponseTransfer {
        $glueResponseTransfer = new GlueResponseTransfer();

        if ($oauthResponseTransfer->getIsValid() === false) {
            $glueResponseTransfer
                ->setHttpStatus(Response::HTTP_BAD_REQUEST)
                ->addError((new GlueErrorTransfer())
                    ->setMessage($oauthResponseTransfer->getErrorOrFail()->getMessage())
                    ->setStatus(Response::HTTP_BAD_REQUEST)
                    ->setCode($oauthResponseTransfer->getErrorOrFail()->getErrorType()));

            return $glueResponseTransfer;
        }

        $resourceTransfer = (new GlueResourceTransfer())
            ->setType(AuthorizationPickingAppBackendApiConfig::RESOURCE_AUTHORIZE)
            ->setAttributes(
                (new AuthCodeResponseAttributesTransfer())
                ->fromArray($oauthResponseTransfer->toArray(), true),
            );
        $glueResponseTransfer->addResource($resourceTransfer);

        return $glueResponseTransfer;
    }
}

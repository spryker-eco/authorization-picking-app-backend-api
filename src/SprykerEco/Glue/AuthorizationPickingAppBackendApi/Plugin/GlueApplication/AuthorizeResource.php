<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Glue\AuthorizationPickingAppBackendApi\Plugin\GlueApplication;

use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer;
use Spryker\Glue\GlueApplication\Plugin\GlueApplication\Backend\AbstractResourcePlugin;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use SprykerEco\Glue\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig;
use SprykerEco\Glue\AuthorizationPickingAppBackendApi\Controller\AuthorizeResourceController;

class AuthorizeResource extends AbstractResourcePlugin implements ResourceInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getType(): string
    {
        return AuthorizationPickingAppBackendApiConfig::RESOURCE_AUTHORIZE;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @uses \SprykerEco\Glue\AuthorizationPickingAppBackendApi\Controller\AuthorizeResourceController
     *
     * @return string
     */
    public function getController(): string
    {
        return AuthorizeResourceController::class;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer
     */
    public function getDeclaredMethods(): GlueResourceMethodCollectionTransfer
    {
        return (new GlueResourceMethodCollectionTransfer())
            ->setPost(
                (new GlueResourceMethodConfigurationTransfer())
                    ->setAction('postAction')
                    ->setAttributes('\Generated\Shared\Transfer\AuthCodeAttributesTransfer')
                    ->setIsSnakeCased(true),
            );
    }
}

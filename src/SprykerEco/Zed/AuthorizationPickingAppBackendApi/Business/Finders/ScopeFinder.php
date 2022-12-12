<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Finders;

use Generated\Shared\Transfer\OauthScopeFindTransfer;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Exception\CacheFileNotFoundException;
use SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External\AuthorizationPickingAppBackendApiToYamlAdapterInterface;

class ScopeFinder implements ScopeFinderInterface
{
    /**
     * @uses \Spryker\Glue\GlueBackendApiApplication\Plugin\DocumentationGeneratorApi\BackendApiApplicationProviderPlugin::GLUE_BACKEND_API_APPLICATION_NAME
     *
     * @var string
     */
    protected const GLUE_BACKEND_API_APPLICATION_NAME = 'backend';

    /**
     * @var string
     */
    protected const CACHE_FILE_NOT_FOUND_EXCEPTION_MESSAGE = 'Scope collection cache file not found. Please run the following command to generate it: `console oauth:scope-collection-file:generate`';

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig
     */
    protected AuthorizationPickingAppBackendApiConfig $config;

    /**
     * @var \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External\AuthorizationPickingAppBackendApiToYamlAdapterInterface
     */
    protected AuthorizationPickingAppBackendApiToYamlAdapterInterface $yamlAdapter;

    /**
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\AuthorizationPickingAppBackendApiConfig $config
     * @param \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External\AuthorizationPickingAppBackendApiToYamlAdapterInterface $yamlAdapter
     */
    public function __construct(
        AuthorizationPickingAppBackendApiConfig $config,
        AuthorizationPickingAppBackendApiToYamlAdapterInterface $yamlAdapter
    ) {
        $this->config = $config;
        $this->yamlAdapter = $yamlAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\OauthScopeFindTransfer $oauthScopeFindTransfer
     *
     * @throws \SprykerEco\Zed\AuthorizationPickingAppBackendApi\Business\Exception\CacheFileNotFoundException
     *
     * @return string|null
     */
    public function find(OauthScopeFindTransfer $oauthScopeFindTransfer): ?string
    {
        if (!file_exists($this->config->getGeneratedFullFileNameForCollectedScopes())) {
            throw new CacheFileNotFoundException(static::CACHE_FILE_NOT_FOUND_EXCEPTION_MESSAGE);
        }

        $scopes = $this->yamlAdapter->parseFile($this->config->getGeneratedFullFileNameForCollectedScopes());

        if (
            $scopes &&
            isset($scopes[static::GLUE_BACKEND_API_APPLICATION_NAME]) &&
            in_array($oauthScopeFindTransfer->getIdentifier(), $scopes[static::GLUE_BACKEND_API_APPLICATION_NAME])
        ) {
            return $oauthScopeFindTransfer->getIdentifier();
        }

        return null;
    }
}

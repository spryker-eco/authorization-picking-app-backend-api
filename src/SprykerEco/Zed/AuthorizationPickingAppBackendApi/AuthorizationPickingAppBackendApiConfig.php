<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class AuthorizationPickingAppBackendApiConfig extends AbstractBundleConfig
{
    /**
     * @uses \Spryker\Zed\Oauth\OauthConfig::GENERATED_FULL_FILE_NAME
     *
     * @var string
     */
    protected const GENERATED_FULL_FILE_NAME = '/Generated/Zed/Oauth/GlueScopesCache/glue_scopes_cache.yml';

    /**
     * @uses \Spryker\Shared\Oauth\OauthConstants::ENCRYPTION_KEY()
     *
     * @var string
     */
    protected const ENCRYPTION_KEY = 'ENCRYPTION_KEY';

    /**
     * @var bool
     */
    protected const OAUTH_REQUIRE_CODE_CHALLENGE = true;

    /**
     * Specification:
     * - Sets the interval for how long is the auth code is valid, this will be feed to \DateTime object.
     *
     * @api
     *
     * @return string
     */
    public function getAuthCodeTTL(): string
    {
        return 'PT10M';
    }

    /**
     * Specification:
     * - Returns user scopes.
     *
     * @api
     *
     * @return array<string>
     */
    public function getUserScopes(): array
    {
        return [];
    }

    /**
     * Specification:
     * - Returns the path for the cache file with configured scopes.
     *
     * @api
     *
     * @return string
     */
    public function getGeneratedFullFileNameForCollectedScopes(): string
    {
        return APPLICATION_SOURCE_DIR . static::GENERATED_FULL_FILE_NAME;
    }

    /**
     * Specification:
     * - The flag defaults to true and requires all public clients to provide a PKCE code challenge when requesting an authorization code.
     *
     * @api
     *
     * @return bool
     */
    public function isCodeChallengeRequired(): bool
    {
        return static::OAUTH_REQUIRE_CODE_CHALLENGE;
    }

    /**
     * Specification:
     * - Encryption key used to encrypt data when generates authorization code.
     *
     * @api
     *
     * @return string
     */
    public function getEncryptionKey(): string
    {
        return $this->get(static::ENCRYPTION_KEY);
    }
}

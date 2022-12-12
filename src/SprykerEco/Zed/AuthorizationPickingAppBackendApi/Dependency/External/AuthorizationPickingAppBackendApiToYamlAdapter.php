<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AuthorizationPickingAppBackendApi\Dependency\External;

use Symfony\Component\Yaml\Yaml;

class AuthorizationPickingAppBackendApiToYamlAdapter implements AuthorizationPickingAppBackendApiToYamlAdapterInterface
{
    /**
     * @param string $filename
     * @param int $flags
     *
     * @return array<mixed>
     */
    public function parseFile(string $filename, int $flags = 0): array
    {
        return Yaml::parseFile($filename, $flags);
    }
}

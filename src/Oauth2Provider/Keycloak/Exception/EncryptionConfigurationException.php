<?php

namespace App\Oauth2Provider\Keycloak\Exception;

class EncryptionConfigurationException extends \Exception
{
    /**
     * Returns properly formatted exception when response decryption fails.
     */
    public static function undeterminedEncryption(): static
    {
        return new self(
            'The given response may be encrypted and sufficient ' .
            'encryption configuration has not been provided.',
            400
        );
    }
}

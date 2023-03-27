<?php

namespace App\Oauth2Provider\Keycloak;

use App\Oauth2Provider\Keycloak\Exception\EncryptionConfigurationException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class Keycloak extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Keycloak URL, eg. http://localhost:8080/auth.
     */
    public ?string $authServerUrl = null;

    /**
     * Realm name, eg. demo.
     */
    public ?string $realm = null;

    /**
     * Encryption algorithm.
     *
     * You must specify supported algorithms for your application. See
     * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
     * for a list of spec-compliant algorithms.
     */
    public ?string  $encryptionAlgorithm = null;

    /**
     * Encryption key.
     */
    public ?string $encryptionKey = null;

    /**
     * Keycloak version.
     */
    public ?string $version = null;

    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options       An array of options to set on this provider.
     *                             Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *                             Individual providers may introduce more options, as needed.
     * @param array $collaborators An array of collaborators that may be used to
     *                             override this provider's default behavior. Collaborators include
     *                             `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
     *                             Individual providers may introduce more collaborators, as needed.
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->authServerUrl = $options['auth_server_url'];

        if (isset($options['realm'])) {
            $this->realm = $options['realm'];
        }

        if (isset($options['encryptionKeyPath'])) {
            $this->setEncryptionKeyPath($options['encryptionKeyPath']);
            unset($options['encryptionKeyPath']);
        }

        if (isset($options['version'])) {
            $this->setVersion($options['version']);
        }

        parent::__construct($options, $collaborators);
    }

    /**
     * Attempts to decrypt the given response.
     *
     * @throws JsonException
     * @throws EncryptionConfigurationException
     * @throws \JsonException
     */
    public function decryptResponse(array|string|null $response): array|string|null
    {
        if (!is_string($response)) {
            return $response;
        }

        if ($this->usesEncryption()) {
            return json_decode(json_encode(JWT::decode(
                $response,
                new Key($this->encryptionKey, $this->encryptionAlgorithm)
            ), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        }

        throw EncryptionConfigurationException::undeterminedEncryption();
    }

    /**
     * Get authorization url to begin OAuth flow.
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/auth';
    }

    /**
     * Get access token url to retrieve token.
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/token';
    }

    /**
     * Get provider url to fetch user details.
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/userinfo';
    }

    /**
     * Builds the logout URL.
     *
     * @return string Authorization URL
     */
    public function getLogoutUrl(array $options = []): string
    {
        $base  = $this->getBaseLogoutUrl();
        $query = $this->buildQueryString($options);

        return $this->appendQuery($base, $query);
    }

    /**
     * Get logout url to logout of session token.
     */
    private function getBaseLogoutUrl(): string
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/logout';
    }

    /**
     * Creates base url from provider configuration.
     */
    protected function getBaseUrlWithRealm(): string
    {
        return $this->authServerUrl . '/realms/' . $this->realm;
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return string[]
     */
    protected function getDefaultScopes(): array
    {
        $scopes = [
            'profile',
            'email',
        ];
        if ($this->validateGteVersion('20.0.0')) {
            $scopes[] = 'openid';
        }

        return $scopes;
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator, defaults to ','
     */
    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    /**
     * Check a provider response for errors.
     *
     * @param array|string $data Parsed response data
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (!empty($data['error'])) {
            $error = $data['error'];
            if (isset($data['error_description'])) {
                $error .= ': ' . $data['error_description'];
            }

            throw new IdentityProviderException($error, 0, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     */
    protected function createResourceOwner(array $response, AccessToken $token): KeycloakResourceOwner
    {
        return new KeycloakResourceOwner($response);
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @throws JsonException
     * @throws EncryptionConfigurationException
     * @throws \JsonException
     */
    public function getResourceOwner(AccessToken $token): KeycloakResourceOwner
    {
        $response = $this->fetchResourceOwnerDetails($token);

        // We are always getting an array. We have to check if it is
        // the array we created
        if (array_key_exists('jwt', $response)) {
            $response = $response['jwt'];
        }

        $response = $this->decryptResponse($response);

        return $this->createResourceOwner($response, $token);
    }

    /**
     * Updates expected encryption algorithm of Keycloak instance.
     */
    public function setEncryptionAlgorithm(string $encryptionAlgorithm): static
    {
        $this->encryptionAlgorithm = $encryptionAlgorithm;

        return $this;
    }

    /**
     * Updates expected encryption key of Keycloak instance.
     */
    public function setEncryptionKey(string $encryptionKey): static
    {
        $this->encryptionKey = $encryptionKey;

        return $this;
    }

    /**
     * Updates expected encryption key of Keycloak instance to content of given
     * file path.
     */
    public function setEncryptionKeyPath(string $encryptionKeyPath): static
    {
        try {
            $this->encryptionKey = file_get_contents($encryptionKeyPath);
        } catch (Exception) {
            // Not sure how to handle this yet.
        }

        return $this;
    }

    /**
     * Updates the keycloak version.
     */
    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Checks if provider is configured to use encryption.
     */
    public function usesEncryption(): bool
    {
        return $this->encryptionAlgorithm && $this->encryptionKey;
    }

    /**
     * Parses the response according to its content-type header.
     *
     * @throws \UnexpectedValueException
     */
    protected function parseResponse(ResponseInterface $response): array
    {
        // We have a problem with keycloak when the userinfo responses
        // with a jwt token
        // Because it just return a jwt as string with the header
        // application/jwt
        // This can't be parsed to a array
        // Dont know why this function only allow an array as return value...
        $content = (string) $response->getBody();
        $type    = $this->getContentType($response);

        if (str_contains($type, 'jwt')) {
            // Here we make the temporary array
            return ['jwt' => $content];
        }

        return parent::parseResponse($response);
    }

    /**
     * Validate if version is greater or equal.
     */
    private function validateGteVersion(string $version): bool
    {
        return isset($this->version) && version_compare($this->version, $version, '>=');
    }
}

<?php

namespace App\Oauth2Provider\LemonLdap;

use App\Oauth2Provider\Keycloak\Keycloak;
use League\OAuth2\Client\Token\AccessToken;

class LemonLdap extends Keycloak
{
    /**
     * Get authorization url to begin OAuth flow.
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->authServerUrl . '/authorize';
    }

    /**
     * Get access token url to retrieve token.
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->authServerUrl . '/token';
    }

    /**
     * Get provider url to fetch user details.
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->authServerUrl . '/userinfo';
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
        return $this->authServerUrl . '/logout';
    }

    /**
     * Generate a user object from a successful user details request.
     */
    protected function createResourceOwner(array $response, AccessToken $token): LemonLdapResourceOwner
    {
        return new LemonLdapResourceOwner($response);
    }
}

<?php

namespace Dkoehn\CCB\OAuth2\Client;

use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class CCBClient extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * The Http Client
     *
     * @var Client
     */
    private $_client;

    protected $apiDomain = 'https://api.ccbchurch.com';
    protected $oauthAppDomain = 'https://oauth.ccbchurch.com';

    public function __construct(array $options = [], array $collaborators = [])
    {
        $requestFactory = new CCBRequestFactory();

        parent::__construct(
            $options,
            array_merge(
                $collaborators,
                ['requestFactory' => $requestFactory]
            )
        );
    }

    /**
     * Constructs and returns the Http Client
     *
     * @return Client
     */
    protected function getClient(): Client
    {
        if ($this->_client === null) {
            $this->_client = new Client(
                [
                    'base_uri' => $this->apiDomain,
                    'headers' => ['Content-Type' => 'application/json'],
                ]
            );
        }

        return $this->_client;
    }

    /**
     * Perform a GET request to CCB api
     *
     * @param string $uri         The uri to get
     * @param string $accessToken The Access Token
     *
     * @return array The json response
     */
    public function get(string $uri, string $accessToken)
    {
        $request = $this->getAuthenticatedRequest(
            'GET',
            ltrim($uri, '/'),
            $accessToken
        );
        $response = $this->getClient()->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Perform a POST request to CCB api
     *
     * @param string $uri         The uri to get
     * @param array  $data        The post data to send
     * @param string $accessToken The Access Token
     *
     * @return array The json response
     */
    public function post(string $uri, array $data, string $accessToken)
    {
        $request = $this->getAuthenticatedRequest(
            'POST',
            ltrim($uri, '/'),
            $accessToken,
            ['json' => $data]
        );
        $response = $this->getClient()->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Perform a PUT request to CCB api
     *
     * @param string $uri         The uri to get
     * @param array  $data        The post data to send
     * @param string $accessToken The Access Token
     *
     * @return array The json response
     */
    public function put(string $uri, array $data, string $accessToken)
    {
        $request = $this->getAuthenticatedRequest(
            'PUT',
            ltrim($uri, '/'),
            $accessToken,
            ['json' => $data]
        );
        $response = $this->getClient()->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Perform a DELETE request to CCB api
     *
     * @param string $uri         The uri to get
     * @param string $accessToken The Access Token
     *
     * @return array The json response
     */
    public function delete(string $uri, string $accessToken)
    {
        $request = $this->getAuthenticatedRequest(
            'DELETE',
            ltrim($uri, '/'),
            $accessToken
        );
        $response = $this->getClient()->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Returns the Base Authorization Url
     *
     * @return string the Base Authorization Url
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->oauthAppDomain . '/oauth/authorize';
    }

    /**
     * Returns the Base Access Token Url
     *
     * @param array $params the custom params to use for the Access Token Url
     *
     * @return string the Base Access Token Url
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->apiDomain . '/oauth/token';
    }

    /**
     * Returns the Resource Owner Details Url
     *
     * @param AccessToken $token the Access Token
     *
     * @return string the Resource Owner Details Url
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->apiDomain . '/me';
    }

    /**
     * Returns the Default Scopes
     *
     * @return array the Default Scopes
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Returns the Scope Separator
     *
     * @return string the Scope Separator
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Checks the response for errors
     *
     * @param ResponseInterface $response the response
     * @param mixed             $data     the data
     *
     * @throws IdentityProviderException
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $error = $data['error'];
            if (!is_string($error)) {
                $error = var_export($error, true);
            }
            $code  = 0; // $this->responseCode && !empty($data[$this->responseCode])? $data[$this->responseCode] : 0;
            // if (!is_int($code)) {
            //     $code = intval($code);
            // }
            throw new IdentityProviderException($error, $code, $data);
        }
    }

    /**
     * Creates a Resource Owner
     *
     * @param array       $response the response data
     * @param AccessToken $token    the Access Token
     *
     * @return null
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return null;
    }

    /**
     * Returns the Api Domain
     *
     * @return string
     */
    public function getApiDomain()
    {
        return $this->apiDomain;
    }

    /**
     * Sets the Api Domain
     *
     * @param string $apiDomain The Api Domain
     *                          (defaults to https://api.ccbchurch.com)
     *
     * @return void
     */
    public function setApiDomain(string $apiDomain)
    {
        $this->apiDomain = $apiDomain;
    }

    /**
     * Returns the OAuth App Domain
     *
     * @return string
     */
    public function getOAuthAppDomain()
    {
        return $this->oauthAppDomain;
    }

    /**
     * Sets the OAuth App Domain
     *
     * @param string $oauthAppDomain The OAuth App Domain
     *                               (defaults to https://oauth.ccbchurch.com)
     *
     * @return void
     */
    public function setOAuthAppDomain(string $oauthAppDomain)
    {
        $this->oauthAppDomain = $oauthAppDomain;
    }
}

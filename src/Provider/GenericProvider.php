<?php

namespace League\OAuth1\Client\Provider;

use InvalidArgumentException;
use League\OAuth1\Client\Credentials\ClientCredentials;
use League\OAuth1\Client\RequestInjector;
use League\OAuth1\Client\User;
use LogicException;
use Psr\Http\Message\ResponseInterface;

class GenericProvider extends BaseProvider
{
    /** @var array */
    private $config;

    /** @var callable */
    private $userDetailsExtractor;

    public function __construct(ClientCredentials $clientCredentials, array $config = [])
    {
        parent::__construct($clientCredentials);

        $this->config = $this->parseConfig($config);
    }

    protected function getTemporaryCredentialsMethod(): string
    {
        return $this->config['temporary_credentials']['method'] ?? parent::getTemporaryCredentialsMethod();
    }

    protected function getTemporaryCredentialsLocation(): string
    {
        return $this->config['temporary_credentials']['location'] ?? parent::getTemporaryCredentialsLocation();
    }

    protected function getAuthorizationMethod(): string
    {
        return $this->config['authorization']['method'] ?? parent::getAuthorizationMethod();
    }

    protected function getAuthorizationLocation(): string
    {
        return $this->config['authorization']['location'] ?? parent::getAuthorizationLocation();
    }

    protected function getTokenCredentialsMethod(): string
    {
        return $this->config['token_credentials']['method'] ?? parent::getTokenCredentialsMethod();
    }

    protected function getTokenCredentialsLocation(): string
    {
        return $this->config['token_credentials']['location'] ?? parent::getTokenCredentialsLocation();
    }

    protected function getUserDetailsMethod(): string
    {
        return $this->config['user_details']['method'] ?? parent::getUserDetailsMethod();
    }

    protected function getAuthenticatedLocation(): string
    {
        return $this->config['authenticated']['location'] ?? parent::getAuthenticatedLocation();
    }

    protected function getTemporaryCredentialsUri(): string
    {
        return $this->config['temporary_credentials']['uri'];
    }

    protected function getAuthorizationUri(): string
    {
        return $this->config['authorization']['uri'];
    }

    protected function getTokenCredentialsUri(): string
    {
        return $this->config['token_credentials']['uri'];
    }

    protected function getUserDetailsUri(): string
    {
        return $this->config['user_details']['uri'];
    }

    /**
     * @inheritDoc
     *
     * @throws LogicException           If an extractor was not configured prior to trying to extract user details
     * @throws InvalidArgumentException If the configured extractor did not return the correct instance
     */
    public function extractUserDetails(ResponseInterface $response): User
    {
        if (null === $this->userDetailsExtractor) {
            throw new LogicException(sprintf(
                'You must first configure how to extract user details using %s::extractUserDetailsUsing()',
                get_class($this)
            ));
        }

        $user = ($this->userDetailsExtractor)($response, $this);

        if (!$user instanceof User) {
            throw new InvalidArgumentException(sprintf(
                'The configured extractor did not return an instance of %s.',
                User::class
            ));
        }

        return $user;
    }

    /**
     * Configure the callback used to resolve user details. The callback will receive an instance
     * of the response generated by the configured user details URI as well as this provider
     * instance and must return an instance of `\League\OAuth1\Client\User`
     */
    public function extractUserDetailsUsing(callable $callback): GenericProvider
    {
        $this->userDetailsExtractor = $callback;

        return $this;
    }

    private function parseConfig(array $config): array
    {
        $defaults = [
            'temporary_credentials' => [
                'method' => 'GET',
                'location' => RequestInjector::LOCATION_QUERY,

                // Required
                'uri' => null,
            ],
            'authorization' => [
                'method' => 'GET',
                'location' => RequestInjector::LOCATION_QUERY,

                // Required
                'uri' => null,
            ],
            'token_credentials' => [
                'method' => 'POST',
                'location' => RequestInjector::LOCATION_HEADER,

                // Required
                'uri' => null,
            ],
            'user_details' => [
                'method' => 'GET',

                // Required
                'uri' => null,
            ],
            'authenticated' => [
                'location' => RequestInjector::LOCATION_HEADER,
            ],
        ];

        return array_replace_recursive($defaults, $config);
    }
}
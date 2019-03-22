<?php

namespace MarketRedesign\MrdLogin;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'MRDLOGIN';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'openid',
        'profile',
        'email',
    ];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getLaravelPassportUrl('authorize_uri'), $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->getLaravelPassportUrl('token_uri');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getLaravelPassportUrl('userinfo_uri'), [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['sub'],
            'nickname' => array_get($user, 'name'),
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getLaravelPassportUrl($type)
    {
        return rtrim($this->getConfig('host'), '/').'/'.ltrim(($this->getConfig($type, Arr::get([
                'authorize_uri' => 'oauth/authorize',
                'token_uri'     => 'oauth/token',
                'userinfo_uri'  => 'api/userinfo',
            ], $type))), '/');
    }
}

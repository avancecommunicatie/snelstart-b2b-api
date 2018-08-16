<?php

namespace Snelstart;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Snelstart\Exceptions\SnelstartException;

class Snelstart
{
    protected $path = 'https://b2bapi.snelstart.nl/v1/';
    protected $authPath = 'https://auth.snelstart.nl/b2b/token';
    protected $apiKey;
    protected $accessToken;

    /**
     * @param string $connectionKey
     * @return Snelstart
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateAccessToken(string $connectionKey): Snelstart
    {
        $response = (new Client())->send(
            new Request('POST', $this->authPath),
            [
                'timeout' => 60,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'clientkey',
                    'clientkey' => $connectionKey,
                ]
            ]
        );
        $data = json_decode($response->getBody(), true);
        $this->setAccessToken($data['access_token']);

        return $this;
    }

    public function send(string $uri, string $method = 'GET', string $body = null)
    {
        $request = new Request($method, $this->path . $uri, $this->headers(), $body);
        $response = (new Client())->send($request, ['timeout' => 60]);
        return json_decode($response->getBody()->getContents(), 1);
    }

    /** @throws SnelstartException */
    protected function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $this->getApiKey(),
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ];
    }

    /** @throws SnelstartException */
    public function getApiKey()
    {
        if (!$this->apiKey) {
            throw new SnelstartException('ApiKey required.', 400);
        }
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /** @throws SnelstartException */
    public function getAccessToken()
    {
        if (!$this->accessToken) {
            throw new SnelstartException('Generate AccessToken first.', 400);
        }
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}

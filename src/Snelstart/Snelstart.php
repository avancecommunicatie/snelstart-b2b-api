<?php

namespace Snelstart;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Snelstart\Exceptions\SnelstartException;

class Snelstart
{
    protected $path = 'https://b2bapi.snelstart.nl/v1/';
    protected $auth_path = 'https://auth.snelstart.nl/b2b/token';
    protected $username;
    protected $password;
    protected $api_key;
    protected $access_token;

    public function generateAccessToken($connection_key, $api_key)
    {
        $this->setUsernameAndPassword($connection_key);

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $body = "grant_type=password&username={$this->username}&password={$this->password}";

        $request = new Request('POST', $this->auth_path, $headers, $body);
        $response = $response = $client->send($request, ['timeout' => 15]);
        $data = json_decode($response->getBody(), true);

        if (!empty($data['access_token'])) {
            $this->setAccessToken($data['access_token']);
        }

        return $this;
    }

    protected function setUsernameAndPassword($connection_key)
    {
        list($username, $password) = explode(':', base64_decode($connection_key));

        $this->username = urlencode($username);
        $this->password = urlencode($password);
    }

    public function send($uri, $method = 'GET', $body = null)
    {
        $client = new Client();
        $request = new Request($method, $this->path.$uri, $this->headers(), $body);
        $response = $response = $client->send($request, ['timeout' => 60]);
        return json_decode($response->getBody()->getContents(), 1);
    }

    protected function headers()
    {
        return [
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $this->getApiKey(),
            'Authorization' => 'Bearer '.$this->getAccessToken()
        ];
    }

    public function getApiKey()
    {
        if (!$this->api_key) {
            throw new SnelstartException("ApiKey required.", 400);
        }
        return $this->api_key;
    }

    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }

    public function getAccessToken()
    {
        if (!$this->access_token) {
            throw new SnelstartException("Generate AccessToken first.", 400);
        }
        return $this->access_token;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }
}

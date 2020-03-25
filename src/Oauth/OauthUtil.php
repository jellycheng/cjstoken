<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-25
 * Time: 13:01
 */
namespace CjsToken\Oauth;

class OauthUtil
{

    protected $client_id;
    protected $secret;

    public static function newInstance() {
        $instance = new static();
        return $instance;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    public function getAuthorizationHeaderFormat() {
        $baseic = base64_encode($this->getClientId() . ":" . $this->getSecret());
        $header = "Authorization: Basic " . $baseic;
        return $header;
    }

    public function getAuthorizationBearerFormat($token) {
        $str = "Authorization: Bearer " . $token;
        return $str;
    }


}
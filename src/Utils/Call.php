<?php

namespace AnimusCoop\AppleTokenAuth\Utils;

class Call {

    private $jwt = '',$code = null,$client_id= null;

    public function __construct($jwtSigned,$objCall)
    {
        $this->jwt=$jwtSigned;
        $this->code = $objCall->code;
        $this->client_id = $objCall->client_id;
    }


    public function getResponse()
    {

        $curl = curl_init();
        $postData = [
            "client_id=$this->client_id",
            "client_secret=$this->jwt",
            "code=$this->code",
            "grant_type=authorization_code"
        ];


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://appleid.apple.com/auth/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => implode('&', $postData),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);
        $responseArr = [];
        $responseArr['authorization'] = $response;
        $responseArr['user'] = Jwt::parseJWT($response->id_token);

        return $responseArr;

    }
}
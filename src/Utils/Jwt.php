<?php

namespace AnimusCoop\AppleTokenAuth\Utils;

class  Jwt
{


    public  $jwtSigned = '';
    private $alg = 'ES256',$typ = 'JWT';


    public function __construct($data)
    {
       $this->jwtSigned = $this->generateJWT($data);
    }

    private function generateJWT($info)
    {
        $header = [
            'alg' => $this->alg,
            'kid' => $info->key_id,
            'typ' => $this->typ
        ];
        $body = [
            'iss' => $info->team_id,
            'iat' => time(),
            'exp' => time() + 86400 * 180,
            'aud' => 'https://appleid.apple.com',
            'sub' => $info->client_id,
        ];

        $key = openssl_pkey_get_private(file_get_contents($info->key));
        if (!$key) {
            return false;
        }

        $segments = array();
        $segments[] = $this->urlsafeB64Encode(json_encode($header));
        $segments[] = $this->urlsafeB64Encode(json_encode($body));
        $signing_input = implode('.', $segments);

        $signature = '';
        $success = openssl_sign($signing_input, $signature, $key, 'SHA256');
        if (!$success) {
            throw new \Exception('Encryption key fail, please check your p8 key file');
        }
        $signature = $this->signatureFromDER($signature, 256);

        $segments[] = $this->urlsafeB64Encode($signature);

        $clientSecret = implode('.', $segments);


        return $clientSecret;
    }

    // function from firebase/jwt-php

    private function urlsafeB64Encode($input)
    {
        return \str_replace('=', '', \strtr(\base64_encode($input), '+/', '-_'));
    }

    // function from firebase/jwt-php
    private function signatureFromDER($der, $keySize)
    {

        // OpenSSL returns the ECDSA signatures as a binary ASN.1 DER SEQUENCE
        list($offset, $_) = $this->readDER($der);
        list($offset, $r) = $this->readDER($der, $offset);
        list($offset, $s) = $this->readDER($der, $offset);

        // Convert r-value and s-value from signed two's compliment to unsigned
        // big-endian integers
        $r = \ltrim($r, "\x00");
        $s = \ltrim($s, "\x00");

        // Pad out r and s so that they are $keySize bits long
        $r = \str_pad($r, $keySize / 8, "\x00", STR_PAD_LEFT);
        $s = \str_pad($s, $keySize / 8, "\x00", STR_PAD_LEFT);

        return $r . $s;
    }

    // function from firebase/jwt-php
    private function readDER($der, $offset = 0)
    {
        $ASN1_BIT_STRING = 0x03;

        $pos = $offset;
        $size = \strlen($der);
        $constructed = (\ord($der[$pos]) >> 5) & 0x01;
        $type = \ord($der[$pos++]) & 0x1f;

        // Length
        $len = \ord($der[$pos++]);
        if ($len & 0x80) {
            $n = $len & 0x1f;
            $len = 0;
            while ($n-- && $pos < $size) {
                $len = ($len << 8) | \ord($der[$pos++]);
            }
        }

        // Value
        if ($type == $ASN1_BIT_STRING) {
            $pos++; // Skip the first contents octet (padding indicator)
            $data = \substr($der, $pos, $len - 1);
            $pos += $len - 1;
        } else if (!$constructed) {
            $data = \substr($der, $pos, $len);
            $pos += $len;
        } else {
            $data = null;
        }

        return array(
            $pos,
            $data
        );
    }

    public static  function parseJWT($id_token){
        $jwt = explode('.',$id_token);
        $payloadEncrypt = $jwt[1];
        $payload = base64_decode(str_replace(array('-', '_'), array('+', '/'), $payloadEncrypt));
        $payload = json_decode($payload);
        return $payload;
    }

}
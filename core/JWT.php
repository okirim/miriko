<?php


namespace App\core;





use Carbon\Carbon;

class JWT
{
    protected string $JWT_header;
    protected string $JWT_payload;
    protected string $JWT_signature;
    protected string $token;

    public function __construct()
    {

    }

    public function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    public function tokenHeader()
    {
        return json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
    }

    public function tokenPayload()
    {
        return json_encode([
            'user_id' => 1,
            'role' => 'admin',
            'exp' => 2593828222
        ]);

    }

    public function setJWTHeader()
    {
        $this->JWT_header = $this->base64UrlEncode($this->tokenHeader());
        return $this;
    }

    public function setJWTPayload()
    {
        $this->JWT_payload = $this->base64UrlEncode(($this->tokenPayload()));
        return $this;
    }

    public function signature()
    {
        $secret = getenv('JWT_SECRET') ?? "jwt-secret";
        return hash_hmac('sha256', $this->JWT_header . "." . $this->JWT_payload, $secret, true);

    }

    public function setJWTSignature()
    {
        $this->JWT_signature = $this->base64UrlEncode($this->signature());
        return $this;
    }

    public function setToken()
    {
        $this->token = $this->JWT_header . "." . $this->JWT_payload . "." . $this->JWT_signature;
        return $this;
    }

    public function getToken()
    {
        echo $this->token;
    }

    public function make()
    {
        $this->setJWTHeader()->setJWTPayload()->setJWTSignature()->setToken()->getToken();
    }

    public function validate($jwt)
    {
        $secret = getenv('JWT_SECRET');
        // split the token
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
        $expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
        $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);
        // build a signature based on the header and payload using the secret
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        // verify it matches the signature provided in the token
        $signatureValid = ($base64UrlSignature === $signatureProvided);

        if ($tokenExpired) {
            return  "Token has expired.";
        }

        if (!$signatureValid) {
           return 'The signature is NOT valid';
        }
        return json_decode($payload);
    }
}



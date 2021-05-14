<?php


namespace App\core\authentification;


use App\core\Exception;
use Carbon\Carbon;

class JWT
{
    protected static string $JWT_header;
    protected static string $JWT_payload;
    protected static string $JWT_signature;

    protected static function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    protected static function tokenHeader()
    {
        return json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
    }

    protected static function setJWTHeader(?array $tokenHeader)
    {
        try {
            $_tokenHeader = $tokenHeader ? json_encode($tokenHeader) : self::tokenHeader();
            self::$JWT_header = self::base64UrlEncode($_tokenHeader);
            return new static;
        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }

    }

    protected static function setJWTPayload(array $payload)
    {

        if (!array_key_exists('exp', $payload)) {
            $payload = array_merge($payload, ['exp' => self::expireIn()]);
        }
        $payload_str = json_encode($payload);
        self::$JWT_payload = self::base64UrlEncode($payload_str);
        return new static;
    }

    protected static function signature()
    {
        try {
            $secret = $_ENV['JWT_SECRET'];

            if (empty($secret)) {
                Exception::make('JWT_SECRET is empty', 401);
            }
            return hash_hmac('sha256', self::$JWT_header . "." . self::$JWT_payload, $secret, true);

        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }
    }

    protected static function setJWTSignature()
    {
        try {
            self::$JWT_signature = self::base64UrlEncode(self::signature());
            return new static;
        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }
    }


    protected static function getToken()
    {
        return self::$JWT_header . "." . self::$JWT_payload . "." . self::$JWT_signature;
    }

    public static function create(array $payload, ?array $header = [])
    {
        try {
            return self::setJWTHeader($header)::setJWTPayload($payload)::setJWTSignature()::getToken();
        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }

    }

    protected static function expireIn()
    {
        try {
            $expireIn = trim($_ENV['JWT_EXPIRE_IN']) ?? '6h';
            if (preg_match('/[0-9]+(h)$/', $expireIn)) {
                //h for hours
                $getVal = str_replace('h', '', $expireIn);
                if (is_numeric($getVal)) {
                    return Carbon::now()->addHours($getVal)->getTimestamp();
                }
                Exception::make("invalid JWT_EXPIRE_IN = $expireIn", 401);
            } elseif (preg_match('/[0-9]+(d)$/', $expireIn)) {
                //d for days
                $getVal = str_replace('d', '', $expireIn);
                if (is_numeric($getVal)) {
                    return Carbon::now()->addDays($getVal)->getTimestamp();
                }
                Exception::make("invalid JWT_EXPIRE_IN = $expireIn", 401);
            } elseif (preg_match('/[0-9]+(m)$/', $expireIn)) {
                //m for minutes
                $getVal = str_replace('m', '', $expireIn);
                if (is_numeric($getVal)) {
                    return Carbon::now()->addMinutes($getVal)->getTimestamp();
                }
                Exception::make("invalid JWT_EXPIRE_IN = $expireIn", 401);
            } else {

                Exception::make("invalid JWT_EXPIRE_IN = $expireIn", 401);
            }

        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }
    }

    public static function validateOrFail($jwt)
    {
        try {
            $secret = $_ENV['JWT_SECRET'];
            if (empty($secret)) {
                Exception::make('JWT_SECRET is empty', 401);
            }
            if (empty($jwt)) {
                Exception::make('TOKEN is empty', 401);
            }
            // split the token
            $tokenParts = explode('.', $jwt);
            $header = base64_decode($tokenParts[0]);
            $payload = base64_decode($tokenParts[1]);
            $signatureProvided = $tokenParts[2];

            // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
            $expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
            //$expiration =Carbon::createFromTimestamp(self::expireIn());
            $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);
            // build a signature based on the header and payload using the secret
            $base64UrlHeader = self::base64UrlEncode($header);
            $base64UrlPayload = self::base64UrlEncode($payload);
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
            $base64UrlSignature = self::base64UrlEncode($signature);
            // verify it matches the signature provided in the token
            $signatureValid = ($base64UrlSignature === $signatureProvided);

            if ($tokenExpired) {
                return "Token has expired.";
            }

            if (!$signatureValid) {
                return 'The signature is not valid';
            }
            return json_decode($payload);
        } catch (\Exception $err) {
            Exception::make($err->getMessage(), $err->getCode());
        }

    }

    public static function validate($jwt)
    {
        try {
            $secret = $_ENV['JWT_SECRET'];
            if (empty($secret)) {
                return false;
            }
            // split the token
            if (empty($jwt)) {
                return false;
            }
            $tokenParts = explode('.', $jwt);

            $header = base64_decode($tokenParts[0]);
            $payload = base64_decode($tokenParts[1]);
            $signatureProvided = $tokenParts[2];

            // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
            $expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
            //$expiration =Carbon::createFromTimestamp(self::expireIn());
            $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);
            // build a signature based on the header and payload using the secret
            $base64UrlHeader = self::base64UrlEncode($header);
            $base64UrlPayload = self::base64UrlEncode($payload);
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
            $base64UrlSignature = self::base64UrlEncode($signature);
            // verify it matches the signature provided in the token
            $signatureValid = ($base64UrlSignature === $signatureProvided);

            if ($tokenExpired) {
                return "Token has expired.";
            }

            if (!$signatureValid) {
                return 'The signature is not valid';
            }
            return json_decode($payload);
        } catch (\Exception $err) {
            return false;
        }

    }
}



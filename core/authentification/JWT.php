<?php


namespace App\core\authentification;


use App\core\exceptions\Exception;
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
            throw $err;
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
            $secret = self::JWT_SECRET();
            if (empty($secret)) {
                Exception::make('JWT SECRET KEY is null', 401);
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
            $secret = self::JWT_SECRET();
            if (empty($secret)) {
                Exception::make('JWT_SECRET is empty', 401);
            }
            if (empty($jwt)) {
                Exception::make('TOKEN is empty', 401);
            }
            // split the token
            $token = self::getTokenArray($jwt);
            $header = self::getTokenHeader($token[0]);
            $payload = self::getTokenPayload($token[1]);
            $signatureProvided = self::getTokenSignature($token[2]);

            // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
            $expiration = self::getTokenExpireIn($payload);
            $tokenExpired = self::isExpired($expiration);
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
            $token = self::getTokenArray($jwt);

            $header = self::getTokenHeader($token[0]);
            $payload = self::getTokenPayload($token[1]);
            $signatureProvided = self::getTokenSignature($token[2]);

            // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
            $expiration = self::getTokenExpireIn($payload);
            $tokenExpired = self::isExpired($expiration);
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

    /**
     * @param string $jwt
     * @return false|string[]
     */
    protected static function getTokenArray(string $jwt)
    {
        return explode('.', $jwt);
    }

    /**
     * @param $tokenParts
     * @return false|string
     */
    protected static function getTokenHeader($tokenParts)
    {
        return base64_decode($tokenParts);
    }

    /**
     * @param $tokenParts
     * @return false|string
     */
    protected static function getTokenPayload($tokenParts)
    {
        return base64_decode($tokenParts);
    }

    /**
     * @param $tokenParts
     * @return mixed
     */
    protected static function getTokenSignature($tokenParts)
    {
        return $tokenParts;
    }

    /**
     * @param string $payload
     * @return Carbon
     */
    protected static function getTokenExpireIn(string $payload): Carbon
    {
        return Carbon::createFromTimestamp(json_decode($payload)->exp);
    }

    /**
     * @param Carbon $expiration
     * @return bool
     */
    protected static function isExpired(Carbon $expiration): bool
    {
        return (Carbon::now()->diffInSeconds($expiration, false) < 0);
    }

    /**
     * @return mixed
     */
    protected static function JWT_SECRET(): mixed
    {
        return $_ENV['JWT_SECRET'];
    }
}



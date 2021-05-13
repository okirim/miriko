<?php


namespace App\core;


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

    public static function tokenHeader()
    {
        return json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
    }

    public static function setJWTHeader(?array $tokenHeader)
    {
        try {
            $_tokenHeader = $tokenHeader ? json_encode($tokenHeader) : self::tokenHeader();
            self::$JWT_header = self::base64UrlEncode($_tokenHeader);
            return new static;
        } catch (\Exception $err) {
            return Response::json_response_error($err->getMessage());
        }

    }

    public static function setJWTPayload(array $payload)
    {
        $_payload = array_merge($payload, ['exp' => self::expireIn()]);
        $payload_str = json_encode($_payload);
        self::$JWT_payload = self::base64UrlEncode($payload_str);
        return new static;
    }

    public static function signature()
    {
        try {
            $secret = $_ENV['JWT_SECRET'];

            if (empty($secret)) {
                Response::json_response_error('JWT_SECRET is empty');
            }
            return hash_hmac('sha256', self::$JWT_header . "." . self::$JWT_payload, $secret, true);

        } catch (\Exception $err) {
            Response::json_response_error($err->getMessage());
        }
    }

    public static function setJWTSignature()
    {
        try {
            self::$JWT_signature = self::base64UrlEncode(self::signature());
            return new static;
        } catch (\Exception $e) {
            Response::json_response_error($e->getMessage());
        }
    }


    public static function getToken()
    {
        return self::$JWT_header . "." . self::$JWT_payload . "." . self::$JWT_signature;
    }

    public static function create(array $payload, ?array $header = [])
    {
        try {
            return self::setJWTHeader($header)::setJWTPayload($payload)::setJWTSignature()::getToken();
        } catch (\Exception $e) {
            Response::json_response_error($e->getMessage());
        }

    }

    public static function expireIn()
    {
        try {
            $expireIn = trim($_ENV['JWT_EXPIRE_IN']) ?? '6h';
            if (preg_match('/[0-9]+(h)$/', $expireIn)) {
                //h for hours
                $getVal = str_replace('h', '', $expireIn);
                if (is_numeric($getVal)) {
                    return Carbon::now()->addHours($getVal)->getTimestamp();
                }
                return Response::json_response_error("invalid JWT_EXPIRE_IN = $expireIn");
            } elseif (preg_match('/[0-9]+(d)$/', $expireIn)) {
                //d for days
                $getVal = str_replace('d', '', $expireIn);
                if (is_numeric($getVal)) {
                    return Carbon::now()->addDays($getVal)->getTimestamp();
                }
                return Response::json_response_error("invalid JWT_EXPIRE_IN = $expireIn");
            } elseif (preg_match('/[0-9]+(m)$/', $expireIn)) {
                //m for minutes
                $getVal = str_replace('m', '', $expireIn);
                if (is_numeric($getVal)) {
                    return Carbon::now()->addMinutes($getVal)->getTimestamp();
                }
                return Response::json_response_error("invalid JWT_EXPIRE_IN = $expireIn");
            } else {
                return Response::json_response_error("invalid JWT_EXPIRE_IN = $expireIn");
            }

        } catch (\Exception $err) {
            return Response::json_response_error($err->getMessage());
        }
    }

    public static function validate($jwt)
    {
        try {
            $secret = $_ENV['JWT_SECRET'];
            if (empty($secret)) {
                Response::json_response_error('JWT_SECRET is empty');
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
                return 'The signature is NOT valid';
            }
            return json_decode($payload);
        } catch (\Exception $err) {
            return Response::json_response_error($err->getMessage());
        }
    }
}



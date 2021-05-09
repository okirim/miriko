<?php


namespace App\core;


class Response
{
    public static function json_response($data,string $status='success',int $code=200){
        http_response_code($code);
        header('Content-type: application/json');
        $response=[
            'statusCode'=>$code,
            'status'=>$status,
            'data'=>$data
        ];
        return json_encode($response);
    }
    public static function json_response_error($error,string $status='failed',int $code=500){
        http_response_code($code);
        header('Content-type: application/json');
        $response=[
            'statusCode'=>$code,
            'status'=>$status,
            'error'=>$error
        ];
        return json_encode($response);
    }
    public static function setStatusCode(int $code)
    {
        http_response_code($code);
    }
}
<?php


namespace App\core;


class Request
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $questionMark = strpos($path, '?');
        if (!$questionMark) return $path;
        return substr($path, 0, $questionMark);
    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody($param=null)
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($param){
           return  $body[$param];
        }
        return $body;
    }

    public function getParams()
    {

    }

    public function getQuery()
    {

    }

    public static function Body($param=null)
    {
        $req = self::instanceRequest();
        return $req->getBody($param);
    }

    protected static function instanceRequest()
    {
        return new Request();
    }
}











//public function getPath()
//    {
//        $path = $_SERVER['REQUEST_URI'] ?? '/';
//        $questionMark = strpos($path, '?');
//        if (!$questionMark) return $path;
//        return substr($path, 0, $questionMark);
//    }
//
//    public function getMethod()
//    {
//        return strtolower($_SERVER['REQUEST_METHOD']);
//    }

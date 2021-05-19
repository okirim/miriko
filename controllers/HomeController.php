<?php


namespace App\controllers;



use App\core\Request;
use App\core\View;

class HomeController extends BaseController
{
    public static function middleware()
    {
        return true;
    }
    public static function index(){
        return View::render('home');
//        $params=['name'=>'kadiro'];
//
//        return View::render('home',$params);
    }
    public function contact(){

        return View::render('contact');
    }
    public function login(){
         $body=Request::Body();
//        echo '<pre>';
//         var_dump($body);
//        echo '</pre>';
//        exit();
        return View::render('contact');
    }
}
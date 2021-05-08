<?php
namespace App\core;

class View {
   protected static string $layout='main';
    public static function render(string $view,array $params=[])
    {
        $layoutContent = self::layoutContent();
        $viewContent = self::renderOnlyView($view,$params);

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected static function renderOnlyView(string $view,array $params=[])
    {
        foreach($params as $key=>$value){
            $$key=$value;
    }

        ob_start();
        include_once Application::$ROOT_DIR. "/views/$view.php";
        return ob_get_clean();
    }

    protected static function layoutContent()
    {
        $layout=self::$layout;
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }
    public static function setLayout(string $layout=''){
        if($layout){
             self::$layout=$layout;
        }
          self::$layout;
    }
}
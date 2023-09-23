<?php 

    namespace App;

class Config{
    private static $css = null;
    public static $adminName = null;

    public static $admin_dir = 'admin';


    public static function home_url(){
        $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

        if(($_SERVER['SERVER_NAME'] == ('127.0.0.1' || 'localhost')) && ($_SERVER['SERVER_PORT'] == 8000)){
            $url = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        }else{
            $url = $_SERVER['SERVER_NAME'];
        }
        return $http . $url;
    }
    public static function asset($dir){
        return self::home_url() . "\/public/" . $dir;
    }

    public static function redirect(String $url){
        return header('Location: ' . $url);
    }

    public static function inc(String $dir){
        include_once realpath(__DIR__.DIRECTORY_SEPARATOR.'..') . "\/resource/views/" . str_replace('.','/',$dir) . ".php";
    }
    
    public static function putCss(...$arguments){
        self::$css = $arguments;
    }

    public static function getCss($css = []){
        return self::$css;
    }

}
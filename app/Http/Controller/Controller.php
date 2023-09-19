<?php 
    
    namespace App\Http\Controller;
    use App\Config;

class Controller extends Config{
    public static function view(String $dir,$var = [],$resource='/resource/views'){
        include_once __DIR__ . '/../../..' . str_replace('.','/',$resource.'/'.$dir) . '.php';
    }
}
<?php 
    
    namespace App\Http\Controller;
    use App\Config;

class Controller extends Config{
    public $home_url;
    public $admin_url;

    public function __construct(){
        $this->home_url = Config::home_url();
        $this->admin_url = Config::$admin_dir;
    }

    public static function view(String $dir,$var = [],$resource='/resource/views'){
        $config = new Controller();
        include_once __DIR__ . '/../../..' . str_replace('.','/',$resource.'/'.$dir) . '.php';
    }
    public static $public = __DIR__ . '/../../../public/';
}
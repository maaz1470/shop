<?php 

    namespace App\Http\Controller;
    use App\Http\Controller\Controller;
class DashboardController extends Controller{
    public static function dashboard(){
        self::view('backend.dashboard.dashboard');
    }

    public static function test(){
        echo 'Hello';
    }

    
}
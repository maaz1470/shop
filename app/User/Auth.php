<?php 

    namespace App\User;
    use App\Http\Controller\AdminController;
    use App\Config;
    use PDO;
class Auth{
    public static function check(){
        if(isset($_SESSION['client']) || isset($_COOKIE['client'])){
            AdminController::initialization();
            $encode_id = $_COOKIE['client'] ?? $_SESSION['client'];
            $id = base64_decode($encode_id);
            $connect = AdminController::class::$table;
            $tableName = AdminController::class::$tableName;
            $admin = $connect->prepare("SELECT id FROM $tableName WHERE id=:id");
            $admin->bindParam(':id',$id,PDO::PARAM_INT);
            $admin->execute();
            $data = $admin->fetch(PDO::FETCH_OBJ);
            if($data){
                if(($_SERVER['REQUEST_URI'] === '/admin/login') || ($_SERVER['REQUEST_URI'] == '/admin/registration')){
                    echo 'redirect to dashboard';
                    return Config::class::redirect('/admin/dashboard');
                }
            }
        }else{
            if(($_SERVER['REQUEST_URI'] !== '/admin/login') || ($_SERVER['REQUEST_URI'] !== '/admin/registration')){
                
                if(($_SERVER['REQUEST_URI'] === '/admin/login') || ($_SERVER['REQUEST_URI'] === '/admin/registration')){
                    return true;
                }else{
                    return Config::class::redirect('/admin/login');
                }
                
            }
        }
    }
}
<?php 

    namespace App\Http\Controller;

use App\Config;
use App\Connection\DB;
use Exception;
use PDO;

class AdminController extends Controller{
    public static $tableName = 'admins';
    public static $table;
    public static function initialization(){
        self::$table = DB::connection();
        $tableName = self::$tableName;
        self::$table->prepare("CREATE TABLE $tableName(id int NOT NULL PRIMARY KEY, name varchar(255) NOT NULL, email varchar(255) UNIQUE, password varchar(255) NOT NULL, profile_pic varchar(255) NULL, status TINYINT DEFAULT 0, role TINYINT DEFAULT 0)");
    }

    protected static function userCheck(){
        
        session_start();
        AdminController::class::initialization();
        if(isset($_SESSION['client']) || isset($_COOKIE['client'])){
            $encode_id = $_COOKIE['client'] ?? $_SESSION['client'];
            $id = base64_decode($encode_id);
            $tableName = self::$tableName;
            $connect = self::$table;
            $admin = $connect->prepare("SELECT id FROM $tableName WHERE id=:id");
            $admin->bindParam(':id',$id,PDO::PARAM_INT);
            $admin->execute();
            $fetch_admin = $admin->fetch(PDO::FETCH_OBJ);
            if($fetch_admin){
                if($fetch_admin->id == $id){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    public static function login(){
        self::view('backend.auth.sign-in');
        
    }

    public static function checkLogin($request){
        AdminController::class::initialization();
        $username = $request->username;
        $password = $request->password;
        $remember = $request->remember;
        $tableName = self::$tableName;
        
        if(!$username || !$password){
            echo json_encode([
                'status'    => 403,
                'message'   => 'All Field is Required.'
            ]);
            exit();
        }
        
        $connect = self::$table;
        
        try{
            $myAdmin = $connect->prepare("SELECT * FROM $tableName WHERE username=:username");
            $myAdmin->bindParam(':username',$username, PDO::PARAM_STR);
            $myAdmin->execute();
            $admin = $myAdmin->fetch(PDO::FETCH_OBJ);
            if($admin){
                if(password_verify($password, $admin->password)){
                    
                    echo json_encode([
                        'status'    => 200,
                        'message'   => 'Access Granted. Please wait your are automatically redirect your dashboard.'
                    ]);
                    
                    if($remember){
                        setcookie('client', base64_encode($admin->id), time()+999999999,'/');
                    }else{
                        session_start();
                        $_SESSION['client'] = base64_encode($admin->id);
                    }
                    
                    exit();
                }else{
                    throw new Exception('Access Denied. Username or Password not Matched');
                }
            }else{
                throw new Exception('Access Denied. Username of Password not found');
            }
        } catch (Exception $e) {
            echo json_encode([
                'status'    => 401,
                'message'   => explode('.',$e->getMessage())
            ]);
            exit();
        }

    }

    public static function register(){
        return self::view('backend.auth.sign-up');
    }

    public static function adminRegistration($request){
        $name = $request->name;
        $username = $request->username;
        $email = $request->email;
        $password = password_hash($request->password,PASSWORD_BCRYPT);
        $tableName = self::$tableName;
        $status = 1;
        $role = 1;
        
        try{
            if(!$name ||!$username || !$email || !$password){
                throw new Exception(json_encode([
                    'status'    => 401,
                    'message'   => 'All field is required'
                ]));
            }
    
            
            $connect = DB::connection();
            
            $findUsername = $connect->prepare("SELECT * FROM $tableName WHERE username=:username");
            $findUsername->bindParam(':username',$username,PDO::PARAM_STR);
            $findUsername->execute();
            if($findUsername->rowCount() >= 1){
                throw new Exception(json_encode([
                    'status'    => 403,
                    'message'   => 'Username allready exist.'
                ]));
            }else{
                $findEmail = $connect->prepare("SELECT * FROM $tableName WHERE email=:email");
                $findEmail->bindParam(':email',$email,PDO::PARAM_STR);
                $findEmail->execute();
                if($findEmail->rowCount() >= 1){
                    throw new Exception(json_encode([
                        'status'    => 403,
                        'message'   => 'Email allready exist.'
                    ]));
                }else{
                    $admin = $connect->prepare("INSERT INTO $tableName(name,username,email,password,role,status) VALUES(:name,:username,:email,:password,:role,:status)");
                    $admin->bindParam(':name',$name,PDO::PARAM_STR);
                    $admin->bindParam(':username',$username,PDO::PARAM_STR);
                    $admin->bindParam(':email',$email,PDO::PARAM_STR);
                    $admin->bindParam(':password',$password,PDO::PARAM_STR);
                    $admin->bindParam(':role',$role,PDO::PARAM_INT);
                    $admin->bindParam(':status',$status,PDO::PARAM_INT);
                    if($admin->execute()){
                        throw new Exception(json_encode([
                            'status'    => 200,
                            'message'   => 'Admin saved successfully'
                        ]));
                    }
                }
            }
            
            
        }catch(Exception $e){
            echo $e->getMessage();
        }

    }
}
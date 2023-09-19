<?php 

    namespace App\Connection;
    use PDO;
    use Exception;
class DB{
    private static $host = 'localhost';
    private static $user = 'root';
    private static $password = '';
    private static $dbname = 'ecommerce';
    public static function connection(String $host = 'localhost', String $user = 'root', String $password = '', String $dbname = 'ecommerce'){
        try{
            $sql = "mysql:host=$host;dbname=$dbname";
            if($conn = new PDO($sql,$user,$password)){
                return $conn;
            }else{
                throw new Exception($conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION));
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}
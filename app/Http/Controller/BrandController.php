<?php 

    namespace App\Http\Controller;
    use App\Connection\DB;
    use Exception;
    use PDO;

class BrandController extends Controller{

    protected static $table;
    protected static $tableName = 'brands';

    public static function initialization(){
        $db = DB::connection();
        self::$table = $db;
        $tableName = self::$tableName;
        $check_db = self::$table->prepare("SHOW TABLES LIKE :tableName");
        $check_db->bindParam(':tableName',self::$tableName,PDO::PARAM_STR);
        $check_db->execute();
        if($check_db->rowCount() == 0){
            $brand = self::$table->prepare("CREATE TABLE $tableName(
                id int AUTO_INCREMENT PRIMARY KEY,
                name varchar(255) NOT NULL,
                url varchar(255) NOT NULL UNIQUE,
                image varchar(255) NULL,
                status TINYINT(1) NOT NULL DEFAULT 0
            )");
            $brand->execute();
        }
    }

    protected static function createBrandUrl($url,$tableName){
        BrandController::class::initialization();
        $table = self::$table;
        $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($url)));
        $category = $table->prepare("SELECT id,url FROM $tableName WHERE url=:url");
        $allCategories = $table->prepare("SELECT id FROM $tableName");
        $allCategories->execute();
        $category->bindParam(':url',$slug,PDO::PARAM_STR);
        $category->execute();
        if($category->rowCount() > 0){
            $slug = $slug . '-' . $allCategories->rowCount();
        }
        return $slug;
    }
    public static function brands(){
        BrandController::class::initialization();
        $table = self::$table;
        $tableName = self::$tableName;

        $fetch = $table->prepare("SELECT * FROM $tableName");
        $fetch->execute();
        $brands = $fetch->fetchAll(PDO::FETCH_OBJ);
        return self::view('backend.brands.brands',compact('brands'));
    }

    public static function addBrand(){
        return self::view('backend.brands.add-brand');
    }

    protected static function createImage($image){
        $path = null;
        if($image != 'null'){
            $image = (object)$_FILES['brand_image'];
            $name = time() . '_' . uniqid() . '_rh' . '.jpg';
            $dir = self::$public . 'brands/' . $name;
            move_uploaded_file($image->tmp_name,$dir);
            return $name;
        }
        return $path;
    }

    public static function createBrand($request){
        
        BrandController::class::initialization();
        $table = self::$table;
        $tableName = self::$tableName;

        

        try{
            if($request->name != '' && $request->status != ''){
                $name = $request->name;
                $status = $request->status;
                $image = self::createImage($request->brand_image);
                
                $url = self::createBrandUrl($request->name,$tableName);
                $brand = $table->prepare("INSERT INTO $tableName(name,url,image,status) VALUES(:name,:url,:image,:status)");
                $brand->bindParam(':name',$name,PDO::PARAM_STR);
                $brand->bindParam(':url',$url,PDO::PARAM_STR);
                $brand->bindParam(':image',$image,PDO::PARAM_STR);
                $brand->bindParam(':status',$status,PDO::PARAM_STR);
                if($brand->execute()){
                    echo json_encode([
                        'status'    => 200,
                        'message'   => 'Brand Successfully Saved'
                    ]);
                    exit();
                }else{
                    throw new Exception("Something went wrong. Please try again.");
                }
            }else{
                throw new Exception("All Field is Required");
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    => 401,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function statusChange($request){
        BrandController::class::initialization();
        $tableName = self::$tableName;
        $table = self::$table;
        try{
            $status = $table->prepare("UPDATE $tableName SET status=:status WHERE id=:id");
            $status->bindParam(':status',$request->status,PDO::PARAM_INT);
            $status->bindParam(':id',$request->id,PDO::PARAM_INT);
            if($status->execute()){
                echo json_encode([
                    'status'    => 200,
                    'message'   => 'Status Changed Successfully'
                ]);
                exit();
            }else{
                throw new Exception("Something went wrong. Please try again or contact your developer.");
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    => 403,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function delete($request){
        BrandController::class::initialization();
        try{
            $tableName = self::$tableName;
            $table = self::$table;
            $category = $table->prepare("DELETE FROM $tableName WHERE id=:id");
            $category->bindParam(':id',$request->id,PDO::PARAM_INT);
            if($category->execute()){
                echo json_encode([
                    'status'    => 200,
                    'message'   => 'Brand Delete Successfully'
                ]);
                exit();
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    => 401,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function editBrand($id){
        BrandController::class::initialization();
        $table = self::$table;
        $tableName = self::$tableName;
        $categoryTable = self::$tableName;
        $subcategories = $table->prepare("SELECT * FROM $tableName WHERE id=:id");
        $subcategories->bindParam(':id',$id,PDO::PARAM_INT);
        $subcategories->execute();
        $subcategory = $subcategories->fetch(PDO::FETCH_OBJ);
        if($subcategory){
            $category = $table->prepare("SELECT * FROM $categoryTable WHERE status=1");
            $category->execute();
            $categories = $category->fetchAll(PDO::FETCH_OBJ);
            return self::view('backend.category.edit-subcategory',compact('subcategory','categories'));
        }else{
            self::redirect('/admin/sub-categories');
        }

    }


}
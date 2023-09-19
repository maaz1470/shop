<?php 

    namespace App\Http\Controller;
    use App\Connection\DB;
    use Exception;
    use PDO;
class CategoryController extends Controller{

    protected static $tableName = 'categories';
    protected static $subCategoryTable = 'subcategories';

    protected static $subSubCategoryTable = 'sub_sub_categories';

    protected static $table;

    protected static function initialization(){
        $tableName = self::$tableName;
        $subcategory = self::$subCategoryTable;
        self::$table = DB::connection();
        $sql = "CREATE TABLE $tableName(
            id int AUTO_INCREMENT PRIMARY KEY,
            name varchar(255) NOT NULL,
            url varchar(255) NOT NULL UNIQUE,
            description text NULL,
            status TINYINT NOT NULL DEFAULT 0
        )";

        $check = self::$table->prepare("SHOW TABLES LIKE :tableName");
        $check->bindParam(':tableName',$tableName,PDO::PARAM_STR);
        $check->execute();
        if($check->rowCount() == 0){
            $createTable = self::$table->prepare($sql);
            $createTable->execute();
        }

        $subcategory_sql = "CREATE TABLE $subcategory(
            id int AUTO_INCREMENT PRIMARY KEY,
            name varchar(255) NOT NULL,
            parent_id int NOT NULL DEFAULT 0,
            url varchar(255) NOT NULL UNIQUE,
            description text NULL,
            status TINYINT NOT NULL DEFAULT 0
        )";
        $check_subcategory = self::$table->prepare("SHOW TABLES LIKE :tableName");
        $check_subcategory->bindParam(':tableName',$subcategory,PDO::PARAM_STR);
        $check_subcategory->execute();
        if($check_subcategory->rowCount() == 0){
            $subcategory = self::$table->prepare($subcategory_sql);
            $subcategory->execute();
        }
        
    }

    public static function category(){
        CategoryController::class::initialization();
        $tableName = self::$tableName;
        $table = self::$table;
        $category = $table->prepare("SELECT * FROM $tableName");
        $category->execute();
        $categories = $category->fetchAll(PDO::FETCH_OBJ);
        return self::view('backend.category.categories',compact('categories'));
    }

    public static function addCategory(){
        return self::view('backend.category.add-category');
    }

    private static function createCategoryUrl($url,$tableName){
        
        CategoryController::class::initialization();
        $table = self::$table;
        $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($url)));
            $category = $table->prepare("SELECT id,url FROM $tableName WHERE url=:url");
            $allCategories = $table->prepare("SELECT id FROM $tableName");
            $allCategories->execute();
            $category->bindParam(':url',$url,PDO::PARAM_STR);
            $category->execute();
            if($category->rowCount() > 0){
                $slug = $slug . '-' . $allCategories->rowCount();
            }
            return $slug;
    }

    public static function storeCategory($request){
        
        try {
            if($request->name != '' && $request->status != ''){
                $name = $request->name;
                $description = $request->description;
                $status = $request->status;
                $url = self::createCategoryUrl($request->name,self::$tableName);
                $table = self::$table;
                $tableName = self::$tableName;

                $category = $table->prepare("INSERT INTO $tableName(name,url,description,status) VALUES(:name,:url,:description,:status)");
                $category->bindParam(':name',$name,PDO::PARAM_STR);
                $category->bindParam(':url',$url,PDO::PARAM_STR);
                $category->bindParam(':description',$description,PDO::PARAM_STR);
                $category->bindParam(':status',$status,PDO::PARAM_INT);
                if($category->execute()){
                    echo json_encode([
                        'status'    => 200,
                        'message'   => 'Category Saved Successfully.'
                    ]);
                    exit();
                }else{
                    throw new Exception('Something went wrong. Please try again.');
                }
            }else {
                echo json_encode([
                    'status'    => 401,
                    'message'   => "All Field is required."
                ]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode([
                'status'    => 403,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function editCategory($id){
        CategoryController::class::initialization();
        $tableName = self::$tableName;
        $table = self::$table;
        $categories = $table->prepare("SELECT * FROM $tableName WHERE id=:id");
        $categories->bindParam(':id',$id,PDO::PARAM_INT);
        $categories->execute();
        $category = $categories->fetch(PDO::FETCH_OBJ);
        if($category){
            return self::view('backend.category.edit-category',compact('category'));
        }else{
            self::redirect('/admin/category');
        }
    }

    public static function updateCategory($request){
        CategoryController::class::initialization();
        $tableName = self::$tableName;
        $table = self::$table;
        try{
            $table->beginTransaction();
            if($request->name != '' && $request->url != '' && $request->status != ''){
                $check_url = $table->prepare("SELECT * FROM $tableName WHERE id=:id");
                $check_url->bindParam(':id',$request->id,PDO::PARAM_INT);
                $check_url->execute();
                $slug = $check_url->fetch(PDO::FETCH_OBJ);
                if($request->url != $slug->url){
                    $url = self::createCategoryUrl($request->url,$tableName);
                }else{
                     $url = $request->url;
                }
                $updateCategory = $table->prepare("UPDATE $tableName SET name=:name, url=:url, description=:description, status=:status WHERE id=:id");
                $updateCategory->bindParam(':name',$request->name, PDO::PARAM_STR);
                $updateCategory->bindParam(':url',$url, PDO::PARAM_STR);
                $updateCategory->bindParam(':description',$request->description, PDO::PARAM_STR);
                $updateCategory->bindParam(':status',$request->status, PDO::PARAM_INT);
                $updateCategory->bindParam(':id',$request->id,PDO::PARAM_INT);
                if($updateCategory->execute()){
                    echo json_encode([
                        'status'    => 200,
                        'message'   => 'Category Update Successfully'
                    ]);
                    $table->commit();
                    exit();
                }else{
                    echo json_encode([
                        'status'    => 403,
                        'message'   => 'Something went wrong. Please try again.'
                    ]);
                    $table->rollBack();
                    exit();
                }

            }else{
                echo json_encode([
                    'status'    => 401,
                    'message'   => 'All Field is required'
                ]);
                exit();
            }
        }catch(Exception $e){
            $table->rollBack();
            echo json_encode([
                'status'    => 403,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function statusChange($request){
        CategoryController::class::initialization();
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

    public static function deleteCategory($request){
        CategoryController::class::initialization();
        try{
            $tableName = self::$tableName;
            $table = self::$table;
            $category = $table->prepare("DELETE FROM $tableName WHERE id=:id");
            $category->bindParam(':id',$request->id,PDO::PARAM_INT);
            if($category->execute()){
                echo json_encode([
                    'status'    => 200,
                    'message'   => 'Category Delete Successfully'
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

    public static function subCategories(){
        CategoryController::class::initialization();
        $tableName = self::$subCategoryTable;
        $table = self::$table;
        $category = $table->prepare("SELECT categories.id as category_id, categories.name as category_name, subcategories.* FROM $tableName LEFT JOIN categories ON subcategories.parent_id=categories.id");
        $category->execute();
        $subcategories = $category->fetchAll(PDO::FETCH_OBJ);
        return self::view('backend.category.subcategory',compact('subcategories'));
    }

    public static function addSubCategory(){
        CategoryController::class::initialization();
        $tableName = self::$tableName;
        $table = self::$table;
        $category = $table->prepare("SELECT id,name FROM $tableName WHERE status=1");
        $category->execute();
        $parents = $category->fetchAll(PDO::FETCH_OBJ);

        return self::view('backend.category.addsubcategory',compact('parents'));
    }

    public static function submitSubCategory($request){
        CategoryController::class::initialization();
        $tableName = self::$subCategoryTable;
        $table = self::$table;
        try{
            if($request->name != '' && $request->parent_category != '' && $request->status != ''){
                $name = $request->name;
                $parent_category = $request->parent_category;
                $description = $request->description;
                $status = $request->status;
                $url = self::createCategoryUrl($name,$tableName);
                $subcategory = $table->prepare("INSERT INTO $tableName(name,parent_id,url,description,status) VALUES(:name,:parent_id,:url,:description,:status)");
                $subcategory->bindParam(':name',$name,PDO::PARAM_STR);
                $subcategory->bindParam(':parent_id',$parent_category,PDO::PARAM_INT);
                $subcategory->bindParam(':url',$url,PDO::PARAM_STR);
                $subcategory->bindParam(':description',$description,PDO::PARAM_STR);
                $subcategory->bindParam(':status',$status,PDO::PARAM_INT);
                if($subcategory->execute()){
                    echo json_encode([
                        'status'    => 200,
                        'message'   => 'Category Added Successfully'
                    ]);
                    exit();
                }else{
                    throw new Exception("Something went wrong. Please try again.");
                }
            }else{
                throw new Exception('All Field is Required');
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    => 403,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function editSubCategory($id){
        CategoryController::class::initialization();
        $tableName = self::$subCategoryTable;
        $table = self::$table;
        $categoryTable = self::$tableName;
        $subcategories = $table->prepare("SELECT * FROM $tableName WHERE id=:id");
        $subcategories->bindParam(':id',$id,PDO::PARAM_INT);
        $subcategories->execute();
        $subcategory = $subcategories->fetch(PDO::FETCH_OBJ);
        $category = $table->prepare("SELECT * FROM $categoryTable WHERE status=1");
        $category->execute();
        $categories = $category->fetchAll(PDO::FETCH_OBJ);
        return self::view('backend.category.edit-subcategory',compact('subcategory','categories'));
    }

    public static function updateSubCategory($request){
        CategoryController::class::initialization();
        $tableName = self::$subCategoryTable;
        $table = self::$table;
        try{
            if($request->name != '' && $request->url != '' && $request->description != '' && $request->parent_id != '' && $request->status != ''){
                $name = $request->name;
                $check_url = $table->prepare("SELECT id,url FROM $tableName WHERE id=:id");
                $check_url->bindParam(':id',$request->id,PDO::PARAM_INT);
                $check_url->execute();
                $urlData = $check_url->fetch(PDO::FETCH_OBJ);
                if($urlData->url != $request->url){
                    $url = self::createCategoryUrl($request->url,$table);
                }else{
                    $url = $request->url;
                }
                
                $description = $request->description;
                $parent_id = $request->parent_id;
                $status = $request->status;
                $category = $table->prepare("UPDATE $tableName SET name=:name, url=:url, description=:description, parent_id=:parent_id, status=:status WHERE id=:id");
                $category->bindParam(':name',$name,PDO::PARAM_STR);
                $category->bindParam(':url',$url,PDO::PARAM_STR);
                $category->bindParam(':description',$description,PDO::PARAM_STR);
                $category->bindParam(':parent_id',$parent_id,PDO::PARAM_INT);
                $category->bindParam(':status',$status,PDO::PARAM_INT);
                $category->bindParam(':id',$request->id,PDO::PARAM_INT);
                if($category->execute()){
                    echo json_encode([
                        'status'    => 200,
                        'message'   => 'Category Update Successfully'
                    ]);
                    exit();
                }else{
                    throw new Exception("Something went wrong. Please try again.");
                }
            }else{
                throw new Exception("All Field is Required.");
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    => 401,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function subCategoryStatusChange($request){
        CategoryController::class::initialization();
        $table = self::$table;
        $tableName = self::$subCategoryTable;
        try{
            
            $subCategory = $table->prepare("UPDATE $tableName SET status=:status WHERE id=:id");
            $subCategory->bindParam(':status',$request->status,PDO::PARAM_INT);
            $subCategory->bindParam(':id',$request->id,PDO::PARAM_INT);
            if($subCategory->execute()){
                echo json_encode([
                    'status'    => 200,
                    'message'   => "Subcategory update successfully"
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

    public static function subCategoryDelete($request){
        CategoryController::class::initialization();
        $table = self::$table;
        $tableName = self::$subCategoryTable;

        try{
            $delete = $table->prepare("DELETE FROM $tableName WHERE id=:id");
            $delete->bindParam(':id',$request->id,PDO::PARAM_INT);
            if($delete->execute()){
                echo json_encode([
                    'status'    => 200,
                    'message'   => "Subcategory Delete successfully"
                ]);
                exit();
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    => 401,
                'message'   => $e->getMessage()
            ]);
            exit();
        }
    }

    public static function subSubCategories(){
        CategoryController::class::initialization();
        
    }

}
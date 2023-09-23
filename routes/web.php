<?php

  require_once __DIR__ . '/../vendor/autoload.php';

use App\Config;
use App\Http\Controller\AdminController;
use App\Http\Controller\BrandController;
use App\Http\Controller\CategoryController;
use App\Http\Controller\DashboardController;
use App\User\Auth;
use Package\Route\Routes as Route;

  Route::initialization();

  $admin_dir = Config::$admin_dir;

  Route::post('/' . $admin_dir . '/check-login',function($post){
    AdminController::class::checkLogin($post);
  });

  Route::post('/' . $admin_dir . '/admin-registration',function($data){
    AdminController::class::adminRegistration($data);
  });
  
  // print_r(array_search('admin',explode('/',$_SERVER['REQUEST_URI'])));
  if(array_search($admin_dir,explode('/',$_SERVER['REQUEST_URI']))){
    Auth::check();
    Route::get('/' . $admin_dir . '/login',function(){
      AdminController::class::login();
    });
  
    
  
    Route::get('/' . $admin_dir . '/registration',function(){
      AdminController::class::register();
    });
  
    
  

    Route::get('/' . $admin_dir . '/dashboard',function(){
      DashboardController::class::dashboard();
    });



    // Category Section
    
    Route::get('/' . $admin_dir . '/category',function(){
      CategoryController::class::category();
    });
    Route::get('/' . $admin_dir . '/category/add-category',function(){
      CategoryController::class::addCategory();
    });

    Route::post('/' . $admin_dir . '/category/submitAddCategory',function($request){
      CategoryController::class::storeCategory($request);
    });
    Route::get('/' . $admin_dir . '/category/edit-category/$id',function($id){
      CategoryController::class::editCategory($id);
    });

    Route::post('/' . $admin_dir . '/category/updateCategory',function($request){
      CategoryController::class::updateCategory($request);
    });

    Route::post('/' . $admin_dir . '/category/statuChange',function($request){
      CategoryController::class::statusChange($request);
    });

    Route::post('/' . $admin_dir . '/category/delete',function($request){
      CategoryController::class::deleteCategory($request);
    });


    // Sub Categories Section

    Route::get('/' . $admin_dir . '/sub-categories',function(){
      CategoryController::class::subCategories();
    });
    Route::get('/' . $admin_dir . '/sub-category/add-subcategory',function(){
      CategoryController::class::addSubCategory();
    });

    Route::post('/' . $admin_dir . '/sub-category/submitSubCategory',function($request){
      CategoryController::class::submitSubCategory($request);
    });
    Route::get('/' . $admin_dir . '/sub-category/edit-subcategory/$id',function($id){
      CategoryController::class::editSubCategory($id);
    });
    Route::post('/' . $admin_dir . '/sub-category/updateSubCategory',function($request){
      CategoryController::class::updateSubCategory($request);
    });

    Route::post('/' . $admin_dir . '/sub-category/statusChange',function($request){
      CategoryController::class::subCategoryStatusChange($request);
    });
    Route::post('/' . $admin_dir . '/sub-category/delete',function($request){
      CategoryController::class::subCategoryDelete($request);
    });

    // Sub Sub Category

    Route::get('/' . $admin_dir . '/sub-sub-categories',function(){
      CategoryController::class::subSubCategories();
    });

    Route::get('/' . $admin_dir . '/sub-sub-category/add-subcategory',function(){
      CategoryController::class::addSubSubCategory();
    });

    Route::post('/' . $admin_dir . '/sub-sub-category/submitSubCategory',function($request){
      CategoryController::class::submitSubSubCategory($request);
    });

    Route::post('/' . $admin_dir . '/sub-sub-category/statusChange',function($request){
      CategoryController::class::subSubCategoryStatusChange($request);
    });

    Route::post('/' . $admin_dir . '/subs-sub-category/delete',function($request){
      CategoryController::class::deleteSubSubCategory($request);
    });

    Route::get('/' . $admin_dir . '/sub-sub-category/edit-subcategory/$id',function($id){
      CategoryController::class::editSubSubCategory($id);
    });

    Route::post('/' . $admin_dir . '/sub-sub-category/updateSubCategory',function($request){
      CategoryController::class::updateSubSubCategory($request);
    });



    // Brand Section Start Here

    Route::get('/' . $admin_dir . '/brands',function(){
      BrandController::class::brands();
    });

    Route::post('/' . $admin_dir . '/brand/create',function($request){
      BrandController::class::createBrand($request);
    });
    
    Route::get('/' . $admin_dir . '/add-brand',function(){
      BrandController::class::addBrand();
    });
    Route::post('/' . $admin_dir . '/brand/statusChange',function($request){
      BrandController::class::statusChange($request);
    });
    Route::post('/' . $admin_dir . '/brand/delete',function($request){
      BrandController::class::delete($request);
    });

    Route::get('/' . $admin_dir . '/brand/edit/$id',function($id){
      BrandController::class::editBrand($id);
    });


  }

  
Route::any('/404',function(){
  echo 'This is not found page';
});
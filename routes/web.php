<?php

  require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Controller\AdminController;
use App\Http\Controller\CategoryController;
use App\Http\Controller\DashboardController;
use App\User\Auth;
use Package\Route\Routes as Route;

  Route::initialization();
  Route::post('/admin/check-login',function($post){
    AdminController::class::checkLogin($post);
  });

  Route::post('/admin/admin-registration',function($data){
    AdminController::class::adminRegistration($data);
  });
  
  // print_r(array_search('admin',explode('/',$_SERVER['REQUEST_URI'])));
  if(array_search('admin',explode('/',$_SERVER['REQUEST_URI']))){
    Auth::check();
    Route::get('/admin/login',function(){
      AdminController::class::login();
    });
  
    
  
    Route::get('/admin/registration',function(){
      AdminController::class::register();
    });
  
    
  

    Route::get('/admin/dashboard',function(){
      DashboardController::class::dashboard();
    });



    // Category Section
    
    Route::get('/admin/category',function(){
      CategoryController::class::category();
    });
    Route::get('/admin/category/add-category',function(){
      CategoryController::class::addCategory();
    });

    Route::post('/admin/category/submitAddCategory',function($request){
      CategoryController::class::storeCategory($request);
    });
    Route::get('/admin/category/edit-category/$id',function($id){
      CategoryController::class::editCategory($id);
    });

    Route::post('/admin/category/updateCategory',function($request){
      CategoryController::class::updateCategory($request);
    });

    Route::post('/admin/category/statuChange',function($request){
      CategoryController::class::statusChange($request);
    });

    Route::post('/admin/category/delete',function($request){
      CategoryController::class::deleteCategory($request);
    });


    // Sub Categories Section

    Route::get('/admin/sub-categories',function(){
      CategoryController::class::subCategories();
    });
    Route::get('/admin/sub-category/add-subcategory',function(){
      CategoryController::class::addSubCategory();
    });

    Route::post('/admin/sub-category/submitSubCategory',function($request){
      CategoryController::class::submitSubCategory($request);
    });
    Route::get('/admin/sub-category/edit-subcategory/$id',function($id){
      CategoryController::class::editSubCategory($id);
    });
    Route::post('/admin/sub-category/updateSubCategory',function($request){
      CategoryController::class::updateSubCategory($request);
    });

    Route::post('/admin/sub-category/statusChange',function($request){
      CategoryController::class::subCategoryStatusChange($request);
    });
    Route::post('/admin/sub-category/delete',function($request){
      CategoryController::class::subCategoryDelete($request);
    });

    // Sub Sub Category

    Route:get('/admin/sub-sub-categories',function(){
      CategoryController::class::subSubCategories();
    });

  }

  
Route::any('/404',function(){
  echo 'This is not found page';
});
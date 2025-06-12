<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ACLController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|   
*/

    // Public
    Route::get('/', [PostController::class, 'public'])->name('home');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');


    Route::get('/login', [AuthController::class, 'showLogin'])->name('showLogin');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('showRegister');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


// Post routes
Route::middleware(['auth'])->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/create', [PostController::class, 'create']); 
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::get('/posts/all', [PostController::class, 'allPosts'])->name('posts.all');




    // Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('acl', [ACLController::class, 'index']);
    Route::post('acl/assign', [ACLController::class, 'assign']);
    Route::put('/acl/update-user-permissions', [ACLController::class, 'updateUserPermissions'])->name('acl.updateUserPermissions');
    Route::post('/user/{user}/update-role', [ACLController::class, 'updateUserRole'])->name('user.updateRole');
    Route::post('/acl/update-user-role/{user}', [ACLController::class, 'updateUserRole'])->name('acl.updateUserRole');
    Route::post('/roles/permissions/update-all', [ACLController::class, 'updateRolePermissions'])->name('role_permissions.update_all');
    Route::get('/admin/users', [ACLController::class, 'manageUsers'])->name('admin.users.index');
    Route::post('/admin/role-permissions/update', [ACLController::class, 'updateRolePermissions'])->name('role_permissions.update');



    Route::get('/admin/users/{id}', [ACLController::class, 'show'])->name('admin.users.show');
    Route::post('/admin/users/{id}', [ACLController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [ACLController::class, 'destroy'])->name('admin.users.destroy');

    // Route::get('/admin/users', [UserController::class, 'manageUsers'])->name('admin.manageUsers');
    




    // Admin ACL Management
    Route::get('/users', [ACLController::class, 'index']);
    Route::post('/users/{user}/assign-role', [ACLController::class, 'assignRole']);
    Route::post('/users/{user}/assign-permission', [ACLController::class, 'assignPermission']);
    Route::resource('/roles', RoleController::class);
    Route::resource('/permissions', PermissionController::class);
    
    Route::post('/posts/approve-all', [PostController::class, 'approveAll'])->name('posts.approveAll');
    
    
    Route::get('/post/pending', [PostController::class, 'pending'])->name('posts.pending');
    Route::post('/posts/{post}/approve', [PostController::class, 'approve']);
    Route::post('/posts/{post}/decline', [PostController::class, 'decline']);

    // Route::patch('/user/{id}/role', [UserController::class, 'updateRole'])->name('user.updateRole');
    // Route::post('/acl/update-user-permissions', [ACLController::class, 'updateUserPermissions'])->name('acl.updateUserPermissions');


});

    // Route::get('/admin/users', [ACLController::class, 'manageUsers'])->name('admin.users.index');
   

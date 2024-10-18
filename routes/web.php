<?php

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

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middlewares\RoleMiddleware;

Route::get('/', function () {
    return view('auth.login');
});

// Login & logout function
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::middleware('auth')->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('home/store', 'HomeController@store')->name('home.store');
    Route::get('home/{id}/edit', 'HomeController@edit')->name('home.edit');
    Route::put('home/{id}', 'HomeController@update')->name('home.update');
    Route::delete('home/{id}', 'HomeController@destroy')->name('home.destroy');

    //Campus
    Route::get('campus', 'CampusController@index')->name('campus');
    Route::get('campus/view/{id}', 'CampusController@show')->name('campus.show');
    Route::get('/campus/search', 'CampusController@search')->name('campus.search');

    //Announcement
    Route::get('announcement', 'AnnouncementController@index')->name('announcement');
    Route::get('announcement/view/{id}', 'AnnouncementController@show')->name('announcement.show');
    Route::get('/announcement/search', 'AnnouncementController@search')->name('announcement.search');

    //Position
    Route::get('position', 'PositionController@index')->name('position');
    Route::get('position/view/{id}', 'PositionController@show')->name('position.show');
    Route::get('/position/search', 'PositionController@search')->name('position.search');

    //Computer Lab
    Route::get('computer-lab', 'ComputerLabController@index')->name('computer-lab');
    Route::get('computer-lab/view/{id}', 'ComputerLabController@show')->name('computer-lab.show');
    Route::get('/computer-lab/search', 'ComputerLabController@search')->name('computer-lab.search');
    Route::get('computer-lab/{id}/history', 'ComputerLabController@history')->name('computer-lab.history');

    //Software
    Route::get('software', 'SoftwareController@index')->name('software');
    Route::get('software/view/{id}', 'SoftwareController@show')->name('software.show');
    Route::get('/software/search', 'SoftwareController@search')->name('software.search');

    //Work Checklist
    Route::get('work-checklist', 'WorkChecklistController@index')->name('work-checklist');
    Route::get('work-checklist/view/{id}', 'WorkChecklistController@show')->name('work-checklist.show');
    Route::get('/work-checklist/search', 'WorkChecklistController@search')->name('work-checklist.search');

    //Lab Checklist
    Route::get('lab-checklist', 'LabChecklistController@index')->name('lab-checklist');
    Route::get('lab-checklist/view/{id}', 'LabChecklistController@show')->name('lab-checklist.show');
    Route::get('/lab-checklist/search', 'LabChecklistController@search')->name('lab-checklist.search');

    // User Profile
    Route::get('profile/{id}', 'UserProfileController@show')->name('profile.show');
    Route::get('profile/{id}/edit', 'UserProfileController@edit')->name('profile.edit');
    Route::put('profile/{id}', 'UserProfileController@update')->name('profile.update');
    Route::get('profile/{id}/change-password', 'UserProfileController@changePasswordForm')->name('profile.change-password');
    Route::post('profile/{id}/change-password', 'UserProfileController@changePassword')->name('profile.update-password');

    // Lab Management Record
    Route::get('lab-management', 'LabManagementController@index')->name('lab-management');
    Route::get('lab-management/create', 'LabManagementController@create')->name('lab-management.create');
    Route::post('lab-management/store', 'LabManagementController@store')->name('lab-management.store');
    Route::get('lab-management/{id}/edit', 'LabManagementController@edit')->name('lab-management.edit');
    Route::post('lab-management/{id}', 'LabManagementController@update')->name('lab-management.update');
    Route::get('lab-management/view/{id}', 'LabManagementController@show')->name('lab-management.show');
    Route::get('/lab-management/search', 'LabManagementController@search')->name('lab-management.search');
    Route::post('lab-management/submit/{id}', 'LabManagementController@submit')->name('lab-management.submit');
    Route::get('lab-management/{id}/report-detail', 'LabManagementController@reportDetail')->name('lab-management.report-detail');
    Route::get('lab-management/{id}/check-detail', 'LabManagementController@checkDetail')->name('lab-management.check-detail');
    Route::post('lab-management/check/{id}', 'LabManagementController@check')->name('lab-management.check');
    Route::delete('lab-management/{id}', 'LabManagementController@destroy')->name('lab-management.destroy');
    Route::get('/lab-management/trash', 'LabManagementController@trashList')->name('lab-management.trash');
    Route::get('/lab-management/{id}/restore', 'LabManagementController@restore')->name('lab-management.restore');
    Route::delete('/lab-management/{id}/force-delete', 'LabManagementController@forceDelete')->name('lab-management.forceDelete');

    // Lab Management - Maintenance Record
    Route::get('lab-management/{labManagement}/maintenance-records', 'MaintenanceRecordController@index')->name('lab-management.maintenance-records');
    Route::get('lab-management/{labManagement}/maintenance-records/create', 'MaintenanceRecordController@create')->name('lab-management.maintenance-records.create');
    Route::post('lab-management/{labManagement}/maintenance-records/store', 'MaintenanceRecordController@store')->name('lab-management.maintenance-records.store');
    Route::get('lab-management/{labManagement}/maintenance-records/{id}/edit', 'MaintenanceRecordController@edit')->name('lab-management.maintenance-records.edit');
    Route::post('lab-management/{labManagement}/maintenance-records/{id}', 'MaintenanceRecordController@update')->name('lab-management.maintenance-records.update');
    Route::get('lab-management/{labManagement}/maintenance-records/show/{id}', 'MaintenanceRecordController@show')->name('lab-management.maintenance-records.show');
    Route::get('lab-management/{labManagement}/maintenance-records/search', 'MaintenanceRecordController@search')->name('lab-management.maintenance-records.search');
    Route::delete('lab-management/{labManagement}/maintenance-records/{id}', 'MaintenanceRecordController@destroy')->name('lab-management.maintenance-records.destroy');
    Route::get('lab-management/{labManagement}/maintenance-records/trash', 'MaintenanceRecordController@trashList')->name('lab-management.maintenance-records.trash');
    Route::get('lab-management/{labManagement}/maintenance-records/{id}/restore', 'MaintenanceRecordController@restore')->name('lab-management.maintenance-records.restore');
    Route::delete('lab-management/{labManagement}/maintenance-records/{id}/force-delete', 'MaintenanceRecordController@forceDelete')->name('lab-management.maintenance-records.forceDelete');

    // Report
    Route::get('report', 'ReportController@index')->name('report');
    Route::get('report/view/{id}', 'ReportController@show')->name('report.show');
    Route::get('/report/{id}/pdf', 'ReportController@downloadPdf')->name('report.pdf');

    // Report
    Route::get('yearly-report', 'YearlyReportController@index')->name('yearly-report');

    Route::middleware([RoleMiddleware::class . ':Superadmin'])->group(function () {
        // Superadmin - Activity Log
        Route::get('activity-log', 'ActivityLogController@index')->name('activity-log');
        Route::get('/debug-logs', 'ActivityLogController@showDebugLogs')->name('logs.debug');

        // User Role Management
        Route::get('user-role', 'UserRoleController@index')->name('user-role');
        Route::get('user-role/create', 'UserRoleController@create')->name('user-role.create');
        Route::post('user-role/store', 'UserRoleController@store')->name('user-role.store');
        Route::get('user-role/{id}/edit', 'UserRoleController@edit')->name('user-role.edit');
        Route::post('user-role/{id}', 'UserRoleController@update')->name('user-role.update');
        Route::get('user-role/view/{id}', 'UserRoleController@show')->name('user-role.show');
        Route::get('/user-role/search', 'UserRoleController@search')->name('user-role.search');
        Route::delete('user-role/{id}', 'UserRoleController@destroy')->name('user-role.destroy');
        Route::get('/user-role/trash', 'UserRoleController@trashList')->name('user-role.trash');
        Route::get('/user-role/{id}/restore', 'UserRoleController@restore')->name('user-role.restore');
        Route::delete('/user-role/{id}/force-delete', 'UserRoleController@forceDelete')->name('user-role.forceDelete');
    });

    Route::middleware([RoleMiddleware::class . ':Superadmin|Admin'])->group(function () {

        // User Management
        Route::get('user', 'UserController@index')->name('user');
        Route::get('user/create', 'UserController@create')->name('user.create');
        Route::post('user/store', 'UserController@store')->name('user.store');
        Route::get('user/{id}/edit', 'UserController@edit')->name('user.edit');
        Route::post('user/{id}', 'UserController@update')->name('user.update');
        Route::get('user/view/{id}', 'UserController@show')->name('user.show');
        Route::get('/user/search', 'UserController@search')->name('user.search');
        Route::delete('user/{id}', 'UserController@destroy')->name('user.destroy');
        Route::get('/user/trash', 'UserController@trashList')->name('user.trash');
        Route::get('/user/{id}/restore', 'UserController@restore')->name('user.restore');
        Route::delete('/user/{id}/force-delete', 'UserController@forceDelete')->name('user.forceDelete');

        //Campus
        Route::get('campus/create', 'CampusController@create')->name('campus.create');
        Route::post('campus/store', 'CampusController@store')->name('campus.store');
        Route::get('campus/{id}/edit', 'CampusController@edit')->name('campus.edit');
        Route::post('campus/{id}', 'CampusController@update')->name('campus.update');
        Route::delete('campus/{id}', 'CampusController@destroy')->name('campus.destroy');
        Route::get('/campus/trash', 'CampusController@trashList')->name('campus.trash');
        Route::get('/campus/{id}/restore', 'CampusController@restore')->name('campus.restore');
        Route::delete('/campus/{id}/force-delete', 'CampusController@forceDelete')->name('campus.forceDelete');

        //Announcement
        Route::get('announcement/create', 'AnnouncementController@create')->name('announcement.create');
        Route::post('announcement/store', 'AnnouncementController@store')->name('announcement.store');
        Route::get('announcement/{id}/edit', 'AnnouncementController@edit')->name('announcement.edit');
        Route::post('announcement/{id}', 'AnnouncementController@update')->name('announcement.update');
        Route::delete('announcement/{id}', 'AnnouncementController@destroy')->name('announcement.destroy');
        Route::get('/announcement/trash', 'AnnouncementController@trashList')->name('announcement.trash');
        Route::get('/announcement/{id}/restore', 'AnnouncementController@restore')->name('announcement.restore');
        Route::delete('/announcement/{id}/force-delete', 'AnnouncementController@forceDelete')->name('announcement.forceDelete');

        //Position
        Route::get('position/create', 'PositionController@create')->name('position.create');
        Route::post('position/store', 'PositionController@store')->name('position.store');
        Route::get('position/{id}/edit', 'PositionController@edit')->name('position.edit');
        Route::post('position/{id}', 'PositionController@update')->name('position.update');
        Route::delete('position/{id}', 'PositionController@destroy')->name('position.destroy');
        Route::get('/position/trash', 'PositionController@trashList')->name('position.trash');
        Route::get('/position/{id}/restore', 'PositionController@restore')->name('position.restore');
        Route::delete('/position/{id}/force-delete', 'PositionController@forceDelete')->name('position.forceDelete');

        // Computer Lab
        Route::get('computer-lab/create', 'ComputerLabController@create')->name('computer-lab.create');
        Route::post('computer-lab/store', 'ComputerLabController@store')->name('computer-lab.store');
        Route::get('computer-lab/{id}/edit', 'ComputerLabController@edit')->name('computer-lab.edit');
        Route::post('computer-lab/{id}', 'ComputerLabController@update')->name('computer-lab.update');
        Route::delete('computer-lab/{id}', 'ComputerLabController@destroy')->name('computer-lab.destroy');
        Route::get('/computer-lab/trash', 'ComputerLabController@trashList')->name('computer-lab.trash');
        Route::get('/computer-lab/{id}/restore', 'ComputerLabController@restore')->name('computer-lab.restore');
        Route::delete('/computer-lab/{id}/force-delete', 'ComputerLabController@forceDelete')->name('computer-lab.forceDelete');

        // Software
        Route::get('software/create', 'SoftwareController@create')->name('software.create');
        Route::post('software/store', 'SoftwareController@store')->name('software.store');
        Route::get('software/{id}/edit', 'SoftwareController@edit')->name('software.edit');
        Route::post('software/{id}', 'SoftwareController@update')->name('software.update');
        Route::delete('software/{id}', 'SoftwareController@destroy')->name('software.destroy');
        Route::get('/software/trash', 'SoftwareController@trashList')->name('software.trash');
        Route::get('/software/{id}/restore', 'SoftwareController@restore')->name('software.restore');
        Route::delete('/software/{id}/force-delete', 'SoftwareController@forceDelete')->name('software.forceDelete');

        // Work checklist
        Route::get('work-checklist/create', 'WorkChecklistController@create')->name('work-checklist.create');
        Route::post('work-checklist/store', 'WorkChecklistController@store')->name('work-checklist.store');
        Route::get('work-checklist/{id}/edit', 'WorkChecklistController@edit')->name('work-checklist.edit');
        Route::post('work-checklist/{id}', 'WorkChecklistController@update')->name('work-checklist.update');
        Route::delete('work-checklist/{id}', 'WorkChecklistController@destroy')->name('work-checklist.destroy');
        Route::get('/work-checklist/trash', 'WorkChecklistController@trashList')->name('work-checklist.trash');
        Route::get('/work-checklist/{id}/restore', 'WorkChecklistController@restore')->name('work-checklist.restore');
        Route::delete('/work-checklist/{id}/force-delete', 'WorkChecklistController@forceDelete')->name('work-checklist.forceDelete');

        // Lab Checklist
        Route::get('lab-checklist/create', 'LabChecklistController@create')->name('lab-checklist.create');
        Route::post('lab-checklist/store', 'LabChecklistController@store')->name('lab-checklist.store');
        Route::get('lab-checklist/{id}/edit', 'LabChecklistController@edit')->name('lab-checklist.edit');
        Route::post('lab-checklist/{id}', 'LabChecklistController@update')->name('lab-checklist.update');
        Route::delete('lab-checklist/{id}', 'LabChecklistController@destroy')->name('lab-checklist.destroy');
        Route::get('/lab-checklist/trash', 'LabChecklistController@trashList')->name('lab-checklist.trash');
        Route::get('/lab-checklist/{id}/restore', 'LabChecklistController@restore')->name('lab-checklist.restore');
        Route::delete('/lab-checklist/{id}/force-delete', 'LabChecklistController@forceDelete')->name('lab-checklist.forceDelete');
    });
});

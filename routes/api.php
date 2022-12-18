<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ToDoController;
use App\Http\Resources\ToDoResource;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::post('/create-account', [AuthenticationController::class, 'createAccount'])->name('CreateAccount');
//login user
Route::post('/signin', [AuthenticationController::class, 'signIn'])->name('login');
//using middleware
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function (Request $request) {
        return auth()->user();
    });
    Route::post('/sign-out', [AuthenticationController::class, 'signOut'])->name('logout');
});

Route::group(['middleware' => ['auth:sanctum', 'abilities:check-status,place-orders']], function() {
    Route::get('todo', [ToDoController::class, 'index']);
    Route::get('todo/{id}', [ToDoController::class, 'show']);
    Route::post('todo', [ToDoController::class, 'store'])->name('createTodo');
    Route::put('todo/{id}', [ToDoController::class, 'update']);
    Route::patch('todo/{id}', [ToDoController::class, 'changeStatus'])->name('changeStatus');
    Route::delete('todo/{id}', [ToDoController::class, 'delete'])->name('todoDelete');
    Route::get('check-token', [AuthenticationController::class, 'checkToken']);
});

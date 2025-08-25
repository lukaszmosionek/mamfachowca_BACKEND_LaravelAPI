<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Role;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    use ApiResponse;

    public function index(){

        $user = User::withTrashed()->get();

        return $this->success([
            'users' => UserResource::collection($user),
        ], 'Users fetched successfully');
    }

    public function show($userId){

        $perPage = request('per_page');

        $user = User::with('availabilities')->findOrFail($userId);
        $services = $user->services()->with('photos')->paginate($perPage);

        return $this->success([
            'user' => new UserResource($user),
            'services' => ServiceResource::collection( $services->items() ),
            'last_page' => $services->lastPage(),
        ], 'User fetched successfully');
    }

    public function destroy($userId){

        $user = User::withTrashed()->findOrFail($userId);

        if( $user->role == Role::ADMIN ) return $this->error('Admins cannot be deleted', 403);

        if ($user->trashed()) {
            $user->restore();
            $message = 'User restored successfully.';
        } else {
            $user->delete();
            $message = 'User soft-deleted successfully';
        }

        return response()->json(['message' => $message]);
    }
}

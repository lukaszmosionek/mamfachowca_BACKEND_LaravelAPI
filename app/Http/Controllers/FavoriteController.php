<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FavoriteController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $search = request('name');
        $provider_id = request('provider_id');
        $user = auth()->user();

        $services = Service::with(['provider:id,name', 'photos' ,'favoritedBy:id'])
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when($provider_id, function ($query, $provider_id) {
                    $query->where('provider_id', $provider_id);
                })
                ->whereHas('favoritedBy', function($query) use($user){
                    $query->where('users.id', $user->id );
                })
                // ->where('lang', App::getLocale())
                ->paginate(request('per_page', 10))
                ->through(function($service){
                    $service->is_favorited = true;
                    return $service;
                })
                ->withQueryString();

        return $this->success([
                    'data' => ServiceResource::collection($services->items()),
                    'total_pages' => $services->lastPage(),
                ],'Favorited services fetched successfully'
            );
    }

    public function toggle(Request $request, $itemId)
    {
        $user = auth()->user();
        $service = Service::findOrFail($itemId);

        if ($user->favorites()->where('service_id', $itemId)->exists()) {
            $user->favorites()->detach($itemId);
            return response()->json(['favorited' => false]);
        } else {
            $user->favorites()->attach($itemId);
            return response()->json(['favorited' => true]);
        }
    }

    public function isFavorited($itemId)
    {
        $user = auth()->user();
        $favorited = $user->favorites()->where('item_id', $itemId)->exists();
        return response()->json(['favorited' => $favorited]);
    }
}

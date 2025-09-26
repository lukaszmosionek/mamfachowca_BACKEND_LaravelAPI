<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoritedResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\FavoriteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FavoriteController extends Controller
{
    use ApiResponse;

    public function index(ServiceRepositoryInterface $services)
    {
        $services = $services->getFavoritedByUser(auth()->user()->id);

        return $this->success([
                    'favorites' => FavoritedResource::collection($services->items()),
                    'last_page' => $services->lastPage(),
                ],'Favorited services fetched successfully'
            );
    }

    public function toggle(Request $request, int $itemId, FavoriteService $service)
    {
        $result = $service->toggle(auth()->user(), $itemId);
        return $this->success($result);
    }

    public function isFavorited($itemId)
    {
        $favorited = auth()->user()->favorites()->where('item_id', $itemId)->exists();
        return $this->success(['favorited' => $favorited]);
    }
}

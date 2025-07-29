<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $favorites = $user->favorites()->get(['services.id'])->pluck('id');
        return response()->json($favorites);
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

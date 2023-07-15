<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IconResource;
use App\Models\Icon;

class IconController extends Controller
{
    protected Icon $icon;
    public function __construct()
    {
        $this->icon = new Icon();
    }
    public function index()
    {
        try {
            $all = $this->icon->all();
            $iconResource = IconResource::collection($all);
            return response()->json(['success' => true, 'message' => 'berhasil get data icons', 'icons' => $iconResource, 'code' => 200]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }
}

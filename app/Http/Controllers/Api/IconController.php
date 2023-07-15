<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IconController extends Controller
{
    public function index()
    {
        $get_all_storege = Storage::disk('public')->allFiles('icons');
        $icon_model = new Icon();
        foreach ($get_all_storege as $icon) {
            $icon_model->create([
                'icon' => explode('/', $icon)[1]
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    protected Category $category;
    public function __construct()
    {
        $this->category = new Category();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * DONE
         */
        try {
            $all = $this->category
                ->with('icon')->where('user_id', auth()->user()->id)->get();
            return response()->json(['success' => true, 'message' => 'berhasil get data categories', 'categories' => CategoryResource::collection($all), 'code' => 200]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /**
         * DONE
         */
        try {
            $validate = Validator::make($request->only('name', 'color', 'id_icon'), ['name' => 'required', 'color' => 'required', 'id_icon' => 'numeric|required']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            if ($request->id_icon) {
                $icons = new Icon();
                $check_icon_is_exist = $icons->find($request->id_icon);
                if ($check_icon_is_exist == null) {
                    return response()->json(['success' => false, 'error' => "Icon does'nt exist", 'code' => 400], 400);
                }
            }

            $created = $this->category->create([
                'category_name' => $request->name,
                'category_color' => $request->color,
                'id_icon' => $request->id_icon ?? null,
                'user_id' => auth()->user()->id,
            ]);
            if ($created) {
                return response()->json(['success' => true, 'message' => 'berhasil simpan category', 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'gagal simpan category', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500,], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        /**
         * DONE
         */
        try {
            $category = $this->category->where('user_id', auth()->user()->id)->find($id);
            if ($category != null) {
                return response()->json(['success' => true, 'message' => 'berhasil get data category', 'category' => $category, 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'category not found', 'code' => 404], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /**
         * DONE
         */
        try {
            $validate = Validator::make($request->only('name', 'color', 'id_icon'), ['name' => 'required', 'color' => 'required', 'id_icon' => 'numeric|required']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            if ($request->id_icon) {
                $icons = new Icon();
                $check_icon_is_exist = $icons->find($request->id_icon);
                if ($check_icon_is_exist == null) {
                    return response()->json(['success' => false, 'error' => "Icon does'nt exist", 'code' => 400], 400);
                }
            }

            $created = $this->category->where('user_id', auth()->user()->id)->where('id', $id)->update([
                'category_name' => $request->name,
                'category_color' => $request->color,
                'id_icon' => $request->id_icon ?? null,
            ]);
            if ($created) {
                return response()->json(['success' => true, 'message' => 'berhasil update category', 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'gagal update category', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500,], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /**
         * DONE
         */
        try {
            $deleted = $this->category->where('user_id', auth()->user()->id)->where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'berhasil delete category', 'code' => 200]);
            } else {
                return response()->json(['success' => true, 'message' => 'gagal delete category', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }
}

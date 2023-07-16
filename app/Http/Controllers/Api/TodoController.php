<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    protected Todo $todo;

    public function __construct()
    {
        $this->todo = new Todo();
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
            $all = $this->todo->orderBy('created_at', "DESC")->with(['category' => function ($q) {
                return $q->with('icon');
            }])->where('user_id', auth()->user()->id)->cursorPaginate();
            return response()->json(['success' => true, 'message' => 'berhasil get data todo', 'todos' => $all, 'code' => 200]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
            $validate = Validator::make($request->only('title', 'content', 'id_category'), ['title' => 'required', 'content' => 'required', 'id_category' => 'numeric|nullable']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            if ($request->id_category) {
                $category = new Category();
                $check_category_exist = $category->find($request->id_category);
                if ($check_category_exist == null) {
                    return response()->json(['success' => false, 'error' => "Category does'nt exist", 'code' => 400], 400);
                }
            }

            $created = $this->todo->create([
                'title' => $request->title,
                'content' => $request->content,
                'id_category' => $request->id_category ?? null,
                'user_id' => auth()->user()->id,
            ]);
            if ($created) {
                return response()->json(['success' => true, 'message' => 'berhasil menyimpan todo', 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'gagal menyimpan todo', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
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
            $todo = $this->todo->where('user_id', auth()->user()->id)->find($id);
            if ($todo != null) {
                return response()->json(['success' => true, 'message' => 'berhasil get data todo', 'todo' => $todo, 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'todo not found', 'code' => 404], 404);
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
            $validate = Validator::make($request->only('title', 'content', 'id_category'), ['title' => 'required', 'content' => 'required', 'id_category' => 'numeric|nullable']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            $created = $this->todo->where('user_id', auth()->user()->id)->where('id', $id)->update([
                'title' => $request->title,
                'content' => $request->content,
                'id_category' => $request->id_category ?? null,
            ]);
            if ($created) {
                return response()->json(['success' => true, 'message' => 'berhasil update todo', 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'gagal update todo', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->todo->where('user_id', auth()->user()->id)->where('id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'berhasil delete todo', 'code' => 200]);
            } else {
                return response()->json(['success' => true, 'message' => 'gagal delete todo', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }
}

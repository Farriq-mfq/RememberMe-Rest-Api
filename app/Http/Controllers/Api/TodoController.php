<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        try {
            $all = $this->todo->orderBy('created_at', "DESC")->all();
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
        try {
            $validate = Validator::make($request->only('title', 'content', 'background'), ['title' => 'required', 'content' => 'required', 'background' => 'required']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            $created = $this->todo->create([
                'title' => $request->title,
                'content' => $request->content,
                'background' => $request->background,
            ]);
            if ($created) {
                return response()->json(['success' => true, 'message' => 'berhasil menyimpan todo', 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'gagal menyimpan todo', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500, 'detail_error' => $e->getMessage()], 500);
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
        try {
            $todo = $this->todo->orderBy('created_at', "DESC")->find($id);
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
        try {
            $validate = Validator::make($request->only('title', 'content', 'background'), ['title' => 'required', 'content' => 'required', 'background' => 'required']);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'validations' => $validate->errors(), 'code' => 400], 400);
            }
            $created = $this->todo->where('id', $id)->update([
                'title' => $request->title,
                'content' => $request->content,
                'background' => $request->background,
            ]);
            if ($created) {
                return response()->json(['success' => true, 'message' => 'berhasil update todo', 'code' => 200]);
            } else {
                return response()->json(['success' => false, 'message' => 'gagal update todo', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->todo->where('id', $id)->delete();
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use App\Models\Category;
use App\Models\Todo;
use Carbon\Carbon;
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
    public function index(Request $request)
    {
        /**
         * DONE
         */
        try {
            if ($request->filter) {
                switch ($request->filter) {
                    case 'completed':
                        $all = $this->todo->orderBy('created_at', "DESC")->where('pinned', true)->with(['category' => function ($q) {
                            return $q->with('icon');
                        }])->where('user_id', auth()->user()->id)->get();
                        break;
                    case 'uncomplted':
                        $all = $this->todo->orderBy('created_at', "DESC")->where('pinned', false)->with(['category' => function ($q) {
                            return $q->with('icon');
                        }])->where('user_id', auth()->user()->id)->get();
                        break;
                    case 'today':
                        $all = $this->todo->orderBy('created_at', "DESC")->whereDate('created_at', today(auth()->user()->timezone))->with(['category' => function ($q) {
                            return $q->with('icon');
                        }])->where('user_id', auth()->user()->id)->get();
                        break;
                    case 'date':
                        if ($request->date) {
                            $validate_date = Validator::make($request->only('date'), ['date' => 'date']);
                            if ($validate_date->fails()) {
                                return response()->json(['success' => false, 'message' => "Invalid date filter", 'code' => 400], 400);
                            } else {
                                $all = $this->todo->orderBy('created_at', "DESC")->whereDate('created_at', Carbon::parse($request->date))->with(['category' => function ($q) {
                                    return $q->with('icon');
                                }])->where('user_id', auth()->user()->id)->get();
                            }
                        } else {
                            $all = $this->todo->orderBy('created_at', "DESC")->whereDate('created_at', Carbon::today())->with(['category' => function ($q) {
                                return $q->with('icon');
                            }])->where('user_id', auth()->user()->id)->get();
                        }
                        break;
                    default:
                        $all = $this->todo->orderBy('created_at', "DESC")->with(['category' => function ($q) {
                            return $q->with('icon');
                        }])->where('user_id', auth()->user()->id)->get();
                        break;
                }
            } else if ($request->search) {
                $all = $this->todo->orderBy('created_at', "DESC")->where('title', 'LIKE', '%' . $request->search . '%')->with(['category' => function ($q) {
                    return $q->with('icon');
                }])->where('user_id', auth()->user()->id)->get();
            } else {
                $all = $this->todo->orderBy('created_at', "DESC")->with(['category' => function ($q) {
                    return $q->with('icon');
                }])->where('user_id', auth()->user()->id)->get();
            }
            return response()->json(['success' => true, 'message' => 'berhasil get data todo', 'todos' => TodoResource::collection($all), 'code' => 200]);
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


    /**
     * update pinned status completed for task
     */

    public function updatePinned(string $id)
    {
        try {
            $update = $this->todo->where('user_id', auth()->user()->id)->where('id', $id);
            if ($update) {
                $pinned = $update->first();
                if ($pinned != null) {
                    if ($update->first()->pinned) {
                        $update->update([
                            'pinned' => false
                        ]);
                    } else {
                        $update->update([
                            'pinned' => true
                        ]);
                    }
                } else {
                    return response()->json(['success' => true, 'message' => 'todo not found', 'code' => 401]);
                }
                return response()->json(['success' => true, 'message' => 'berhasil update pinned', 'code' => 200]);
            } else {
                return response()->json(['success' => true, 'message' => 'gagal delete pinned', 'code' => 400], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Internal server error", 'code' => 500], 500);
        }
    }
}

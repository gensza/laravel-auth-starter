<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TodoController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $search = $request->get('search');

        $query = Todo::with('user:id,username')
            ->where('user_id', auth()->id());

        if ($search) {
            $query->where('title', 'like', "%$search%");
        }

        $total = $query->count();

        $todos = $query
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($todo) {
            return [
                'id' => $todo->id,
                'title' => $todo->title,
                'descriptions' => $todo->descriptions,
                'is_done' => $todo->is_done,
                'is_done_label' => $todo->is_done_label,
                'username' => $todo->user->username,
                'created_at' => $todo->created_at,
            ];
        });

        return response()->json([
            'data' => $todos,
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'descriptions' => 'nullable'
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'descriptions' => $request->descriptions,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'code' => 201,
            'message' => 'Create todo success',
            'data' => $todo
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $todo->update($request->only('title', 'descriptions', 'is_done'));

        return response()->json([
            'code' => 200,
            'message' => 'Update todo success',
            'data' => $todo
        ], 200);
    }

    public function destroy($id)
    {
        $todo = Todo::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $todo->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Delete todo success',
        ], 200);
    }
}

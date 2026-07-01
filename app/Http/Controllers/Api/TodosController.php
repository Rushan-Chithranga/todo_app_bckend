<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    public function index()
    {
        $todos = auth()->user()->todos;

        return response()->json($todos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo = auth()->user()->todos()->create($request->only('title', 'description'));

        return response()->json($todo, 201);
    }

    public function update(Request $request, $id)
    {
        $todo = auth()->user()->todos()->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'sometimes|required|boolean',
        ]);

        $todo->update($request->only('title', 'description', 'is_completed'));

        return response()->json($todo);
    }

    public function destroy($id)
    {
        $todo = auth()->user()->todos()->findOrFail($id);
        $todo->delete();

        return response()->json(null, 204);
    }
}

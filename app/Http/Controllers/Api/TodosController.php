<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class TodosController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);
        $filter = $request->query('filter');
        $search = $request->query('search');

        $todos = auth()->user()
            ->todos()
            ->with('user:id,name')
            ->when($filter, function ($query) use ($filter) {
                if ($filter === 'completed') {
                    $query->where('is_completed', true);
                } elseif ($filter === 'pending') {
                    $query->where('is_completed', false);
                }
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        $todos->getCollection()->transform(function ($todo) {
            return [
                'id' => $todo->id,
                'title' => $todo->title,
                'description' => $todo->description,
                'is_completed' => $todo->is_completed,
                'user_name' => $todo->user?->name,
                'created_at' => $todo->created_at,
                'updated_at' => $todo->updated_at,
            ];
        });

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

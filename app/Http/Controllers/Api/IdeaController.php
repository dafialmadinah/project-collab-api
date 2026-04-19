<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index()
    {
        $ideas = Idea::with('user')->latest()->get();

        return response()->json([
            'message' => 'List ideas',
            'data' => $ideas
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string'
        ]);

        $idea = Idea::create($validated);

        return response()->json([
            'message' => 'Idea created successfully',
            'data' => $idea
        ], 201);
    }

    public function show($id)
    {
        $idea = Idea::with(['user', 'joinRequests'])->find($id);

        if (!$idea) {
            return response()->json([
                'message' => 'Idea not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail idea',
            'data' => $idea
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $idea = Idea::find($id);

        if (!$idea) {
            return response()->json([
                'message' => 'Idea not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|string'
        ]);

        $idea->update($validated);

        return response()->json([
            'message' => 'Idea updated successfully',
            'data' => $idea
        ], 200);
    }

    public function destroy($id)
    {
        $idea = Idea::find($id);

        if (!$idea) {
            return response()->json([
                'message' => 'Idea not found'
            ], 404);
        }

        $idea->delete();

        return response()->json([
            'message' => 'Idea deleted successfully'
        ], 200);
    }
}
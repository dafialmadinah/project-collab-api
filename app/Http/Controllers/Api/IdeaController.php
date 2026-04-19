<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ideas', 'public');
        }

        $idea = Idea::create([
            'user_id' => auth('api')->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Idea created successfully',
            'data' => $idea
        ], 201);
    }

    public function show($id)
    {
        $idea = Idea::with(['user', 'joinRequests'])->find($id);

        if (!$idea) {
            return response()->json(['message' => 'Idea not found'], 404);
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
            return response()->json(['message' => 'Idea not found'], 404);
        }

        if ($idea->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            if ($idea->image && Storage::disk('public')->exists($idea->image)) {
                Storage::disk('public')->delete($idea->image);
            }
            $data['image'] = $request->file('image')->store('ideas', 'public');
        }

        $idea->update($data);

        return response()->json([
            'message' => 'Idea updated successfully',
            'data' => $idea
        ], 200);
    }

    public function destroy($id)
    {
        $idea = Idea::find($id);

        if (!$idea) {
            return response()->json(['message' => 'Idea not found'], 404);
        }

        if ($idea->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($idea->image && Storage::disk('public')->exists($idea->image)) {
            Storage::disk('public')->delete($idea->image);
        }

        $idea->delete();

        return response()->json([
            'message' => 'Idea deleted successfully'
        ], 200);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JoinRequest;
use Illuminate\Http\Request;

class JoinRequestController extends Controller
{
    public function index()
    {
        $requests = JoinRequest::with(['user', 'idea'])->latest()->get();

        return response()->json([
            'message' => 'List join requests',
            'data' => $requests
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idea_id' => 'required|exists:ideas,id',
        ]);

        $joinRequest = JoinRequest::create([
            'user_id' => auth('api')->id(),
            'idea_id' => $validated['idea_id'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Join request created',
            'data' => $joinRequest
        ], 201);
    }

    public function show($id)
    {
        $joinRequest = JoinRequest::with(['user', 'idea'])->find($id);

        if (!$joinRequest) {
            return response()->json(['message' => 'Join request not found'], 404);
        }

        return response()->json([
            'message' => 'Detail join request',
            'data' => $joinRequest
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $joinRequest = JoinRequest::find($id);

        if (!$joinRequest) {
            return response()->json(['message' => 'Join request not found'], 404);
        }

        if ($joinRequest->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $joinRequest->update($validated);

        return response()->json([
            'message' => 'Join request updated',
            'data' => $joinRequest
        ], 200);
    }

    public function destroy($id)
    {
        $joinRequest = JoinRequest::find($id);

        if (!$joinRequest) {
            return response()->json(['message' => 'Join request not found'], 404);
        }

        if ($joinRequest->user_id !== auth('api')->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $joinRequest->delete();

        return response()->json([
            'message' => 'Join request deleted'
        ], 200);
    }
}
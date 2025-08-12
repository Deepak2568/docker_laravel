<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // CREATE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'status'     => 'required|in:active,inactive',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string',
            'type'       => 'required|in:free,premium',
            'image'      => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'status', 'categories', 'type']);

        // Handle file upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/items', 'public');
        }

        $item = Item::create($data);

        return response()->json(['message' => 'Item created successfully', 'item' => $item], 201);
    }

    // READ
    public function index()
    {
        return response()->json(Item::orderBy('id', 'desc')->get());
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'       => 'sometimes|required|string|max:255',
            'status'     => 'sometimes|required|in:active,inactive',
            'categories' => 'sometimes|required|array|min:1',
            'categories.*' => 'string',
            'type'       => 'sometimes|required|in:free,premium',
            'image'      => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'status', 'categories', 'type']);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('uploads/items', 'public');
        }

        $item->update($data);

        return response()->json(['message' => 'Item updated successfully', 'item' => $item]);
    }

    // DELETE
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }

}

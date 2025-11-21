<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    public function index()
    {
        return CustomField::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,date,number,select,checkbox,radio,file,email,phone',
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $validated['name'] = Str::slug($validated['label'], '_');

        if (CustomField::where('name', $validated['name'])->exists()) {
            return response()->json(['message' => 'A field with a similar label already exists.'], 422);
        }

        $validated['is_required'] = (bool)($validated['is_required'] ?? false);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $field = CustomField::create($validated);

        return response()->json($field, 201);
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return response()->json(['message' => 'Field deleted successfully.']);
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');

        foreach ($order as $item) {
            CustomField::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order']
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Field order updated']);
    }
}

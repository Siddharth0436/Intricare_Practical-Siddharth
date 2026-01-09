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
        // Allowed UI types (what the front-end sends)
        $allowedUiTypes = ['text','email','phone','number','date','textarea','select','checkbox','radio','file'];

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => ['required', 'string'],
            'is_required' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        // Ensure the provided type is one of the expected UI types
        if (!in_array($validated['type'], $allowedUiTypes, true)) {
            return response()->json(['message' => 'Invalid field type provided.'], 422);
        }

        // Map UI input types to the stored DB type (migration enum)
        // - keep DB types consistent with the migration/enums
        // - email is stored as text, phone/number stored as number
        $typeMap = [
            'text' => 'text',
            'textarea' => 'textarea',
            'date' => 'date',
            'number' => 'number',
            'select' => 'select',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
            'file' => 'file',
            'email' => 'text',
            'phone' => 'number',
        ];

        $originalType = $validated['type'];
        $validated['type'] = $typeMap[$originalType];

        // Build a machine-friendly name from label
        $validated['name'] = Str::slug($validated['label'], '_');

        if (CustomField::where('name', $validated['name'])->exists()) {
            return response()->json(['message' => 'A field with a similar label already exists.'], 422);
        }

        $validated['is_required'] = (bool)($validated['is_required'] ?? false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Preserve the original UI input type in meta so the front-end can render correct input attributes
        $validated['meta'] = array_merge($validated['meta'] ?? [], ['input_type' => $originalType]);

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

<?php

class ProfileController {

    public function updateField(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'field' => ['required', 'string', 'in:name,phone,email,olive_type,farm_name,farm_location,tree_number,camion_capacity,company_name,mill_name,packer_name'],
            'value' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $field = $request->input('field');
        $value = $request->input('value');

        // Check if the field is actually allowed to be updated through this method
        $allowedFields = [
            'name', 'phone', 'email', 'olive_type', 'farm_name', 'farm_location', 
            'tree_number', 'camion_capacity', 'company_name', 'mill_name', 'packer_name'
        ];

        if (in_array($field, $allowedFields)) {
            $user->$field = $value;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => __('Updated successfully!'),
                'value' => $value
            ]);
        }

        return response()->json(['success' => false, 'message' => __('Invalid field.')], 400);
    }
}

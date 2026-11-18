<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the user's profile edit form.
     */
    public function edit()
    {
        $user = auth()->user();
        $districts = District::orderBy('name')->get();

        return view('citizen.profile', compact('user', 'districts'));
    }

    /**
     * Save updated profile data.
     */
    public function update(Request $request)
    {
        $tab = $request->input('active_tab');

        $customMessages = [
            'phone.regex' => 'The mobile number must be a valid 11-digit Bangladeshi phone number (e.g. 01712345678).',
            'nid.regex' => 'National ID (NID) must be exactly 10 or 17 digits.',
            'nid.unique' => 'This National ID (NID) has already been registered.',
            'current_password.required' => 'Current password is required to change your password.',
            'current_password.required_with' => 'Current password is required to change your password.',
            'current_password.current_password' => 'The provided current password is incorrect.',
            'password.required' => 'New password is required.',
            'password.required_with' => 'New password is required.',
        ];

        if ($tab === 'personal') {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'name_bn' => ['required', 'string', 'max:255', 'regex:/^[\p{Bengali}\s().,\-\/]+$/u'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
                'nid' => ['nullable', 'string', 'regex:/^(\d{10}|\d{17})$/', Rule::unique('users', 'nid')->ignore(auth()->id())],
                'phone' => ['nullable', 'string', 'regex:/^01[3-9]\d{8}$/'],
                'dob' => ['nullable', 'date'],
                'father_name' => ['nullable', 'string', 'max:255'],
                'mother_name' => ['nullable', 'string', 'max:255'],
                'spouse_name' => ['nullable', 'string', 'max:255'],
                'marital_status' => ['nullable', 'string', 'in:Married,Single,Divorced,Widowed'],
                'nationality' => ['nullable', 'string', 'max:100'],
                'religion' => ['nullable', 'string', 'max:100'],
                'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ];
            $fieldsToUpdate = [
                'name', 'name_bn', 'email', 'nid', 'phone', 'dob', 'father_name', 'mother_name',
                'spouse_name', 'marital_status', 'nationality', 'religion',
            ];
        } elseif ($tab === 'address') {
            $rules = [
                'district_id' => ['nullable', 'integer', 'exists:districts,id'],
                'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
                'present_address' => ['nullable', 'string'],
                'permanent_address' => ['nullable', 'string'],
            ];
            $fieldsToUpdate = ['district_id', 'upazila_id', 'present_address', 'permanent_address'];
        } elseif ($tab === 'education') {
            $rules = [
                'edu_qualification' => ['nullable', 'string', 'max:255'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'employer_address' => ['nullable', 'string', 'max:500'],
                'annual_income' => ['nullable', 'numeric', 'min:0'],
                'tin_number' => ['nullable', 'string', 'max:20'],
            ];
            $fieldsToUpdate = ['edu_qualification', 'occupation', 'employer_address', 'annual_income', 'tin_number'];
        } elseif ($tab === 'security') {
            $rules = [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ];
            $fieldsToUpdate = [];
        } else {
            $rules = [
                'name' => [$request->has('name') ? 'required' : 'nullable', 'string', 'max:255'],
                'name_bn' => ['nullable', 'string', 'max:255', 'regex:/^[\p{Bengali}\s().,\-\/]+$/u'],
                'email' => [$request->has('email') ? 'required' : 'nullable', 'string', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
                'nid' => ['nullable', 'string', 'regex:/^(\d{10}|\d{17})$/', Rule::unique('users', 'nid')->ignore(auth()->id())],
                'phone' => ['nullable', 'string', 'regex:/^01[3-9]\d{8}$/'],
                'dob' => ['nullable', 'date'],
                'father_name' => ['nullable', 'string', 'max:255'],
                'mother_name' => ['nullable', 'string', 'max:255'],
                'spouse_name' => ['nullable', 'string', 'max:255'],
                'marital_status' => ['nullable', 'string', 'in:Married,Single,Divorced,Widowed'],
                'nationality' => ['nullable', 'string', 'max:100'],
                'religion' => ['nullable', 'string', 'max:100'],
                'present_address' => ['nullable', 'string'],
                'permanent_address' => ['nullable', 'string'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'employer_address' => ['nullable', 'string', 'max:500'],
                'edu_qualification' => ['nullable', 'string', 'max:255'],
                'annual_income' => ['nullable', 'numeric', 'min:0'],
                'tin_number' => ['nullable', 'string', 'max:20'],
                'district_id' => ['nullable', 'integer', 'exists:districts,id'],
                'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
                'current_password' => ['nullable', 'required_with:password', 'current_password'],
                'password' => ['nullable', 'string', 'min:6', 'confirmed', 'required_with:current_password'],
            ];
            $fieldsToUpdate = [
                'name', 'name_bn', 'email', 'nid', 'phone', 'dob', 'father_name', 'mother_name',
                'spouse_name', 'marital_status', 'nationality', 'religion',
                'present_address', 'permanent_address', 'occupation',
                'employer_address', 'edu_qualification', 'annual_income',
                'tin_number', 'district_id', 'upazila_id',
            ];
        }

        $request->validate($rules, $customMessages);

        $data = [];
        foreach ($fieldsToUpdate as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($tab === 'personal' || ! $tab) {
            if ($request->hasFile('profile_photo')) {
                $user = auth()->user();
                if (! empty($user->profile_photo_path) && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('profile_photo')->store('profiles', 'public');
                $data['profile_photo_path'] = $path;
            }
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if (! empty($data)) {
            auth()->user()->update($data);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully! Your next application will be auto-filled.')
            ->with('active_tab', $tab ?? 'personal');
    }
}

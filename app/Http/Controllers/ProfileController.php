<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

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
        $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'name_bn'           => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'nid'               => ['nullable', 'string', 'min:10', 'max:17'],
            'phone'             => ['nullable', 'string', 'max:15'],
            'dob'               => ['nullable', 'date'],
            'father_name'       => ['nullable', 'string', 'max:255'],
            'mother_name'       => ['nullable', 'string', 'max:255'],
            'spouse_name'       => ['nullable', 'string', 'max:255'],
            'marital_status'    => ['nullable', 'string', 'in:Married,Single,Divorced,Widowed'],
            'nationality'       => ['nullable', 'string', 'max:100'],
            'religion'          => ['nullable', 'string', 'max:100'],
            'present_address'   => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'occupation'        => ['nullable', 'string', 'max:255'],
            'employer_address'  => ['nullable', 'string', 'max:500'],
            'edu_qualification' => ['nullable', 'string', 'max:255'],
            'annual_income'     => ['nullable', 'numeric', 'min:0'],
            'tin_number'        => ['nullable', 'string', 'max:20'],
            'district_id'       => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id'        => ['nullable', 'integer', 'exists:upazilas,id'],
            'password'          => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $data = $request->only([
            'name', 'name_bn', 'email', 'nid', 'phone', 'dob', 'father_name', 'mother_name',
            'spouse_name', 'marital_status', 'nationality', 'religion',
            'present_address', 'permanent_address', 'occupation',
            'employer_address', 'edu_qualification', 'annual_income',
            'tin_number', 'district_id', 'upazila_id',
        ]);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $data['profile_photo_path'] = $path;
        }

        auth()->user()->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully! Your next application will be auto-filled.');
    }
}

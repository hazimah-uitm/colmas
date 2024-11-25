<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('pages.user.profile.show', [
            'user' => $user,
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $campusList = Campus::where('publish_status', 1)->get();
        $positionList = Position::where('publish_status', 1)->get();

        return view('pages.user.profile.edit', [
            'save_route' => route('profile.update', $id),
            'str_mode' => 'Kemas Kini',
            'user' => $user,
            'campusList' => $campusList,
            'positionList' => $positionList,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required',
            'staff_id'   => 'required|unique:users,staff_id,' . $id,
            'email'      => 'required|email|unique:users,email,' . $id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position_id' => 'required|exists:positions,id',
            'campus_id'  => 'required|array|exists:campuses,id',
            'office_phone_no' => 'nullable|string',
        ],[
            'name.required'     => 'Sila isi nama pengguna',
            'staff_id.required' => 'Sila isi no. pekerja pengguna',
            'staff_id.unique' => 'No. pekerja telah wujud',
            'email.required'    => 'Sila isi emel pengguna',
            'email.unique'    => 'Emel telah wujud',
            'position_id.required' => 'Sila isi jawatan pengguna',
            'campus_id.required' => 'Sila isi kampus pengguna',
        ]);

        $user = User::findOrFail($id);

        // Update the user's basic information
        $user->fill($request->only('name', 'staff_id', 'email', 'position_id', 'office_phone_no'));
        if ($request->hasFile('profile_image')) {
            // Store the file and get its path
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }
        $user->save();
        $user->campus()->sync($request->input('campus_id'));
        
        return redirect()->route('profile.show', $id) // Corrected route name
            ->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function changePasswordForm($id)
    {
        $user = User::findOrFail($id);

        return view('pages.user.profile.change-password', [
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Kata laluan semasa tidak sah.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.show', $id)
            ->with('success', 'Kata laluan berjaya dikemaskini.');
    }
}

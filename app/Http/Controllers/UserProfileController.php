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
            'position_id' => 'required|exists:positions,id',
            'campus_id'  => 'required|exists:campuses,id',
            'office_phone_no' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        // Update the user's basic information
        $user->fill($request->only('name', 'staff_id', 'email', 'position_id', 'campus_id'));
        $user->save();

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

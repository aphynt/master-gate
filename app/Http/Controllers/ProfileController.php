<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    //
    public function index()
    {
        return view('profile.index');
    }

    public function changeAvatar(Request $request)
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'max:8192']
        ]);

        try {
            $userId = Auth::id();

            $oldAvatar = User::where('id', $userId)->value('avatar');

            $oldPath = public_path('avatar/' . $oldAvatar);
            if ($oldAvatar && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $file = $validated['avatar'];
            $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('avatar'), $filename);

            User::where('id', $userId)->update([
                'avatar' => $filename,
                'updated_at' => now()
            ]);

            return redirect()->back()->with('success', 'Avatar berhasil diperbarui.');

        } catch (\Throwable $th) {
            return redirect()->back()->with('info', 'Error: ' . $th->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);

        try {
            $userId = Auth::id();
            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return back()->with(['info' => 'Password lama tidak cocok.']);
            }

            User::where('id', $userId)->update([
                'password' => Hash::make($request->new_password)
            ]);

            return back()->with('success', 'Password berhasil diubah.');

        } catch (\Throwable $th) {
            return back()->with('info', $th->getMessage());
        }


    }
}

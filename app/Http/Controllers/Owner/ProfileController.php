<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('owner.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,'.$user->id,
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:30',
        ]);

        $user->update($validated);

        $this->log($request, "Memperbarui profil owner: {$user->name}");

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        $this->log($request, 'Mengganti kata sandi owner');

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    private function log(Request $request, string $activity): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_name' => $user->name,
            'role' => $user->primaryRole(),
            'activity' => $activity,
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);
    }
}

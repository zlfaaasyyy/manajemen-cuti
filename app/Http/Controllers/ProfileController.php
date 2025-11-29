<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        $quotaInfo = null;
        
        if (in_array($user->role, ['user', 'ketua_divisi'])) {
            $totalKuota = 12; 
            $sisaKuota = $user->kuota_cuti ?? 0; 

            $terpakai = max(0, $totalKuota - $sisaKuota);
            
            $quotaInfo = [
                'total' => $totalKuota,
                'terpakai' => $terpakai,
                'sisa' => $sisaKuota
            ];
        }

        return view('profile.edit', [
            'user' => $user,
            'quotaInfo' => $quotaInfo,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            $path = $request->file('foto_profil')->store('fotos', 'public');
            $user->foto_profil = $path;
        }

        if ($user->role === 'admin') {
            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        } else {
            $user->nomor_telepon = $validated['nomor_telepon'] ?? $user->nomor_telepon;
            $user->alamat = $validated['alamat'] ?? $user->alamat;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
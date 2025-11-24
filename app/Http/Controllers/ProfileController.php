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
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // 1. LOGIKA HITUNG KUOTA (Khusus User & Ketua Divisi)
        $quotaInfo = null;
        
        // Cek apakah role user berhak mendapatkan kuota cuti
        if (in_array($user->role, ['user', 'ketua_divisi'])) {
            $totalKuota = 12; // Default kuota tahunan
            $sisaKuota = $user->kuota_cuti ?? 0; // Ambil dari DB
            
            // Hitung terpakai (Total - Sisa)
            // Pastikan tidak minus (jika ada kesalahan data manual)
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

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // 2. LOGIKA UPLOAD FOTO PROFIL
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada (agar tidak menuhin storage)
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            // Simpan foto baru ke folder 'fotos' di disk 'public'
            $path = $request->file('foto_profil')->store('fotos', 'public');
            $user->foto_profil = $path;
        }

        // 3. PEMBATASAN HAK AKSES EDIT
        if ($user->role === 'admin') {
            // Admin SULTAN: Bisa ubah Nama & Email
            $user->fill($validated);

            // Jika email berubah, reset verifikasi email
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        } else {
            // Rakyat Jelata (User/Ketua/HRD): Hanya update data pendukung
            // Nama & Email diabaikan meskipun dikirim dari form (security measure)
            $user->nomor_telepon = $validated['nomor_telepon'] ?? $user->nomor_telepon;
            $user->alamat = $validated['alamat'] ?? $user->alamat;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
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
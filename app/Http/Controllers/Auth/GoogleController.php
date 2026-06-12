<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    private function saveGoogleAvatar($avatarUrl, $nip = null)
    {
        if (!$avatarUrl) {
            return null;
        }

        try {
            $response = Http::timeout(30)->get($avatarUrl);

            if (!$response->successful()) {
                return null;
            }

            $filename = ($nip ?: Str::uuid()) . '.jpg';
            $path = 'upload/avatars/' . $filename;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function login()
    {
        return view('auth.login');
    }

    public function redirect()
    {
        return Socialite::driver('google')
            ->with([
                'prompt' => 'select_account',
            ])
            ->stateless()
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google.');
        }

        $email = strtolower(trim($googleUser->email));

        $isAdminEmail = $email === 'admin@ptaun.com';

        if (!str_ends_with($email, '@ptaun.com')) {
            return redirect('/login')->with('error', 'Login wajib menggunakan email PT AUN.');
        }

        $karyawan = Karyawan::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$isAdminEmail && !$karyawan) {
            return redirect('/login')->with('error', 'Akun Anda belum terdaftar sebagai karyawan. Silakan hubungi Admin.');
        }

        DB::beginTransaction();

        try {
            $avatarPath = null;

            if ($googleUser->avatar) {
                $filename = $isAdminEmail ? 'admin' : $karyawan->nip;

                $avatarPath = $this->saveGoogleAvatar($googleUser->avatar, $filename);
            }

            if ($karyawan) {
                $karyawan->update([
                    'email' => $email,
                    'avatar' => $avatarPath ?: $karyawan->avatar,
                    'nama_depan' => $googleUser->user['given_name'] ?? $karyawan->nama_depan,
                    'nama_belakang' => $googleUser->user['family_name'] ?? $karyawan->nama_belakang,
                ]);

                $karyawan->refresh();
            }

            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if (!$user) {
                if ($isAdminEmail) {
                    $user = User::create([
                        'uuid' => (string) Str::uuid(),
                        'slug' => 'administrator',
                        'nama_depan' => 'Admin',
                        'nama_belakang' => 'PT AUN',
                        'email' => $email,
                        'google_id' => $googleUser->id,
                        'avatar' => $avatarPath,
                        'password' => bcrypt('PT4un#123'),
                    ]);

                    $user->syncRoles(['admin']);
                } else {
                    $user = User::create([
                        'uuid' => (string) Str::uuid(),
                        'slug' => Str::slug(trim($karyawan->nama_depan . ' ' . $karyawan->nama_belakang)),
                        'nama_depan' => $karyawan->nama_depan,
                        'nama_belakang' => $karyawan->nama_belakang,
                        'email' => $karyawan->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $avatarPath ?: $karyawan->avatar,
                        'password' => bcrypt('PT4un#123'),
                    ]);

                    $user->syncRoles(['user']);
                }
            } else {
                if ($isAdminEmail) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'email' => $email,
                        'avatar' => $avatarPath ?: $user->avatar,
                    ]);

                    $user->syncRoles(['admin']);
                } else {
                    $user->update([
                        'slug' => Str::slug(trim($karyawan->nama_depan . ' ' . $karyawan->nama_belakang)),
                        'nama_depan' => $karyawan->nama_depan,
                        'nama_belakang' => $karyawan->nama_belakang,
                        'email' => $karyawan->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $avatarPath ?: $karyawan->avatar,
                    ]);

                    $user->syncRoles(['user']);
                }
            }

            if ($karyawan) {
                $karyawan->update([
                    'user_uuid' => $user->uuid,
                ]);
            }

            DB::commit();

            Auth::login($user, true);

            if ($user->hasRole('user')) {
                $karyawanUser = Karyawan::where('user_uuid', $user->uuid)->first();

                if (!$karyawanUser) {
                    return redirect('/dashboard')->with('error', 'Data karyawan tidak ditemukan.');
                }

                return redirect()
                    ->route('karyawan.idCard', [
                        'nip' => $karyawanUser->nip,
                    ])
                    ->with('success', 'Login berhasil!');
            }

            return redirect('/dashboard')->with('success', 'Login berhasil!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect('/login')->with('error', 'Terjadi kesalahan saat proses login. ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

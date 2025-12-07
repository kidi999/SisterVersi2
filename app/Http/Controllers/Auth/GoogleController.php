<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists by Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // User exists, update avatar if changed
                $user->update([
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Check if user exists by email
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if ($user) {
                    // Link Google account to existing user
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                } else {
                    // Create new user
                    // Get default role (assuming 'User' or 'Mahasiswa')
                    $defaultRole = Role::where('name', 'Mahasiswa')->first();
                    
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'email_verified_at' => now(),
                        'password' => bcrypt(uniqid()), // Random password
                        'role_id' => $defaultRole ? $defaultRole->id : null,
                        'is_active' => true,
                    ]);
                }
            }
            
            // Login user
            Auth::login($user);
            
            return redirect()->intended('/dashboard')->with('success', 'Berhasil login dengan Google!');
            
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }
}

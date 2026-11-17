<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\ItContact;
use App\Mail\CompanyWelcomeMail;
use App\Mail\AdminCompanyRegisteredMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('company.wizard', 1);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('company.wizard', 1));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our Cyber Essentials records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('company.wizard', 1);
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $rawPassword = $validated['password'];

        // 1. Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'company',
            'password' => Hash::make($rawPassword),
        ]);

        // 2. Auto create linked Company Profile
        $company = Company::create([
            'user_id' => $user->id,
            'registered_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => 'draft'
        ]);

        // 3. Auto initialize Tab 9 (IT Contact & Governance) locked with Mohammed Nasar / Nexteck Limited
        ItContact::create([
            'company_id' => $company->id,
            'name' => 'Mohammed Nasar',
            'job_role' => 'CEO & Enterprise Architect',
            'email' => 'info@nexteck.co.uk',
            'phone' => '07879 175585',
            'provider_type' => 'Managed service provider (MSP)',
            'external_provider_name' => 'Nexteck Limited (London, UK)',
            'approver_admin_access' => 'Mohammed Nasar (Nexteck Limited)',
            'person_applying_patches' => 'Mohammed Nasar (Nexteck Limited)',
        ]);

        // 4. Send Welcome Email to Company (with https://cyber.nexteck.uk/ link, email and password)
        try {
            Mail::to($user->email)->send(new CompanyWelcomeMail($user, $rawPassword));
        } catch (Throwable $e) {
            Log::error('Failed to send company welcome email: ' . $e->getMessage());
        }

        // 5. Send Notification Email to Admin (info@nexteck.co.uk)
        $adminEmail = env('ADMIN_EMAIL', 'info@nexteck.co.uk');
        try {
            Mail::to($adminEmail)->send(new AdminCompanyRegisteredMail($user, $company));
        } catch (Throwable $e) {
            Log::error('Failed to send admin notification email: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('company.wizard', 1)->with('success', 'Account registered successfully! A confirmation email with your access credentials has been sent to ' . $user->email);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been safely signed out.');
    }
}

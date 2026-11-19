<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\ItContact;
use App\Mail\CompanyWelcomeMail;
use App\Services\CompanyWorkbookExportService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminController extends Controller
{
    public function dashboard()
    {
        $companies = Company::with(['user', 'staffMembers', 'computers', 'mobileDevices', 'servers', 'networkDevices', 'cloudServices', 'softwareItems', 'itContact'])->latest()->get();

        $totalCompanies = $companies->count();
        $submittedCount = $companies->where('status', 'submitted')->count();
        $draftCount = $companies->where('status', 'draft')->count();

        // Calculate aggregate risks
        $totalRisksIdentified = 0;
        foreach ($companies as $comp) {
            $totalRisksIdentified += $comp->audit_summary['risk_flags']['total_risks'];
        }

        return view('admin.dashboard', compact('companies', 'totalCompanies', 'submittedCount', 'draftCount', 'totalRisksIdentified'));
    }

    public function showCompany($id)
    {
        $company = Company::with(['user', 'staffMembers', 'computers', 'mobileDevices', 'servers', 'networkDevices', 'cloudServices', 'softwareItems', 'itContact'])->findOrFail($id);
        $audit = $company->audit_summary;

        return view('admin.view_company', compact('company', 'audit'));
    }

    public function exportCompany(CompanyWorkbookExportService $exportService, $id)
    {
        $company = Company::with(['user', 'staffMembers', 'computers', 'mobileDevices', 'servers', 'networkDevices', 'cloudServices', 'softwareItems', 'itContact'])->findOrFail($id);
        return $exportService->export($company);
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'company_number' => 'nullable|string|max:50',
            'certification_level' => 'required|string',
        ]);

        $rawPassword = $validated['password'];

        $user = User::create([
            'name' => $validated['company_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'company',
            'password' => Hash::make($rawPassword),
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'registered_name' => $validated['company_name'],
            'company_number' => $validated['company_number'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'certification_level' => $validated['certification_level'],
            'status' => 'draft'
        ]);

        // Auto initialize Tab 9 (IT Contact & Governance) locked with Mohammed Nasar / Nexteck Limited
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

        // Send welcome email to company with portal login details
        try {
            Mail::to($user->email)->send(new CompanyWelcomeMail($user, $rawPassword));
        } catch (Throwable $e) {
            Log::error('Failed to send company welcome email on admin creation: ' . $e->getMessage());
        }

        return redirect()->route('admin.dashboard')->with('success', "Company '{$validated['company_name']}' created successfully and welcome credentials emailed.");
    }

    // Admin can update Tab 9 IT Governance
    public function updateItContact(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'job_role' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'provider_type' => 'required|string',
            'external_provider_name' => 'nullable|string|max:255',
            'approver_admin_access' => 'nullable|string|max:255',
            'person_applying_patches' => 'nullable|string|max:255',
        ]);

        $company->itContact()->updateOrCreate(['company_id' => $company->id], $validated);

        return back()->with('success', 'IT Contact & Governance details updated successfully by Administrator.');
    }

    public function updateStatus(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,approved,flagged',
        ]);

        $company->update(['status' => $validated['status']]);
        return back()->with('success', "Company assessment status updated to '{$validated['status']}'.");
    }

    public function deleteCompany($id)
    {
        $company = Company::findOrFail($id);
        $user = $company->user;
        $company->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.dashboard')->with('success', 'Company record and access safely removed.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\StaffMember;
use App\Models\Computer;
use App\Models\MobileDevice;
use App\Models\Server;
use App\Models\NetworkDevice;
use App\Models\CloudService;
use App\Models\SoftwareItem;
use App\Models\ItContact;
use Illuminate\Support\Facades\Auth;

class CompanyWizardController extends Controller
{
    private function getCompany()
    {
        $user = Auth::user();
        $company = Company::firstOrCreate(
            ['user_id' => $user->id],
            [
                'registered_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => 'draft'
            ]
        );
        return $company;
    }

    public function showStep($step = 1)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 9) {
            $step = 1;
        }

        $company = $this->getCompany();
        $audit = $company->audit_summary;

        return view('company.wizard', compact('company', 'step', 'audit'));
    }

    public function summary()
    {
        $company = $this->getCompany();
        $audit = $company->audit_summary;
        return view('company.summary', compact('company', 'audit'));
    }

    public function submitAssessment(Request $request)
    {
        $company = $this->getCompany();
        $company->update([
            'status' => 'submitted',
            'date_completed' => now()->toDateString(),
            'completed_by' => Auth::user()->name
        ]);

        return redirect()->route('company.summary')->with('success', 'Cyber Essentials Information Gathering Workbook submitted successfully for review!');
    }

    // Step 1: Save Company Details
    public function saveStep1(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'registered_name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'company_number' => 'required|string|max:50',
            'legal_structure' => 'nullable|string|max:100',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:20',
            'other_address1' => 'nullable|string|max:255',
            'other_address2' => 'nullable|string|max:255',
            'other_address3' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'headcount' => 'nullable|integer|min:0',
            'sic_code' => 'nullable|string|max:50',
            'certification_level' => 'required|string',
            'certification_scope' => 'required|string',
            'completed_by' => 'nullable|string|max:255',
        ]);

        $company->update($validated);

        return redirect()->route('company.wizard', 2)->with('success', 'Company details saved successfully.');
    }

    // Step 2: Staff
    public function addStaff(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'job_role' => 'nullable|string|max:255',
            'type' => 'required|string',
            'works_from_home' => 'required|string',
            'uses_personal_device' => 'required|string',
            'has_admin_rights' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $company->staffMembers()->create($validated);
        return back()->with('success', 'Staff member added to scope.');
    }

    public function deleteStaff($id)
    {
        $company = $this->getCompany();
        $company->staffMembers()->where('id', $id)->delete();
        return back()->with('success', 'Staff member removed from scope.');
    }

    // Step 3: Computers
    public function addComputer(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'device_type' => 'required|string',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'operating_system' => 'required|string|max:100',
            'os_version' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'assigned_to' => 'nullable|string|max:255',
            'os_supported' => 'required|string',
            'auto_updates_on' => 'required|string',
            'security_software_on' => 'required|string',
        ]);

        $company->computers()->create($validated);
        return back()->with('success', 'Computer asset added.');
    }

    public function deleteComputer($id)
    {
        $company = $this->getCompany();
        $company->computers()->where('id', $id)->delete();
        return back()->with('success', 'Computer asset removed.');
    }

    // Step 4: Mobiles & Tablets
    public function addMobile(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'device_type' => 'required|string',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'operating_system' => 'required|string|max:100',
            'os_version' => 'nullable|string|max:100',
            'ownership' => 'required|string',
            'used_for_work_email' => 'required|string',
            'assigned_to' => 'nullable|string|max:255',
            'screen_lock' => 'required|string',
            'os_supported' => 'required|string',
        ]);

        $company->mobileDevices()->create($validated);
        return back()->with('success', 'Mobile/Tablet device registered.');
    }

    public function deleteMobile($id)
    {
        $company = $this->getCompany();
        $company->mobileDevices()->where('id', $id)->delete();
        return back()->with('success', 'Mobile device removed.');
    }

    // Step 5: Servers
    public function addServer(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'server_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'operating_system' => 'nullable|string|max:100',
            'os_version' => 'nullable|string|max:100',
            'type' => 'required|string',
            'location' => 'nullable|string|max:100',
            'os_supported' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $company->servers()->create($validated);
        return back()->with('success', 'Server infrastructure logged.');
    }

    public function deleteServer($id)
    {
        $company = $this->getCompany();
        $company->servers()->where('id', $id)->delete();
        return back()->with('success', 'Server removed.');
    }

    // Step 6: Network (Router / Firewall)
    public function addNetworkDevice(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'location' => 'required|string|max:100',
            'device_type' => 'required|string',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'firmware_version' => 'nullable|string|max:100',
            'default_password_changed' => 'required|string',
            'firewall_enabled' => 'required|string',
            'internet_admin_blocked' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $company->networkDevices()->create($validated);
        return back()->with('success', 'Network device recorded.');
    }

    public function deleteNetworkDevice($id)
    {
        $company = $this->getCompany();
        $company->networkDevices()->where('id', $id)->delete();
        return back()->with('success', 'Network device removed.');
    }

    // Step 7: Cloud Services
    public function addCloudService(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:100',
            'users_count' => 'required|integer|min:1',
            'admin_owner' => 'nullable|string|max:255',
            'mfa_enabled' => 'required|string',
            'holds_sensitive_data' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $company->cloudServices()->create($validated);
        return back()->with('success', 'Cloud service added.');
    }

    public function deleteCloudService($id)
    {
        $company = $this->getCompany();
        $company->cloudServices()->where('id', $id)->delete();
        return back()->with('success', 'Cloud service removed.');
    }

    // Step 8: Software
    public function addSoftware(Request $request)
    {
        $company = $this->getCompany();
        $validated = $request->validate([
            'category' => 'nullable|string|max:100',
            'software_name' => 'required|string|max:255',
            'version' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:100',
            'installed_on' => 'nullable|string|max:255',
            'auto_update_on' => 'required|string',
            'still_supported' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $company->softwareItems()->create($validated);
        return back()->with('success', 'Software item registered in inventory.');
    }

    public function deleteSoftware($id)
    {
        $company = $this->getCompany();
        $company->softwareItems()->where('id', $id)->delete();
        return back()->with('success', 'Software item removed.');
    }

    // Step 9: IT Contact
    public function saveStep9(Request $request)
    {
        $company = $this->getCompany();
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

        return redirect()->route('company.summary')->with('success', 'IT Contact details saved. Review your Completeness & Risk checks.');
    }
}

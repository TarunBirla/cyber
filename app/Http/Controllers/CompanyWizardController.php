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
use App\Services\CompanyWorkbookExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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

        // Ensure Tab 9 IT Contact exists with Mohammed Nasar / Nexteck Limited defaults
        if (!$company->itContact) {
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
        }

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

        $staffMembers = ($step == 2) ? $company->staffMembers()->paginate(10)->withQueryString() : collect();
        $computers = ($step == 3) ? $company->computers()->paginate(10)->withQueryString() : collect();
        $mobileDevices = ($step == 4) ? $company->mobileDevices()->paginate(10)->withQueryString() : collect();
        $servers = ($step == 5) ? $company->servers()->paginate(10)->withQueryString() : collect();
        $networkDevices = ($step == 6) ? $company->networkDevices()->paginate(10)->withQueryString() : collect();
        $cloudServices = ($step == 7) ? $company->cloudServices()->paginate(10)->withQueryString() : collect();
        $softwareItems = ($step == 8) ? $company->softwareItems()->paginate(10)->withQueryString() : collect();

        return view('company.wizard', compact(
            'company',
            'step',
            'audit',
            'staffMembers',
            'computers',
            'mobileDevices',
            'servers',
            'networkDevices',
            'cloudServices',
            'softwareItems'
        ));
    }

    public function summary()
    {
        $company = $this->getCompany();
        $audit = $company->audit_summary;
        return view('company.summary', compact('company', 'audit'));
    }

    public function exportWorkbook(CompanyWorkbookExportService $exportService)
    {
        $company = $this->getCompany();
        return $exportService->export($company);
    }

    public function submitAssessment(Request $request)
    {
        $company = $this->getCompany();
        $company->update([
            'status' => 'submitted',
            'date_completed' => now()->toDateString(),
            'completed_by' => Auth::user()->name
        ]);

        return redirect()->route('company.summary')->with('success', 'Cyber Security Information Gathering Workbook submitted successfully for review!');
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
        return back()->with('success', 'Computer asset added successfully.');
    }

    /**
     * Non-technical users: Upload JSON produced by device-register.js
     * Automatically parse and register the computer asset.
     */
    public function uploadDeviceScan(Request $request)
    {
        $company = $this->getCompany();
        
        $request->validate([
            'device_file' => 'required|file|mimes:json,txt|max:5120',
            'assigned_to' => 'required|string|max:255',
        ]);

        try {
            $content = file_get_contents($request->file('device_file')->getRealPath());
            // Strip UTF-8 BOM if generated by Windows PowerShell
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $content = trim($content);
            $json = json_decode($content, true);

            if (!$json) {
                return back()->withErrors(['device_file' => 'The uploaded file is not a valid JSON output.']);
            }

            // Extract device object from script format { generatedAt: ..., device: { ... } } or direct { ... }
            $device = $json['device'] ?? ($json['devices'][0] ?? $json);

            // Map fields cleanly
            $deviceType = $device['Device type'] ?? 'Laptop';
            if (!in_array($deviceType, ['Laptop', 'Desktop', 'Workstation'])) {
                $deviceType = 'Laptop';
            }

            $osSupported = ($device['OS still supported?'] === 'Yes') ? 'Yes' : 'No';
            $autoUpdates = ($device['Auto-updates on?'] === 'Yes') ? 'Yes' : 'No';
            $securityOn = ($device['Firewall & anti-malware on?'] === 'Yes') ? 'Yes' : 'No';
            $assignedTo = trim($request->input('assigned_to', $device['Assigned to / user(s)'] ?? auth()->user()->name));

            $computer = $company->computers()->create([
                'device_type' => $deviceType,
                'make' => $device['Make'] ?? 'Unknown',
                'model' => $device['Model'] ?? 'Unknown Model',
                'quantity' => (int)($device['Quantity'] ?? 1),
                'operating_system' => $device['Operating system'] ?? 'Windows / macOS',
                'os_version' => $device['OS version'] ?? '',
                'location' => $device['Location'] ?? 'Office / Home',
                'assigned_to' => $assignedTo,
                'os_supported' => $osSupported,
                'auto_updates_on' => $autoUpdates,
                'security_software_on' => $securityOn,
            ]);

            return back()->with('success', "Auto-Scan Success! Computer '{$computer->make} {$computer->model}' ({$computer->operating_system}) automatically registered and assigned to '{$computer->assigned_to}'.");

        } catch (\Throwable $e) {
            return back()->withErrors(['device_file' => 'Failed to parse device file: ' . $e->getMessage()]);
        }
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
            'assigned_to' => 'nullable|string|max:255',
            'installed_on' => 'nullable|string|max:255',
            'auto_update_on' => 'required|string',
            'still_supported' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['installed_on']) && !empty($validated['assigned_to'])) {
            $validated['installed_on'] = "Assigned to {$validated['assigned_to']}";
        }

        $company->softwareItems()->create($validated);
        return back()->with('success', 'Software item registered in inventory.');
    }

    /**
     * Non-technical users: Upload JSON produced by list-installed-software.js
     * Automatically registers applications into the inventory with categories & support check.
     */
    public function uploadSoftwareScan(Request $request)
    {
        $company = $this->getCompany();

        $request->validate([
            'software_file' => 'required|file|mimes:json,txt|max:10240',
            'assigned_to' => 'required|string|max:255',
        ]);

        try {
            $content = file_get_contents($request->file('software_file')->getRealPath());
            // Strip UTF-8 BOM if generated by Windows PowerShell
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $content = trim($content);
            $json = json_decode($content, true);

            if (!$json) {
                return back()->withErrors(['software_file' => 'The uploaded file is not valid JSON.']);
            }

            $rawSoftware = $json['software'] ?? ($json['apps'] ?? (is_array($json) && isset($json[0]['name']) ? $json : []));

            if (empty($rawSoftware)) {
                return back()->withErrors(['software_file' => 'No software entries found in the uploaded file.']);
            }

            // Extract high/critical security findings if script was run with --security
            $securityFindings = [];
            if (!empty($json['security']['findings'])) {
                foreach ($json['security']['findings'] as $f) {
                    $key = strtolower(trim($f['software']));
                    $securityFindings[$key] = $f['reason'] ?? 'Security exposure detected';
                }
            }

            $machineName = $json['machine'] ?? 'Laptops & Workstations';
            $assignedTo = trim($request->input('assigned_to'));
            $importedCount = 0;

            // Ensure assigned_to column exists on software_items table
            if (Schema::hasTable('software_items') && !Schema::hasColumn('software_items', 'assigned_to')) {
                try {
                    Schema::table('software_items', function ($table) {
                        $table->string('assigned_to')->nullable()->after('installed_on');
                    });
                } catch (\Throwable $e) {}
            }
            $hasAssignedToCol = Schema::hasColumn('software_items', 'assigned_to');

            foreach ($rawSoftware as $app) {
                $name = trim($app['name'] ?? ($app['DisplayName'] ?? ''));
                if (empty($name)) continue;

                $version = trim($app['version'] ?? ($app['DisplayVersion'] ?? 'Latest'));
                $publisher = trim($app['publisher'] ?? ($app['Publisher'] ?? ''));

                // Auto-categorize based on software name
                $category = 'Other';
                $nameLower = strtolower($name);

                if (preg_match('/chrome|firefox|edge|safari|opera|brave/i', $nameLower)) {
                    $category = 'Web browser';
                } elseif (preg_match('/defender|antivirus|malware|sophos|crowdstrike|sentinel|eset|bitdefender|mcafee|norton|kaspersky|avast/i', $nameLower)) {
                    $category = 'Antivirus / EDR';
                } elseif (preg_match('/outlook|thunderbird|mail/i', $nameLower)) {
                    $category = 'Email client';
                } elseif (preg_match('/office|word|excel|powerpoint|teams|zoom|slack|adobe|acrobat|notion/i', $nameLower)) {
                    $category = 'Office productivity';
                }

                // Check for EOL / unsupported software
                $stillSupported = 'Yes';
                $notes = [];

                if (preg_match('/flash player|shockwave|silverlight|quicktime|python 2|office 2010|office 2013/i', $nameLower)) {
                    $stillSupported = 'No';
                    $notes[] = 'Out of support / End of Life software';
                }

                if (isset($securityFindings[$nameLower])) {
                    $notes[] = $securityFindings[$nameLower];
                }

                $installedOnDesc = $assignedTo ? "Assigned to {$assignedTo} ({$machineName})" : "Installed on {$machineName}";

                // Avoid duplicate exact names for same company
                $existing = $company->softwareItems()->where('software_name', $name)->first();
                if ($existing) {
                    $updateData = [
                        'version' => $version ?: $existing->version,
                        'vendor' => $publisher ?: $existing->vendor,
                        'installed_on' => $installedOnDesc,
                        'still_supported' => $stillSupported,
                        'notes' => count($notes) ? implode('; ', $notes) : $existing->notes,
                    ];
                    if ($hasAssignedToCol) {
                        $updateData['assigned_to'] = $assignedTo;
                    }
                    $existing->update($updateData);
                } else {
                    $createData = [
                        'category' => $category,
                        'software_name' => $name,
                        'version' => $version,
                        'vendor' => $publisher,
                        'installed_on' => $installedOnDesc,
                        'auto_update_on' => 'Yes',
                        'still_supported' => $stillSupported,
                        'notes' => count($notes) ? implode('; ', $notes) : null,
                    ];
                    if ($hasAssignedToCol) {
                        $createData['assigned_to'] = $assignedTo;
                    }
                    $company->softwareItems()->create($createData);
                    $importedCount++;
                }
            }

            return back()->with('success', "Auto-Scan Success! Registered/Updated {$importedCount} software applications assigned to '{$assignedTo}'.");

        } catch (\Throwable $e) {
            return back()->withErrors(['software_file' => 'Failed to process software inventory: ' . $e->getMessage()]);
        }
    }

    public function deleteSoftware($id)
    {
        $company = $this->getCompany();
        $company->softwareItems()->where('id', $id)->delete();
        return back()->with('success', 'Software item removed.');
    }

    // Step 9: IT Contact (Company confirmation - locked by default to Nexteck Limited)
    public function saveStep9(Request $request)
    {
        return redirect()->route('company.summary')->with('success', 'IT Governance confirmed. Verified with Lead Auditor (Nexteck Limited).');
    }
}

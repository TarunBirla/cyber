<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\StaffMember;
use App\Models\Computer;
use App\Models\MobileDevice;
use App\Models\Server;
use App\Models\NetworkDevice;
use App\Models\CloudService;
use App\Models\SoftwareItem;
use App\Models\ItContact;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Admin (Mohammed Nasar - Nexteck Limited)
        $admin = User::updateOrCreate(
            ['email' => 'info@nexteck.co.uk'],
            [
                'name' => 'Mohammed Nasar',
                'phone' => '07879 175585',
                'role' => 'admin',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        // Also keep legacy alias admin if needed or update it
        User::where('email', 'admin@cyberessentials.uk')->delete();

        // 2. Create Sample Demo Company (from Workbook example rows)
        $companyUser = User::firstOrCreate(
            ['email' => 'contact@acmeltd.co.uk'],
            [
                'name' => 'Acme Technologies Ltd',
                'phone' => '+44 20 7123 4567',
                'role' => 'company',
                'password' => Hash::make('Password@123'),
            ]
        );

        $company = Company::firstOrCreate(
            ['user_id' => $companyUser->id],
            [
                'registered_name' => 'Acme Technologies Ltd',
                'trading_name' => 'Acme Tech',
                'company_number' => '12345678',
                'legal_structure' => 'Ltd',
                'address_line1' => '100 Silicon Roundabout',
                'address_line2' => 'Tech City',
                'city' => 'London',
                'postcode' => 'EC1V 1AB',
                'website' => 'https://acmetech.example.uk',
                'phone' => '+44 20 7123 4567',
                'email' => 'contact@acmeltd.co.uk',
                'headcount' => 12,
                'sic_code' => '62020',
                'certification_level' => 'Cyber Security',
                'certification_scope' => 'Whole company',
                'date_completed' => now()->toDateString(),
                'completed_by' => 'Jane Smith',
                'status' => 'draft'
            ]
        );

        // Staff Sample
        if ($company->staffMembers()->count() === 0) {
            StaffMember::create([
                'company_id' => $company->id,
                'full_name' => 'Jane Smith',
                'job_role' => 'Operations Manager',
                'type' => 'Employee',
                'works_from_home' => 'Hybrid',
                'uses_personal_device' => 'No',
                'has_admin_rights' => 'No',
                'notes' => 'Primary contact for internal ops'
            ]);
        }

        // Computer Sample
        if ($company->computers()->count() === 0) {
            Computer::create([
                'company_id' => $company->id,
                'device_type' => 'Laptop',
                'make' => 'Dell',
                'model' => 'Latitude 5440',
                'quantity' => 10,
                'operating_system' => 'Windows 11 Pro',
                'os_version' => '24H2',
                'location' => 'Office / Home',
                'assigned_to' => 'All staff',
                'os_supported' => 'Yes',
                'auto_updates_on' => 'Yes',
                'security_software_on' => 'Yes'
            ]);
        }

        // Mobile Sample
        if ($company->mobileDevices()->count() === 0) {
            MobileDevice::create([
                'company_id' => $company->id,
                'device_type' => 'Phone',
                'make' => 'Apple',
                'model' => 'iPhone 14',
                'quantity' => 5,
                'operating_system' => 'iOS',
                'os_version' => '18',
                'ownership' => 'Personal',
                'used_for_work_email' => 'Yes',
                'assigned_to' => 'Sales team',
                'screen_lock' => 'Yes',
                'os_supported' => 'Yes'
            ]);
        }

        // Server Sample
        if ($company->servers()->count() === 0) {
            Server::create([
                'company_id' => $company->id,
                'server_name' => 'File Server Primary',
                'quantity' => 1,
                'operating_system' => 'Windows Server',
                'os_version' => '2022',
                'type' => 'Physical',
                'location' => 'Office comms room',
                'os_supported' => 'Yes',
                'notes' => 'Active Directory & File storage'
            ]);
        }

        // Network Sample
        if ($company->networkDevices()->count() === 0) {
            NetworkDevice::create([
                'company_id' => $company->id,
                'location' => 'Head Office',
                'device_type' => 'Router/Firewall',
                'make' => 'Ubiquiti',
                'model' => 'UniFi Dream Machine Pro',
                'firmware_version' => '3.2.x',
                'default_password_changed' => 'Yes',
                'firewall_enabled' => 'Yes',
                'internet_admin_blocked' => 'Yes',
                'notes' => 'Primary perimeter gateway'
            ]);
        }

        // Cloud Service Sample
        if ($company->cloudServices()->count() === 0) {
            CloudService::create([
                'company_id' => $company->id,
                'service_name' => 'Microsoft 365',
                'purpose' => 'Email & office apps',
                'provider' => 'Microsoft',
                'users_count' => 12,
                'admin_owner' => 'Jane Smith',
                'mfa_enabled' => 'Yes',
                'holds_sensitive_data' => 'Yes',
                'notes' => 'MFA enforced via conditional access'
            ]);
        }

        // Software Sample
        if ($company->softwareItems()->count() === 0) {
            SoftwareItem::create([
                'company_id' => $company->id,
                'category' => 'Web browser',
                'software_name' => 'Google Chrome',
                'version' => 'Latest',
                'vendor' => 'Google',
                'installed_on' => 'All laptops & phones',
                'auto_update_on' => 'Yes',
                'still_supported' => 'Yes',
                'notes' => 'Managed via Google Workspace/InTune'
            ]);
        }

        // IT Contact Sample (Locked to Mohammed Nasar / Nexteck Limited)
        ItContact::updateOrCreate(
            ['company_id' => $company->id],
            [
                'name' => 'Mohammed Nasar',
                'job_role' => 'CEO & Enterprise Architect',
                'email' => 'info@nexteck.co.uk',
                'phone' => '07879 175585',
                'provider_type' => 'Managed service provider (MSP)',
                'external_provider_name' => 'Nexteck Limited (London, UK)',
                'approver_admin_access' => 'Mohammed Nasar (Nexteck Limited)',
                'person_applying_patches' => 'Mohammed Nasar (Nexteck Limited)'
            ]
        );
    }
}

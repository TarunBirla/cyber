<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Company Details
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('registered_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('company_number')->nullable();
            $table->string('legal_structure')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('other_address1')->nullable();
            $table->string('other_address2')->nullable();
            $table->string('other_address3')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('headcount')->nullable()->default(0);
            $table->string('sic_code')->nullable();
            $table->string('certification_level')->default('Cyber Essentials'); // Cyber Essentials or Cyber Essentials Plus
            $table->string('certification_scope')->default('Whole company');
            $table->date('date_completed')->nullable();
            $table->string('completed_by')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved, flagged
            $table->timestamps();
        });

        // 2. Staff
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('job_role')->nullable();
            $table->string('type')->default('Employee'); // Employee, Contractor, Volunteer
            $table->string('works_from_home')->default('No'); // Yes, No, Hybrid
            $table->string('uses_personal_device')->default('No'); // Yes, No
            $table->string('has_admin_rights')->default('No'); // Yes, No
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Computers (Laptops & Desktops)
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('device_type')->default('Laptop'); // Laptop, Desktop, Workstation
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('operating_system')->nullable();
            $table->string('os_version')->nullable();
            $table->string('location')->nullable();
            $table->string('assigned_to')->nullable();
            $table->string('os_supported')->default('Yes'); // Yes, No
            $table->string('auto_updates_on')->default('Yes'); // Yes, No
            $table->string('security_software_on')->default('Yes'); // Firewall & anti-malware (Yes, No)
            $table->timestamps();
        });

        // 4. Mobiles & Tablets
        Schema::create('mobile_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('device_type')->default('Phone'); // Phone, Tablet
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('operating_system')->nullable();
            $table->string('os_version')->nullable();
            $table->string('ownership')->default('Company'); // Company, Personal
            $table->string('used_for_work_email')->default('Yes'); // Yes, No
            $table->string('assigned_to')->nullable();
            $table->string('screen_lock')->default('Yes'); // PIN/biometric (Yes, No)
            $table->string('os_supported')->default('Yes'); // Yes, No
            $table->timestamps();
        });

        // 5. Servers
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('server_name');
            $table->integer('quantity')->default(1);
            $table->string('operating_system')->nullable();
            $table->string('os_version')->nullable();
            $table->string('type')->default('Physical'); // Physical, Virtual, Cloud VM, NAS
            $table->string('location')->nullable();
            $table->string('os_supported')->default('Yes'); // Yes, No
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Network Devices (Routers / Firewalls)
        Schema::create('network_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('location')->nullable();
            $table->string('device_type')->default('Router/Firewall');
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('default_password_changed')->default('Yes'); // Yes, No (Crucial flag!)
            $table->string('firewall_enabled')->default('Yes'); // Yes, No
            $table->string('internet_admin_blocked')->default('Yes'); // Yes, No
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Cloud Services
        Schema::create('cloud_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('service_name');
            $table->string('purpose')->nullable();
            $table->string('provider')->nullable();
            $table->integer('users_count')->default(1);
            $table->string('admin_owner')->nullable();
            $table->string('mfa_enabled')->default('Yes'); // Yes, No (Crucial flag!)
            $table->string('holds_sensitive_data')->default('No'); // Yes, No
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Software
        Schema::create('software_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable(); // Web browser, Antivirus, Email client, Office suite
            $table->string('software_name');
            $table->string('version')->nullable();
            $table->string('vendor')->nullable();
            $table->string('installed_on')->nullable(); // e.g. All laptops & phones
            $table->string('auto_update_on')->default('Yes'); // Yes, No
            $table->string('still_supported')->default('Yes'); // Yes, No
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. IT Contact & Governance
        Schema::create('it_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('job_role')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('provider_type')->default('Internal'); // Internal, Managed service provider (MSP)
            $table->string('external_provider_name')->nullable();
            $table->string('approver_admin_access')->nullable();
            $table->string('person_applying_patches')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_contacts');
        Schema::dropIfExists('software_items');
        Schema::dropIfExists('cloud_services');
        Schema::dropIfExists('network_devices');
        Schema::dropIfExists('servers');
        Schema::dropIfExists('mobile_devices');
        Schema::dropIfExists('computers');
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('companies');
    }
};

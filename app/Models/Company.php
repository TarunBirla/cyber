<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class);
    }

    public function computers()
    {
        return $this->hasMany(Computer::class);
    }

    public function mobileDevices()
    {
        return $this->hasMany(MobileDevice::class);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function networkDevices()
    {
        return $this->hasMany(NetworkDevice::class);
    }

    public function cloudServices()
    {
        return $this->hasMany(CloudService::class);
    }

    public function softwareItems()
    {
        return $this->hasMany(SoftwareItem::class);
    }

    public function itContact()
    {
        return $this->hasOne(ItContact::class);
    }

    /**
     * Compute Cyber Essentials Compliance and Risk flags matching Excel summary
     */
    public function getAuditSummaryAttribute()
    {
        $staffCount = $this->staffMembers()->count();
        $wfhCount = $this->staffMembers()->whereIn('works_from_home', ['Yes', 'Hybrid'])->count();
        $personalDeviceStaff = $this->staffMembers()->where('uses_personal_device', 'Yes')->count();

        $compQty = (int) $this->computers()->sum('quantity');
        $mobileQty = (int) $this->mobileDevices()->sum('quantity');
        $serverQty = (int) $this->servers()->sum('quantity');
        $networkCount = $this->networkDevices()->count();
        $cloudCount = $this->cloudServices()->count();
        $softwareCount = $this->softwareItems()->count();

        // Single entry key fields check
        $companyFilled = !empty($this->registered_name) && !empty($this->company_number) && !empty($this->address_line1);
        $itContact = $this->itContact;
        $itContactFilled = $itContact && !empty($itContact->name) && !empty($itContact->email);

        // Risk Flags (must ideally be 0 for passing Cyber Essentials)
        $unsupportedComputers = $this->computers()->where('os_supported', 'No')->sum('quantity');
        $unsupportedMobiles = $this->mobileDevices()->where('os_supported', 'No')->sum('quantity');
        $unsupportedServers = $this->servers()->where('os_supported', 'No')->sum('quantity');
        $cloudNoMfa = $this->cloudServices()->where('mfa_enabled', 'No')->count();
        $routerDefaultPass = $this->networkDevices()->where('default_password_changed', 'No')->count();
        $unsupportedSoftware = $this->softwareItems()->where('still_supported', 'No')->count();

        $totalRisks = $unsupportedComputers + $unsupportedMobiles + $unsupportedServers + $cloudNoMfa + $routerDefaultPass + $unsupportedSoftware;

        // Completeness score
        $checks = [
            $companyFilled,
            $staffCount > 0,
            $compQty > 0,
            $mobileQty > 0,
            $networkCount > 0,
            $cloudCount > 0,
            $softwareCount > 0,
            $itContactFilled
        ];
        $completedSteps = count(array_filter($checks));
        $percentComplete = round(($completedSteps / count($checks)) * 100);

        return [
            'staff_count' => $staffCount,
            'wfh_count' => $wfhCount,
            'personal_device_staff' => $personalDeviceStaff,
            'computer_qty' => $compQty,
            'mobile_qty' => $mobileQty,
            'server_qty' => $serverQty,
            'network_count' => $networkCount,
            'cloud_count' => $cloudCount,
            'software_count' => $softwareCount,
            'company_details_ready' => $companyFilled,
            'it_contact_ready' => $itContactFilled,
            'risk_flags' => [
                'computers_unsupported' => $unsupportedComputers,
                'mobiles_unsupported' => $unsupportedMobiles,
                'servers_unsupported' => $unsupportedServers,
                'cloud_no_mfa' => $cloudNoMfa,
                'router_default_password' => $routerDefaultPass,
                'software_unsupported' => $unsupportedSoftware,
                'total_risks' => $totalRisks,
            ],
            'percent_complete' => $percentComplete,
            'status' => $totalRisks === 0 && $percentComplete === 100 ? 'Compliant & Ready' : ($totalRisks > 0 ? 'Risk Alerts Detected' : 'In Progress')
        ];
    }
}

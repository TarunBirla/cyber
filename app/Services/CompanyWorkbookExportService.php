<?php

namespace App\Services;

use App\Models\Company;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class CompanyWorkbookExportService
{
    /**
     * Generate and stream the XLSX workbook for a given company.
     */
    public function export(Company $company): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet($company);

        $nameCandidate = $company->registered_name ?: ($company->user->name ?? 'Company');
        $safeCompanyName = Str::slug($nameCandidate, '_') ?: 'Company';
        $filename = "Cyber_Essentials_{$safeCompanyName}_Workbook.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            // Write directly to php://output
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Build the spreadsheet using the original template and company data.
     */
    public function buildSpreadsheet(Company $company): Spreadsheet
    {
        // 1. Locate template
        $templatePath = resource_path('templates/Cyber_Essentials_Information_Workbook.xlsx');
        if (!file_exists($templatePath)) {
            $templatePath = base_path('resources/templates/Cyber_Essentials_Information_Workbook.xlsx');
        }

        if (file_exists($templatePath)) {
            $spreadsheet = IOFactory::load($templatePath);
        } else {
            $spreadsheet = new Spreadsheet();
        }

        // Make sure all relations are eager loaded if company persisted
        if ($company->exists) {
            $company->loadMissing([
                'user',
                'staffMembers',
                'computers',
                'mobileDevices',
                'servers',
                'networkDevices',
                'cloudServices',
                'softwareItems',
                'itContact'
            ]);
        }

        // 2. Populate Tab 1: Company
        $this->populateCompanyDetails($spreadsheet, $company);

        // 3. Populate Tab 2: Staff
        $staffCount = $this->populateStaff($spreadsheet, $company);

        // 4. Populate Tab 3: Computers
        $compCount = $this->populateComputers($spreadsheet, $company);

        // 5. Populate Tab 4: Mobiles & Tablets
        $mobCount = $this->populateMobiles($spreadsheet, $company);

        // 6. Populate Tab 5: Servers
        $servCount = $this->populateServers($spreadsheet, $company);

        // 7. Populate Tab 6: Network
        $netCount = $this->populateNetwork($spreadsheet, $company);

        // 8. Populate Tab 7: Cloud Services
        $cloudCount = $this->populateCloud($spreadsheet, $company);

        // 9. Populate Tab 8: Software
        $softCount = $this->populateSoftware($spreadsheet, $company);

        // 10. Populate Tab 9: IT Contact
        $this->populateItContact($spreadsheet, $company);

        // 11. Recalculate / Update Summary Tab formulas
        $this->updateSummaryFormulas(
            $spreadsheet,
            $staffCount,
            $compCount,
            $mobCount,
            $servCount,
            $netCount,
            $cloudCount,
            $softCount
        );

        return $spreadsheet;
    }

    /**
     * Tab 1: Company Details
     */
    protected function populateCompanyDetails(Spreadsheet $spreadsheet, Company $company): void
    {
        $sheet = $spreadsheet->getSheetByName('1 Company');
        if (!$sheet) {
            return;
        }

        $sheet->setCellValue('B5', $company->registered_name ?: ($company->user->name ?? ''));
        $sheet->setCellValue('B6', $company->trading_name ?? '');
        $sheet->setCellValue('B7', $company->company_number ?? '');
        $sheet->setCellValue('B8', $company->legal_structure ?? '');
        $sheet->setCellValue('B9', $company->address_line1 ?? '');
        $sheet->setCellValue('B10', $company->address_line2 ?? '');
        $sheet->setCellValue('B11', $company->city ?? '');
        $sheet->setCellValue('B12', $company->postcode ?? '');
        $sheet->setCellValue('B13', $company->other_address1 ?? '');
        $sheet->setCellValue('B14', $company->other_address2 ?? '');
        $sheet->setCellValue('B15', $company->other_address3 ?? '');
        $sheet->setCellValue('B16', $company->website ?? '');
        $sheet->setCellValue('B17', $company->phone ?? '');
        $sheet->setCellValue('B18', $company->email ?: ($company->user->email ?? ''));
        $sheet->setCellValue('B19', $company->headcount ?? 0);
        $sheet->setCellValue('B20', $company->sic_code ?? '');
        $sheet->setCellValue('B21', $company->certification_level ?? 'Cyber Essentials');
        $sheet->setCellValue('B22', $company->certification_scope ?? 'Whole company');
        $sheet->setCellValue('B23', $company->date_completed ? date('d/m/Y', strtotime($company->date_completed)) : date('d/m/Y'));
        $sheet->setCellValue('B24', $company->completed_by ?: ($company->user->name ?? 'Administrator'));
    }

    /**
     * Tab 2: Staff
     */
    protected function populateStaff(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('2 Staff');
        if (!$sheet) {
            return 0;
        }

        $items = $company->staffMembers;
        $count = $items->count();

        // Clear default template example row
        for ($col = 'A'; $col <= 'G'; $col++) {
            $sheet->setCellValue($col . '5', null);
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $sheet->setCellValue('A' . $row, $item->full_name);
                $sheet->setCellValue('B' . $row, $item->job_role ?? '');
                $sheet->setCellValue('C' . $row, $item->type ?? 'Employee');
                $sheet->setCellValue('D' . $row, $item->works_from_home ?? 'No');
                $sheet->setCellValue('E' . $row, $item->uses_personal_device ?? 'No');
                $sheet->setCellValue('F' . $row, $item->has_admin_rights ?? 'No');
                $sheet->setCellValue('G' . $row, $item->notes ?? '');

                $sheet->getStyle("A{$row}:G{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 3: Computers
     */
    protected function populateComputers(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('3 Computers');
        if (!$sheet) {
            return 0;
        }

        $items = $company->computers;
        $count = $items->count();

        // Clear template example row
        for ($col = 'A'; $col <= 'K'; $col++) {
            $sheet->setCellValue($col . '5', null);
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $sheet->setCellValue('A' . $row, $item->device_type ?? 'Laptop');
                $sheet->setCellValue('B' . $row, $item->make ?? '');
                $sheet->setCellValue('C' . $row, $item->model ?? '');
                $sheet->setCellValue('D' . $row, (int)($item->quantity ?: 1));
                $sheet->setCellValue('E' . $row, $item->operating_system ?? '');
                $sheet->setCellValue('F' . $row, $item->os_version ?? '');
                $sheet->setCellValue('G' . $row, $item->location ?? '');
                $sheet->setCellValue('H' . $row, $item->assigned_to ?? '');
                $sheet->setCellValue('I' . $row, $item->os_supported ?? 'Yes');
                $sheet->setCellValue('J' . $row, $item->auto_updates_on ?? 'Yes');
                $sheet->setCellValue('K' . $row, $item->security_software_on ?? 'Yes');

                $sheet->getStyle("A{$row}:K{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 4: Mobiles & Tablets
     */
    protected function populateMobiles(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('4 Mobiles & Tablets');
        if (!$sheet) {
            return 0;
        }

        $items = $company->mobileDevices;
        $count = $items->count();

        // Clear template example rows 5 and 6
        for ($r = 5; $r <= 6; $r++) {
            for ($col = 'A'; $col <= 'K'; $col++) {
                $sheet->setCellValue($col . $r, null);
            }
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $sheet->setCellValue('A' . $row, $item->device_type ?? 'Phone');
                $sheet->setCellValue('B' . $row, $item->make ?? '');
                $sheet->setCellValue('C' . $row, $item->model ?? '');
                $sheet->setCellValue('D' . $row, (int)($item->quantity ?: 1));
                $sheet->setCellValue('E' . $row, $item->operating_system ?? '');
                $sheet->setCellValue('F' . $row, $item->os_version ?? '');
                $sheet->setCellValue('G' . $row, $item->ownership ?? 'Company');
                $sheet->setCellValue('H' . $row, $item->used_for_work_email ?? 'Yes');
                $sheet->setCellValue('I' . $row, $item->assigned_to ?? '');
                $sheet->setCellValue('J' . $row, $item->screen_lock ?? 'Yes');
                $sheet->setCellValue('K' . $row, $item->os_supported ?? 'Yes');

                $sheet->getStyle("A{$row}:K{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 5: Servers
     */
    protected function populateServers(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('5 Servers');
        if (!$sheet) {
            return 0;
        }

        $items = $company->servers;
        $count = $items->count();

        // Clear template example row
        for ($col = 'A'; $col <= 'H'; $col++) {
            $sheet->setCellValue($col . '5', null);
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $sheet->setCellValue('A' . $row, $item->server_name ?? '');
                $sheet->setCellValue('B' . $row, (int)($item->quantity ?: 1));
                $sheet->setCellValue('C' . $row, $item->operating_system ?? '');
                $sheet->setCellValue('D' . $row, $item->os_version ?? '');
                $sheet->setCellValue('E' . $row, $item->type ?? 'Physical');
                $sheet->setCellValue('F' . $row, $item->location ?? '');
                $sheet->setCellValue('G' . $row, $item->os_supported ?? 'Yes');
                $sheet->setCellValue('H' . $row, $item->notes ?? '');

                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 6: Network (Routers / Firewalls)
     */
    protected function populateNetwork(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('6 Network');
        if (!$sheet) {
            return 0;
        }

        $items = $company->networkDevices;
        $count = $items->count();

        // Clear template example row
        for ($col = 'A'; $col <= 'I'; $col++) {
            $sheet->setCellValue($col . '5', null);
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $sheet->setCellValue('A' . $row, $item->location ?? 'Head office');
                $sheet->setCellValue('B' . $row, $item->device_type ?? 'Router/Firewall');
                $sheet->setCellValue('C' . $row, $item->make ?? '');
                $sheet->setCellValue('D' . $row, $item->model ?? '');
                $sheet->setCellValue('E' . $row, $item->firmware_version ?? '');
                $sheet->setCellValue('F' . $row, $item->default_password_changed ?? 'Yes');
                $sheet->setCellValue('G' . $row, $item->firewall_enabled ?? 'Yes');
                $sheet->setCellValue('H' . $row, $item->internet_admin_blocked ?? 'Yes');
                $sheet->setCellValue('I' . $row, $item->notes ?? '');

                $sheet->getStyle("A{$row}:I{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 7: Cloud Services
     */
    protected function populateCloud(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('7 Cloud Services');
        if (!$sheet) {
            return 0;
        }

        $items = $company->cloudServices;
        $count = $items->count();

        // Clear template example row
        for ($col = 'A'; $col <= 'H'; $col++) {
            $sheet->setCellValue($col . '5', null);
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $sheet->setCellValue('A' . $row, $item->service_name ?? '');
                $sheet->setCellValue('B' . $row, $item->purpose ?? '');
                $sheet->setCellValue('C' . $row, $item->provider ?? '');
                $sheet->setCellValue('D' . $row, (int)($item->users_count ?: 1));
                $sheet->setCellValue('E' . $row, $item->admin_owner ?? '');
                $sheet->setCellValue('F' . $row, $item->mfa_enabled ?? 'Yes');
                $sheet->setCellValue('G' . $row, $item->holds_sensitive_data ?? 'No');
                $sheet->setCellValue('H' . $row, $item->notes ?? '');

                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 8: Software
     */
    protected function populateSoftware(Spreadsheet $spreadsheet, Company $company): int
    {
        $sheet = $spreadsheet->getSheetByName('8 Software');
        if (!$sheet) {
            return 0;
        }

        $items = $company->softwareItems;
        $count = $items->count();

        // Clear template example row
        for ($col = 'A'; $col <= 'H'; $col++) {
            $sheet->setCellValue($col . '5', null);
        }

        if ($count > 0) {
            foreach ($items as $idx => $item) {
                $row = 5 + $idx;
                $installedOn = $item->installed_on ?? '';
                if (!empty($item->assigned_to)) {
                    $installedOn .= ($installedOn ? ' - ' : '') . 'Assigned to: ' . $item->assigned_to;
                }

                $sheet->setCellValue('A' . $row, $item->category ?? '');
                $sheet->setCellValue('B' . $row, $item->software_name ?? '');
                $sheet->setCellValue('C' . $row, $item->version ?? '');
                $sheet->setCellValue('D' . $row, $item->vendor ?? '');
                $sheet->setCellValue('E' . $row, $installedOn);
                $sheet->setCellValue('F' . $row, $item->auto_update_on ?? 'Yes');
                $sheet->setCellValue('G' . $row, $item->still_supported ?? 'Yes');
                $sheet->setCellValue('H' . $row, $item->notes ?? '');

                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setItalic(false);
            }
        }

        return $count;
    }

    /**
     * Tab 9: IT Contact
     */
    protected function populateItContact(Spreadsheet $spreadsheet, Company $company): void
    {
        $sheet = $spreadsheet->getSheetByName('9 IT Contact');
        if (!$sheet) {
            return;
        }

        $it = $company->itContact;
        if ($it) {
            $sheet->setCellValue('B5', $it->name ?? '');
            $sheet->setCellValue('B6', $it->job_role ?? '');
            $sheet->setCellValue('B7', $it->email ?? '');
            $sheet->setCellValue('B8', $it->phone ?? '');
            $sheet->setCellValue('B9', $it->provider_type ?? 'Internal');
            $sheet->setCellValue('B10', $it->external_provider_name ?? '');
            $sheet->setCellValue('B11', $it->approver_admin_access ?? '');
            $sheet->setCellValue('B12', $it->person_applying_patches ?? '');
        }
    }

    /**
     * Update Summary formulas to account for row 5 start and any dynamically expanded rows
     */
    protected function updateSummaryFormulas(
        Spreadsheet $spreadsheet,
        int $staffCount,
        int $compCount,
        int $mobCount,
        int $servCount,
        int $netCount,
        int $cloudCount,
        int $softCount
    ): void {
        $sheet = $spreadsheet->getSheetByName('Summary');
        if (!$sheet) {
            return;
        }

        $staffMax = max(65, 5 + $staffCount);
        $compMax = max(45, 5 + $compCount);
        $mobMax = max(45, 5 + $mobCount);
        $servMax = max(25, 5 + $servCount);
        $netMax = max(20, 5 + $netCount);
        $cloudMax = max(45, 5 + $cloudCount);
        $softMax = max(45, 5 + $softCount);

        // Inventory counts
        $sheet->setCellValue('B5', "=COUNTA('2 Staff'!A5:A{$staffMax})");
        $sheet->setCellValue('B6', "=COUNTIF('2 Staff'!D5:D{$staffMax},\"Yes\")+COUNTIF('2 Staff'!D5:D{$staffMax},\"Hybrid\")");
        $sheet->setCellValue('B7', "=COUNTIF('2 Staff'!E5:E{$staffMax},\"Yes\")");

        $sheet->setCellValue('B8', "=SUM('3 Computers'!D5:D{$compMax})");
        $sheet->setCellValue('B9', "=SUM('4 Mobiles & Tablets'!D5:D{$mobMax})");
        $sheet->setCellValue('B10', "=SUM('5 Servers'!B5:B{$servMax})");
        $sheet->setCellValue('B11', "=COUNTA('6 Network'!A5:A{$netMax})");
        $sheet->setCellValue('B12', "=COUNTA('7 Cloud Services'!A5:A{$cloudMax})");
        $sheet->setCellValue('B13', "=COUNTA('8 Software'!A5:A{$softMax})");

        // Risk flags (should be 0)
        $sheet->setCellValue('B20', "=COUNTIF('3 Computers'!I5:I{$compMax},\"No\")");
        $sheet->setCellValue('B21', "=COUNTIF('4 Mobiles & Tablets'!K5:K{$mobMax},\"No\")");
        $sheet->setCellValue('B22', "=COUNTIF('5 Servers'!G5:G{$servMax},\"No\")");
        $sheet->setCellValue('B23', "=COUNTIF('7 Cloud Services'!F5:F{$cloudMax},\"No\")");
        $sheet->setCellValue('B24', "=COUNTIF('6 Network'!F5:F{$netMax},\"No\")");
        $sheet->setCellValue('B25', "=COUNTIF('8 Software'!G5:G{$softMax},\"No\")");
    }
}

<?php

namespace App\Services;

use App\Models\Company;
use ZipArchive;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Str;

class CompanyWorkbookExportService
{
    /**
     * Generate and download the XLSX workbook for a given company.
     * Uses PHP's native ZipArchive to preserve 100% template integrity
     * with zero external package dependencies (no PhpSpreadsheet required).
     */
    public function export(Company $company): BinaryFileResponse
    {
        $templatePath = resource_path('templates/Cyber_Essentials_Information_Workbook.xlsx');
        if (!file_exists($templatePath)) {
            $templatePath = base_path('resources/templates/Cyber_Essentials_Information_Workbook.xlsx');
        }

        if (!file_exists($templatePath)) {
            abort(500, 'Cyber Essentials Excel workbook template file not found.');
        }

        // Ensure relations are loaded
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

        // Create temporary copy of template
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $tempFile = tempnam($tempDir, 'ce_xlsx_');
        if (!copy($templatePath, $tempFile)) {
            abort(500, 'Could not create temporary workbook instance.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempFile) !== true) {
            @unlink($tempFile);
            abort(500, 'Could not open workbook archive for modification.');
        }

        // 1. Tab 1: Company Details (sheet3.xml)
        $this->updateCompanyDetailsSheet($zip, $company);

        // 2. Tab 9: IT Contact (sheet11.xml)
        $this->updateItContactSheet($zip, $company);

        // 3. Tab 2: Staff (sheet4.xml)
        $staffCount = $this->updateStaffSheet($zip, $company);

        // 4. Tab 3: Computers (sheet5.xml)
        $compCount = $this->updateComputersSheet($zip, $company);

        // 5. Tab 4: Mobiles & Tablets (sheet6.xml)
        $mobCount = $this->updateMobilesSheet($zip, $company);

        // 6. Tab 5: Servers (sheet7.xml)
        $servCount = $this->updateServersSheet($zip, $company);

        // 7. Tab 6: Network (sheet8.xml)
        $netCount = $this->updateNetworkSheet($zip, $company);

        // 8. Tab 7: Cloud Services (sheet9.xml)
        $cloudCount = $this->updateCloudSheet($zip, $company);

        // 9. Tab 8: Software (sheet10.xml)
        $softCount = $this->updateSoftwareSheet($zip, $company);

        // 10. Summary & Completeness Check (sheet2.xml)
        $this->updateSummarySheet(
            $zip,
            $staffCount,
            $compCount,
            $mobCount,
            $servCount,
            $netCount,
            $cloudCount,
            $softCount
        );

        $zip->close();

        $nameCandidate = $company->registered_name ?: ($company->user->name ?? 'Company');
        $safeCompanyName = Str::slug($nameCandidate, '_') ?: 'Company';
        $filename = "Cyber_Essentials_{$safeCompanyName}_Workbook.xlsx";

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ])->deleteFileAfterSend(true);
    }

    /**
     * XML string escape helper
     */
    protected function xmlEscape($str): string
    {
        return htmlspecialchars((string)$str, ENT_XML1, 'UTF-8');
    }

    /**
     * Build inline string cell XML
     */
    protected function buildInlineCell(string $col, int $row, $val, string $style = '14'): string
    {
        $ref = "{$col}{$row}";
        if ($val === null || $val === '') {
            return "<c r=\"{$ref}\" s=\"{$style}\"/>";
        }
        $escaped = $this->xmlEscape($val);
        return "<c r=\"{$ref}\" s=\"{$style}\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
    }

    /**
     * Build numeric cell XML
     */
    protected function buildNumberCell(string $col, int $row, $num, string $style = '25'): string
    {
        $ref = "{$col}{$row}";
        if ($num === null || $num === '') {
            return "<c r=\"{$ref}\" s=\"{$style}\"/>";
        }
        $val = (float)$num;
        return "<c r=\"{$ref}\" s=\"{$style}\"><v>{$val}</v></c>";
    }

    /**
     * Tab 1: Company Details (sheet3.xml)
     */
    protected function updateCompanyDetailsSheet(ZipArchive $zip, Company $company): void
    {
        $xml = $zip->getFromName('xl/worksheets/sheet3.xml');
        if (!$xml) return;

        $values = [
            5 => $company->registered_name ?: ($company->user->name ?? ''),
            6 => $company->trading_name ?? '',
            7 => $company->company_number ?? '',
            8 => $company->legal_structure ?? '',
            9 => $company->address_line1 ?? '',
            10 => $company->address_line2 ?? '',
            11 => $company->city ?? '',
            12 => $company->postcode ?? '',
            13 => $company->other_address1 ?? '',
            14 => $company->other_address2 ?? '',
            15 => $company->other_address3 ?? '',
            16 => $company->website ?? '',
            17 => $company->phone ?? '',
            18 => $company->email ?: ($company->user->email ?? ''),
            19 => (int)($company->headcount ?? 0),
            20 => $company->sic_code ?? '',
            21 => $company->certification_level ?? 'Cyber Essentials',
            22 => $company->certification_scope ?? 'Whole company',
            23 => $company->date_completed ? date('d/m/Y', strtotime($company->date_completed)) : date('d/m/Y'),
            24 => $company->completed_by ?: ($company->user->name ?? 'Administrator'),
        ];

        foreach ($values as $row => $val) {
            $pattern = '/<c r="B' . $row . '"[^>]*>(?:.*?<\/c>)?|<c r="B' . $row . '"[^>]*\/>/s';
            if ($row === 19) {
                $replacement = $this->buildNumberCell('B', $row, $val, '14');
            } else {
                $replacement = $this->buildInlineCell('B', $row, $val, '14');
            }
            $xml = preg_replace($pattern, $replacement, $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet3.xml', $xml);
    }

    /**
     * Tab 9: IT Contact (sheet11.xml)
     */
    protected function updateItContactSheet(ZipArchive $zip, Company $company): void
    {
        $xml = $zip->getFromName('xl/worksheets/sheet11.xml');
        if (!$xml) return;

        $it = $company->itContact;
        $values = [
            5 => $it->name ?? '',
            6 => $it->job_role ?? '',
            7 => $it->email ?? '',
            8 => $it->phone ?? '',
            9 => $it->provider_type ?? 'Internal',
            10 => $it->external_provider_name ?? '',
            11 => $it->approver_admin_access ?? '',
            12 => $it->person_applying_patches ?? '',
        ];

        foreach ($values as $row => $val) {
            $pattern = '/<c r="B' . $row . '"[^>]*>(?:.*?<\/c>)?|<c r="B' . $row . '"[^>]*\/>/s';
            $replacement = $this->buildInlineCell('B', $row, $val, '14');
            $xml = preg_replace($pattern, $replacement, $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet11.xml', $xml);
    }

    /**
     * Tab 2: Staff (sheet4.xml)
     */
    protected function updateStaffSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet4.xml');
        if (!$xml) return 0;

        $items = $company->staffMembers;
        $count = $items->count();
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        if ($count > 0) {
            foreach ($items as $idx => $st) {
                $r = 5 + $idx;
                $data = [
                    $st->full_name ?? '',
                    $st->job_role ?? '',
                    $st->type ?? 'Employee',
                    $st->works_from_home ?? 'No',
                    $st->uses_personal_device ?? 'No',
                    $st->has_admin_rights ?? 'No',
                    $st->notes ?? '',
                ];
                $cells = '';
                foreach ($cols as $cIdx => $c) {
                    $cells .= $this->buildInlineCell($c, $r, $data[$cIdx] ?? '', '14');
                }
                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row
            $cells = '';
            foreach ($cols as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet4.xml', $xml);
        return $count;
    }

    /**
     * Tab 3: Computers (sheet5.xml)
     */
    protected function updateComputersSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet5.xml');
        if (!$xml) return 0;

        $items = $company->computers;
        $count = $items->count();

        if ($count > 0) {
            foreach ($items as $idx => $comp) {
                $r = 5 + $idx;
                $cells = $this->buildInlineCell('A', $r, $comp->device_type ?? 'Laptop', '14')
                    . $this->buildInlineCell('B', $r, $comp->make ?? '', '14')
                    . $this->buildInlineCell('C', $r, $comp->model ?? '', '14')
                    . $this->buildNumberCell('D', $r, (int)($comp->quantity ?: 1), '25')
                    . $this->buildInlineCell('E', $r, $comp->operating_system ?? '', '14')
                    . $this->buildInlineCell('F', $r, $comp->os_version ?? '', '14')
                    . $this->buildInlineCell('G', $r, $comp->location ?? '', '14')
                    . $this->buildInlineCell('H', $r, $comp->assigned_to ?? '', '14')
                    . $this->buildInlineCell('I', $r, $comp->os_supported ?? 'Yes', '14')
                    . $this->buildInlineCell('J', $r, $comp->auto_updates_on ?? 'Yes', '14')
                    . $this->buildInlineCell('K', $r, $comp->security_software_on ?? 'Yes', '14');

                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row
            $cells = '';
            foreach (['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I', 'J', 'K'] as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $cells .= '<c r="D5" s="25"/>';
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet5.xml', $xml);
        return $count;
    }

    /**
     * Tab 4: Mobiles & Tablets (sheet6.xml)
     */
    protected function updateMobilesSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet6.xml');
        if (!$xml) return 0;

        $items = $company->mobileDevices;
        $count = $items->count();

        // Always clear extra placeholder text in row 6 of sheet6 if present
        $cleanRow6 = '';
        foreach (['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I', 'J', 'K'] as $c) {
            $cleanRow6 .= "<c r=\"{$c}6\" s=\"14\"/>";
        }
        $cleanRow6 .= '<c r="D6" s="25"/>';
        $xml = preg_replace('/<row r="6"[^>]*>.*?<\/row>/s', "<row r=\"6\">{$cleanRow6}</row>", $xml, 1);

        if ($count > 0) {
            foreach ($items as $idx => $mob) {
                $r = 5 + $idx;
                $cells = $this->buildInlineCell('A', $r, $mob->device_type ?? 'Phone', '14')
                    . $this->buildInlineCell('B', $r, $mob->make ?? '', '14')
                    . $this->buildInlineCell('C', $r, $mob->model ?? '', '14')
                    . $this->buildNumberCell('D', $r, (int)($mob->quantity ?: 1), '25')
                    . $this->buildInlineCell('E', $r, $mob->operating_system ?? '', '14')
                    . $this->buildInlineCell('F', $r, $mob->os_version ?? '', '14')
                    . $this->buildInlineCell('G', $r, $mob->ownership ?? 'Company', '14')
                    . $this->buildInlineCell('H', $r, $mob->used_for_work_email ?? 'Yes', '14')
                    . $this->buildInlineCell('I', $r, $mob->assigned_to ?? '', '14')
                    . $this->buildInlineCell('J', $r, $mob->screen_lock ?? 'Yes', '14')
                    . $this->buildInlineCell('K', $r, $mob->os_supported ?? 'Yes', '14');

                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row 5
            $cells = '';
            foreach (['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I', 'J', 'K'] as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $cells .= '<c r="D5" s="25"/>';
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet6.xml', $xml);
        return $count;
    }

    /**
     * Tab 5: Servers (sheet7.xml)
     */
    protected function updateServersSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet7.xml');
        if (!$xml) return 0;

        $items = $company->servers;
        $count = $items->count();

        if ($count > 0) {
            foreach ($items as $idx => $serv) {
                $r = 5 + $idx;
                $cells = $this->buildInlineCell('A', $r, $serv->server_name ?? '', '14')
                    . $this->buildNumberCell('B', $r, (int)($serv->quantity ?: 1), '25')
                    . $this->buildInlineCell('C', $r, $serv->operating_system ?? '', '14')
                    . $this->buildInlineCell('D', $r, $serv->os_version ?? '', '14')
                    . $this->buildInlineCell('E', $r, $serv->type ?? 'Physical', '14')
                    . $this->buildInlineCell('F', $r, $serv->location ?? '', '14')
                    . $this->buildInlineCell('G', $r, $serv->os_supported ?? 'Yes', '14')
                    . $this->buildInlineCell('H', $r, $serv->notes ?? '', '14');

                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row
            $cells = '';
            foreach (['A', 'C', 'D', 'E', 'F', 'G', 'H'] as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $cells .= '<c r="B5" s="25"/>';
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet7.xml', $xml);
        return $count;
    }

    /**
     * Tab 6: Network (sheet8.xml)
     */
    protected function updateNetworkSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet8.xml');
        if (!$xml) return 0;

        $items = $company->networkDevices;
        $count = $items->count();

        if ($count > 0) {
            foreach ($items as $idx => $net) {
                $r = 5 + $idx;
                $cells = $this->buildInlineCell('A', $r, $net->location ?? 'Head office', '14')
                    . $this->buildInlineCell('B', $r, $net->device_type ?? 'Router/Firewall', '14')
                    . $this->buildInlineCell('C', $r, $net->make ?? '', '14')
                    . $this->buildInlineCell('D', $r, $net->model ?? '', '14')
                    . $this->buildInlineCell('E', $r, $net->firmware_version ?? '', '14')
                    . $this->buildInlineCell('F', $r, $net->default_password_changed ?? 'Yes', '14')
                    . $this->buildInlineCell('G', $r, $net->firewall_enabled ?? 'Yes', '14')
                    . $this->buildInlineCell('H', $r, $net->internet_admin_blocked ?? 'Yes', '14')
                    . $this->buildInlineCell('I', $r, $net->notes ?? '', '14');

                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row
            $cells = '';
            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet8.xml', $xml);
        return $count;
    }

    /**
     * Tab 7: Cloud Services (sheet9.xml)
     */
    protected function updateCloudSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet9.xml');
        if (!$xml) return 0;

        $items = $company->cloudServices;
        $count = $items->count();

        if ($count > 0) {
            foreach ($items as $idx => $cs) {
                $r = 5 + $idx;
                $cells = $this->buildInlineCell('A', $r, $cs->service_name ?? '', '14')
                    . $this->buildInlineCell('B', $r, $cs->purpose ?? '', '14')
                    . $this->buildInlineCell('C', $r, $cs->provider ?? '', '14')
                    . $this->buildNumberCell('D', $r, (int)($cs->users_count ?: 1), '25')
                    . $this->buildInlineCell('E', $r, $cs->admin_owner ?? '', '14')
                    . $this->buildInlineCell('F', $r, $cs->mfa_enabled ?? 'Yes', '14')
                    . $this->buildInlineCell('G', $r, $cs->holds_sensitive_data ?? 'No', '14')
                    . $this->buildInlineCell('H', $r, $cs->notes ?? '', '14');

                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row
            $cells = '';
            foreach (['A', 'B', 'C', 'E', 'F', 'G', 'H'] as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $cells .= '<c r="D5" s="25"/>';
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet9.xml', $xml);
        return $count;
    }

    /**
     * Tab 8: Software (sheet10.xml)
     */
    protected function updateSoftwareSheet(ZipArchive $zip, Company $company): int
    {
        $xml = $zip->getFromName('xl/worksheets/sheet10.xml');
        if (!$xml) return 0;

        $items = $company->softwareItems;
        $count = $items->count();

        if ($count > 0) {
            foreach ($items as $idx => $sw) {
                $r = 5 + $idx;
                $installedOn = $sw->installed_on ?? '';
                if (!empty($sw->assigned_to)) {
                    $installedOn .= ($installedOn ? ' - ' : '') . 'Assigned to: ' . $sw->assigned_to;
                }

                $cells = $this->buildInlineCell('A', $r, $sw->category ?? '', '14')
                    . $this->buildInlineCell('B', $r, $sw->software_name ?? '', '14')
                    . $this->buildInlineCell('C', $r, $sw->version ?? '', '14')
                    . $this->buildInlineCell('D', $r, $sw->vendor ?? '', '14')
                    . $this->buildInlineCell('E', $r, $installedOn, '14')
                    . $this->buildInlineCell('F', $r, $sw->auto_update_on ?? 'Yes', '14')
                    . $this->buildInlineCell('G', $r, $sw->still_supported ?? 'Yes', '14')
                    . $this->buildInlineCell('H', $r, $sw->notes ?? '', '14');

                $newRow = "<row r=\"{$r}\">{$cells}</row>";
                $xml = preg_replace('/<row r="' . $r . '"[^>]*>.*?<\/row>/s', $newRow, $xml, 1);
            }
        } else {
            // Clear example row
            $cells = '';
            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $c) {
                $cells .= "<c r=\"{$c}5\" s=\"14\"/>";
            }
            $xml = preg_replace('/<row r="5"[^>]*>.*?<\/row>/s', "<row r=\"5\">{$cells}</row>", $xml, 1);
        }

        $zip->addFromString('xl/worksheets/sheet10.xml', $xml);
        return $count;
    }

    /**
     * Summary & Completeness Check (sheet2.xml)
     */
    protected function updateSummarySheet(
        ZipArchive $zip,
        int $staffCount,
        int $compCount,
        int $mobCount,
        int $servCount,
        int $netCount,
        int $cloudCount,
        int $softCount
    ): void {
        $xml = $zip->getFromName('xl/worksheets/sheet2.xml');
        if (!$xml) return;

        $staffMax = max(65, 5 + $staffCount);
        $compMax = max(45, 5 + $compCount);
        $mobMax = max(45, 5 + $mobCount);
        $servMax = max(25, 5 + $servCount);
        $netMax = max(20, 5 + $netCount);
        $cloudMax = max(45, 5 + $cloudCount);
        $softMax = max(45, 5 + $softCount);

        // Update range start from row 5
        $xml = str_replace(
            [
                'COUNTA(\'2 Staff\'!A6:A65)',
                'COUNTIF(\'2 Staff\'!D6:D65',
                'COUNTIF(\'2 Staff\'!E6:E65',
                'SUM(\'3 Computers\'!D6:D45)',
                'SUM(\'4 Mobiles &amp; Tablets\'!D6:D45)',
                'SUM(\'5 Servers\'!B6:B25)',
                'COUNTA(\'6 Network\'!A6:A20)',
                'COUNTA(\'7 Cloud Services\'!A6:A45)',
                'COUNTA(\'8 Software\'!A6:A45)',
                'COUNTIF(\'3 Computers\'!I6:I45',
                'COUNTIF(\'4 Mobiles &amp; Tablets\'!K6:K45',
                'COUNTIF(\'5 Servers\'!G6:G25',
                'COUNTIF(\'7 Cloud Services\'!F6:F45',
                'COUNTIF(\'6 Network\'!F6:F20',
                'COUNTIF(\'8 Software\'!G6:G45',
            ],
            [
                "COUNTA('2 Staff'!A5:A{$staffMax})",
                "COUNTIF('2 Staff'!D5:D{$staffMax}",
                "COUNTIF('2 Staff'!E5:E{$staffMax}",
                "SUM('3 Computers'!D5:D{$compMax})",
                "SUM('4 Mobiles &amp; Tablets'!D5:D{$mobMax})",
                "SUM('5 Servers'!B5:B{$servMax})",
                "COUNTA('6 Network'!A5:A{$netMax})",
                "COUNTA('7 Cloud Services'!A5:A{$cloudMax})",
                "COUNTA('8 Software'!A5:A{$softMax})",
                "COUNTIF('3 Computers'!I5:I{$compMax}",
                "COUNTIF('4 Mobiles &amp; Tablets'!K5:K{$mobMax}",
                "COUNTIF('5 Servers'!G5:G{$servMax}",
                "COUNTIF('7 Cloud Services'!F5:F{$cloudMax}",
                "COUNTIF('6 Network'!F5:F{$netMax}",
                "COUNTIF('8 Software'!G5:G{$softMax}",
            ],
            $xml
        );

        $zip->addFromString('xl/worksheets/sheet2.xml', $xml);
    }
}

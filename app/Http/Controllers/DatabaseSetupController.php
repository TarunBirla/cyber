<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class DatabaseSetupController extends Controller
{
    /**
     * Hit this route to safely create database tables & seed initial data on live server.
     * Route: /setup-db or /setup-db?force=1
     */
    public function setup(Request $request)
    {
        $secretKey = env('DB_SETUP_KEY', 'CyberEssentials2026Setup');
        $providedKey = $request->query('key');

        if ($request->has('key') && $providedKey !== $secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid setup key.'
            ], 403);
        }

        $log = [];
        $startTime = microtime(true);

        try {
            // 1. Test Database connection
            $pdo = DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $databaseName = DB::connection()->getDatabaseName();
            $log[] = "✅ Database Connected: [$driver] - Database: '$databaseName'";

            // 2. Run migrations
            $log[] = "🚀 Running migrations (force)...";
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            $migrationOutput = trim(Artisan::output());
            $log[] = nl2br($migrationOutput ?: 'Tables already up to date.');

            // 3. Run seeders (Default Admin: Mohammed Nasar - Nexteck Limited)
            $log[] = "🌱 Seeding Lead Auditor (Mohammed Nasar) & Nexteck governance structures...";
            Artisan::call('db:seed', [
                '--force' => true,
            ]);
            $seederOutput = trim(Artisan::output());
            $log[] = nl2br($seederOutput ?: 'Database seeded successfully.');

            // 4. Verify tables created
            $tables = [
                'users',
                'companies',
                'staff_members',
                'computers',
                'mobile_devices',
                'servers',
                'network_devices',
                'cloud_services',
                'software_items',
                'it_contacts'
            ];

            $createdTables = [];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $createdTables[] = $table;
                }
            }

            $elapsed = round(microtime(true) - $startTime, 2);

            return view('setup_success', [
                'success' => true,
                'logs' => $log,
                'tables' => $createdTables,
                'elapsed' => $elapsed,
                'admin_name' => 'Mohammed Nasar',
                'admin_role' => 'CEO & Enterprise Architect (Nexteck Limited)',
                'admin_email' => 'info@nexteck.co.uk',
                'admin_pass' => 'Admin@12345',
                'demo_company_email' => 'contact@acmeltd.co.uk',
                'demo_company_pass' => 'Password@123',
            ]);

        } catch (Exception $e) {
            return view('setup_success', [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'logs' => $log
            ]);
        }
    }
}

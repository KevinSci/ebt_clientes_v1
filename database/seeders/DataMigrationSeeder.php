<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class DataMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = DB::table('users')
            ->where('role', 'client')
            ->whereNull('deleted_at')
            ->get();

        foreach ($clients as $client) {
            // Check if user is already linked in pivot table
            $exists = DB::table('company_user')->where('user_id', $client->id)->exists();
            if ($exists) {
                continue;
            }

            $companyName = trim($client->company_name ?? '');
            if (empty($companyName)) {
                $companyName = $client->name;
            }

            // Create company record
            $companyId = DB::table('companies')->insertGetId([
                'name'       => $companyName,
                'phone'      => $client->phone,
                'tax_regime' => 'moral',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link user and company in pivot table
            DB::table('company_user')->insert([
                'user_id'    => $client->id,
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

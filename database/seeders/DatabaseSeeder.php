<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n";
        echo "🌱 ========================================\n";
        echo "   STARTING DATABASE SEEDING\n";
        echo "========================================\n\n";

        // STEP 1: Seed admin users and roles
        echo "📝 Step 1: Seeding admin users and roles...\n";
        $this->call(AdminSeeder::class);

        // STEP 2: Seed mangrove locations
        echo "\n🌳 Step 2: Seeding mangrove locations...\n";
        $this->call(MangroveLocationSeeder::class);

        echo "\n";
        echo "✅ ========================================\n";
        echo "   DATABASE SEEDING COMPLETED!\n";
        echo "========================================\n";
        echo "\n";
        echo "👤 LOGIN CREDENTIALS:\n";
        echo "   Username: superadmin\n";
        echo "   Password: password123\n";
        echo "\n";
        echo "   OR\n";
        echo "\n";
        echo "   Username: admin\n";
        echo "   Password: password123\n";
        echo "\n";
    }
}

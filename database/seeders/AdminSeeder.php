<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Groups (Roles)
        DB::table('ms_group')->insert([
            [
                'MsGroupId' => 1,
                'name' => 'Super Administrator',
                'alias' => 'superadmin',
                'description' => 'Full system access',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'MsGroupId' => 2,
                'name' => 'Administrator',
                'alias' => 'admin',
                'description' => 'Admin access',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'MsGroupId' => 3,
                'name' => 'User',
                'alias' => 'user',
                'description' => 'Regular user',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert Menus
        $menus = [
            // Dashboard
            ['MsMenuId' => 1, 'parent_id' => 0, 'title' => 'Dashboard', 'url' => 'admin/dashboard', 'module' => 'admin.dashboard', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-home-circle', 'ordering' => 1],

            // Master Data
            ['MsMenuId' => 2, 'parent_id' => 0, 'title' => 'Master Data', 'url' => '#', 'module' => 'admin.master', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-data', 'ordering' => 2],
            ['MsMenuId' => 3, 'parent_id' => 2, 'title' => 'Lokasi Mangrove', 'url' => 'admin/lokasi', 'module' => 'admin.master.lokasi', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-map', 'ordering' => 1],
            ['MsMenuId' => 4, 'parent_id' => 2, 'title' => 'Kategori', 'url' => 'admin/kategori', 'module' => 'admin.master.kategori', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-category', 'ordering' => 2],

            // Monitoring
            ['MsMenuId' => 5, 'parent_id' => 0, 'title' => 'Monitoring', 'url' => '#', 'module' => 'admin.monitoring', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-line-chart', 'ordering' => 3],
            ['MsMenuId' => 6, 'parent_id' => 5, 'title' => 'Data Pemantauan', 'url' => 'admin/monitoring/data', 'module' => 'admin.monitoring.data', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-data', 'ordering' => 1],
            ['MsMenuId' => 7, 'parent_id' => 5, 'title' => 'Laporan Kerusakan', 'url' => 'admin/monitoring/laporan', 'module' => 'admin.monitoring.laporan', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-error', 'ordering' => 2],

            // Content Management
            ['MsMenuId' => 8, 'parent_id' => 0, 'title' => 'Content', 'url' => '#', 'module' => 'admin.content', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-file', 'ordering' => 4],
            ['MsMenuId' => 9, 'parent_id' => 8, 'title' => 'Artikel', 'url' => 'admin/artikel', 'module' => 'admin.content.artikel', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-news', 'ordering' => 1],
            ['MsMenuId' => 10, 'parent_id' => 8, 'title' => 'Galeri', 'url' => 'admin/galeri', 'module' => 'admin.content.galeri', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-image', 'ordering' => 2],

            // User Management
            ['MsMenuId' => 11, 'parent_id' => 0, 'title' => 'User Management', 'url' => '#', 'module' => 'admin.user', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-user', 'ordering' => 5],
            ['MsMenuId' => 12, 'parent_id' => 11, 'title' => 'Users', 'url' => 'admin/users', 'module' => 'admin.user.users', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-group', 'ordering' => 1],
            ['MsMenuId' => 13, 'parent_id' => 11, 'title' => 'Roles', 'url' => 'admin/roles', 'module' => 'admin.user.roles', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-shield', 'ordering' => 2],
            ['MsMenuId' => 14, 'parent_id' => 11, 'title' => 'Access Control', 'url' => 'admin/access', 'module' => 'admin.user.access', 'menu_type' => 'sidebar', 'menu_icons' => 'bx bx-lock', 'ordering' => 3],
        ];

        foreach ($menus as $menu) {
            DB::table('ms_menu')->insert(array_merge($menu, [
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Insert Super Admin User
        DB::table('users')->insert([
            'username' => 'superadmin',
            'name' => 'Super Administrator',
            'email' => 'superadmin@sikomang.com',
            'password' => Hash::make('password123'),
            'ms_group_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Admin User
        DB::table('users')->insert([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@sikomang.com',
            'password' => Hash::make('password123'),
            'ms_group_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

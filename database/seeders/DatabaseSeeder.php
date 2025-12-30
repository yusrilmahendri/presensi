<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shift;
use App\Models\AttendanceLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil shift pertama atau buat default
        $shift = Shift::first();
        
        if (!$shift) {
            $shift = Shift::create([
                'name' => 'Shift Pagi',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'description' => 'Shift pagi default'
            ]);
        }

        $password = Hash::make('Bismillah@1');

        // Data Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@presensi.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => $password,
                'role' => 'admin',
            ]
        );

        // Data Karyawan dengan NIK/NIP
        $karyawan = [
            [
                'name' => 'Ahmad Fauzi',
                'username' => null,
                'nik' => '3201011990010001',
                'nip' => '199001012020011001',
                'email' => 'ahmad.fauzi@presensi.com',
                'role' => 'karyawan',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => null,
                'nik' => '3201012000020002',
                'nip' => '200002022021012001',
                'email' => 'siti.nurhaliza@presensi.com',
                'role' => 'karyawan',
            ],
            [
                'name' => 'Budi Santoso',
                'username' => null,
                'nik' => '3201011995030003',
                'nip' => '199503032019013001',
                'email' => 'budi.santoso@presensi.com',
                'role' => 'karyawan',
            ],
            [
                'name' => 'Dewi Lestari',
                'username' => null,
                'nik' => '3201012001040004',
                'nip' => '200104042022014001',
                'email' => 'dewi.lestari@presensi.com',
                'role' => 'karyawan',
            ],
            [
                'name' => 'Eko Prasetyo',
                'username' => null,
                'nik' => '3201011998050005',
                'nip' => '199805052020015001',
                'email' => 'eko.prasetyo@presensi.com',
                'role' => 'karyawan',
            ],
        ];

        foreach ($karyawan as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => $password,
                    'shift_id' => $shift->id,
                ])
            );
        }

        $this->command->info('✅ Dummy users created successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('Password untuk semua user: Bismillah@1');
        $this->command->info('');
        $this->command->info('👤 ADMIN:');
        $this->command->info('   Username: admin');
        $this->command->info('   Email: admin@presensi.com');
        $this->command->info('');
        $this->command->info('👥 KARYAWAN (gunakan NIK atau NIP):');
        $this->command->info('   1. Ahmad Fauzi');
        $this->command->info('      NIK: 3201011990010001');
        $this->command->info('      NIP: 199001012020011001');
        $this->command->info('');
        $this->command->info('   2. Siti Nurhaliza');
        $this->command->info('      NIK: 3201012000020002');
        $this->command->info('      NIP: 200002022021012001');
        $this->command->info('');
        $this->command->info('   3. Budi Santoso');
        $this->command->info('      NIK: 3201011995030003');
        $this->command->info('      NIP: 199503032019013001');
        $this->command->info('');
        $this->command->info('   4. Dewi Lestari');
        $this->command->info('      NIK: 3201012001040004');
        $this->command->info('      NIP: 200104042022014001');
        $this->command->info('');
        $this->command->info('   5. Eko Prasetyo');
        $this->command->info('      NIK: 3201011998050005');
        $this->command->info('      NIP: 199805052020015001');
    }
}


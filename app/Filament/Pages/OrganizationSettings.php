<?php

namespace App\Filament\Pages;

use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class OrganizationSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.organization-settings';
    
    public static function getNavigationLabel(): string
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return 'Pengaturan Organisasi';
        }
        
        $org = auth()->user()?->organization;
        if ($org) {
            $modes = $org->getEnabledModes();
            // Jika hanya working_hours yang aktif
            if (count($modes) === 1 && in_array('working_hours', $modes)) {
                return 'Pengaturan Jam Kerja';
            }
        }
        
        return 'Pengaturan Organisasi';
    }
    
    public function getTitle(): string
    {
        if (auth()->user()->isSuperAdmin()) {
            return 'Pengaturan Organisasi';
        }
        
        $org = auth()->user()->organization;
        if (!$org) {
            return 'Informasi Organisasi';
        }
        
        $modes = $org->getEnabledModes();
        
        if (count($modes) === 1 && in_array('working_hours', $modes)) {
            return 'Pengaturan Jam Kerja';
        }
        return 'Informasi Organisasi';
    }
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Hanya admin bisnis yang bisa akses (BUKAN Super Admin dan BUKAN karyawan)
        return auth()->check() && auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin();
    }

    public function mount(): void
    {
        $organization = auth()->user()->organization;
        
        if ($organization) {
            $this->form->fill([
                'name' => $organization->name,
                'type' => $organization->type,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'enabled_attendance_modes' => $organization->getEnabledModes(),
                'min_working_hours' => $organization->min_working_hours ?? 8,
                'grace_period_hours' => $organization->grace_period_hours ?? 2,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Organisasi')
                    ->description('Data dasar organisasi Anda')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Bisnis')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('type')
                            ->label('Jenis Bisnis')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Bisnis')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Pengaturan Absensi')
                    ->description(function () {
                        if (auth()->user()->isSuperAdmin()) {
                            return '🔧 Aktifkan fitur absensi untuk organisasi ini. Anda bisa mengaktifkan shift, jam kerja, atau keduanya.';
                        }
                        
                        $org = auth()->user()->organization;
                        if (!$org) {
                            return 'Pengaturan absensi organisasi';
                        }
                        
                        $modes = $org->getEnabledModes();
                        
                        if (in_array('working_hours', $modes)) {
                            return '⏰ Mode Jam Kerja aktif - Atur konfigurasi jam kerja di bawah';
                        }
                        return '🕒 Mode Shift aktif - Kelola jadwal shift di menu Shift';
                    })
                    ->schema([
                        // Hanya Super Admin yang bisa aktifkan/nonaktifkan mode
                        Forms\Components\CheckboxList::make('enabled_attendance_modes')
                            ->label('Fitur Absensi yang Diaktifkan')
                            ->options([
                                'shift' => '🕒 Mode Shift - Absensi berdasarkan jadwal shift',
                                'working_hours' => '⏰ Mode Jam Kerja - Waktu fleksibel dengan minimum jam kerja',
                            ])
                            ->descriptions([
                                'shift' => 'Karyawan check-in/out sesuai jadwal shift. Menu Shift akan muncul untuk Admin.',
                                'working_hours' => 'Karyawan bebas check-in, checkout setelah jam minimum. Admin bisa atur konfigurasi jam kerja.',
                            ])
                            ->required()
                            ->minItems(1)
                            ->helperText('💡 Anda bisa mengaktifkan kedua mode sekaligus')
                            ->visible(fn () => auth()->user()->isSuperAdmin()),
                        
                        // Admin biasa hanya bisa lihat mode yang aktif
                        Forms\Components\Placeholder::make('current_modes_info')
                            ->label('Mode Absensi yang Aktif')
                            ->content(function () {
                                $org = auth()->user()->organization;
                                if (!$org) {
                                    return "**Tidak ada organisasi**\n\nSilakan hubungi Super Admin.";
                                }
                                $modes = $org->getEnabledModes();
                                
                                $info = "**Mode yang Diaktifkan oleh Super Admin:**\n\n";
                                
                                if (in_array('shift', $modes)) {
                                    $info .= "🕒 **Mode Shift**\n";
                                    $info .= "✅ Absensi berdasarkan jadwal shift\n";
                                    $info .= "✅ Kelola shift di menu Shift\n\n";
                                }
                                
                                if (in_array('working_hours', $modes)) {
                                    $info .= "⏰ **Mode Jam Kerja**\n";
                                    $info .= "✅ Karyawan bebas check-in\n";
                                    $info .= "✅ Atur konfigurasi jam kerja di bawah\n";
                                }
                                
                                return $info;
                            })
                            ->columnSpanFull()
                            ->visible(fn () => !auth()->user()->isSuperAdmin()),
                        
                        // Field konfigurasi jam kerja - HANYA Admin yang bisa edit (Super Admin TIDAK bisa)
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_working_hours')
                                    ->label('Jam Kerja Minimum')
                                    ->numeric()
                                    ->default(8)
                                    ->minValue(1)
                                    ->maxValue(12)
                                    ->suffix('jam/hari')
                                    ->helperText('Karyawan harus bekerja minimal jam ini sebelum bisa checkout')
                                    ->required()
                                    ->live()
                                    ->disabled(fn () => auth()->user()->isSuperAdmin()),
                                
                                Forms\Components\TextInput::make('grace_period_hours')
                                    ->label('Grace Period (Toleransi)')
                                    ->numeric()
                                    ->default(2)
                                    ->minValue(0)
                                    ->maxValue(4)
                                    ->suffix('jam')
                                    ->helperText('Jam tambahan sebelum dihitung sebagai lembur')
                                    ->required()
                                    ->live()
                                    ->disabled(fn () => auth()->user()->isSuperAdmin()),
                            ])
                            ->visible(function ($get) {
                                // Super Admin: cek dari checkbox
                                if (auth()->user()->isSuperAdmin()) {
                                    $modes = $get('enabled_attendance_modes') ?? ['shift'];
                                    return in_array('working_hours', $modes);
                                }
                                // Admin biasa: cek dari database
                                $org = auth()->user()->organization;
                                return $org && $org->isWorkingHoursBased();
                            }),
                        
                        Forms\Components\Placeholder::make('working_hours_info')
                            ->label('ℹ️ Informasi')
                            ->content(function () {
                                if (auth()->user()->isSuperAdmin()) {
                                    return "**Super Admin hanya aktifkan fitur.**\n\nAdmin yang akan mengatur nilai Jam Kerja Minimum dan Grace Period.";
                                }
                                return "**Anda bisa mengatur konfigurasi jam kerja di atas.**\n\nLihat preview di bawah untuk memahami efeknya.";
                            })
                            ->visible(function ($get) {
                                // Super Admin: cek dari checkbox
                                if (auth()->user()->isSuperAdmin()) {
                                    $modes = $get('enabled_attendance_modes') ?? ['shift'];
                                    return in_array('working_hours', $modes);
                                }
                                // Admin biasa: cek dari database
                                $org = auth()->user()->organization;
                                return $org && $org->isWorkingHoursBased();
                            }),
                        
                        Forms\Components\Placeholder::make('working_hours_preview')
                            ->label('Preview Konfigurasi')
                            ->content(function ($get) {
                                $minHours = (int) ($get('min_working_hours') ?? 8);
                                $gracePeriod = (int) ($get('grace_period_hours') ?? 2);
                                $maxHours = $minHours + $gracePeriod;
                                
                                return "**Konfigurasi Aktif:**\n" .
                                       "• Jam kerja minimum: **{$minHours} jam**\n" .
                                       "• Grace period: **{$gracePeriod} jam**\n" .
                                       "• Maksimal sebelum lembur: **{$maxHours} jam**\n\n" .
                                       "**Contoh Kasus:**\n" .
                                       "Karyawan check-in jam 08:00\n\n" .
                                       "• Checkout jam 14:00 (6 jam) → ❌ Ditolak! Kurang " . ($minHours - 6) . " jam\n" .
                                       "• Checkout jam 16:30 ({$minHours}.5 jam) → ✅ Boleh checkout\n" .
                                       "• Checkout jam 18:00 ({$maxHours} jam) → ✅ Boleh checkout (belum lembur)\n" .
                                       "• Checkout jam 19:00 (" . ($maxHours + 1) . " jam) → ✅ Checkout + 1 jam lembur otomatis";
                            })
                            ->visible(function ($get) {
                                // Hanya Admin yang lihat preview (Super Admin tidak perlu)
                                if (auth()->user()->isSuperAdmin()) {
                                    return false;
                                }
                                // Admin biasa: cek dari database
                                $org = auth()->user()->organization;
                                return $org && $org->isWorkingHoursBased();
                            }),
                        
                        Forms\Components\Placeholder::make('shift_mode_info')
                            ->label('Cara Menggunakan Mode Shift')
                            ->content(function () {
                                return "**📋 Langkah Konfigurasi:**\n\n" .
                                       "1️⃣ Buka menu **Pengaturan** → **Shift**\n" .
                                       "2️⃣ Buat jadwal shift (contoh: Pagi 08:00-16:00, Malam 16:00-00:00)\n" .
                                       "3️⃣ Tetapkan shift ke karyawan\n" .
                                       "4️⃣ Karyawan absen sesuai jam shift mereka\n\n" .
                                       "**✨ Fitur Otomatis:**\n" .
                                       "• Status keterlambatan dihitung otomatis\n" .
                                       "• Lembur tercatat jika checkout setelah jam shift\n" .
                                       "• Notifikasi keterlambatan ke karyawan";
                            })
                            ->visible(function ($get) {
                                // Hanya Admin yang lihat info shift (Super Admin tidak perlu)
                                if (auth()->user()->isSuperAdmin()) {
                                    return false;
                                }
                                // Admin biasa: cek dari database
                                $org = auth()->user()->organization;
                                return $org && $org->isShiftBased();
                            }),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $organization = auth()->user()->organization;

        if (!$organization) {
            Notification::make()
                ->title('Kesalahan')
                ->body('Organisasi tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }

        $updateData = [
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ];

        // Super Admin bisa update enabled modes
        if (auth()->user()->isSuperAdmin()) {
            $updateData['enabled_attendance_modes'] = $data['enabled_attendance_modes'] ?? ['shift'];
        }

        // Admin bisa update konfigurasi jam kerja (BUKAN Super Admin)
        if (!auth()->user()->isSuperAdmin() && $organization->isWorkingHoursBased()) {
            $updateData['min_working_hours'] = $data['min_working_hours'] ?? 8;
            $updateData['grace_period_hours'] = $data['grace_period_hours'] ?? 2;
        }

        $organization->update($updateData);

        Notification::make()
            ->title('Berhasil')
            ->body('Pengaturan organisasi berhasil diperbarui.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('💾 Simpan Pengaturan')
                ->submit('save')
                ->color('primary'),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Contact extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static string $view = 'filament.pages.contact';
    
    protected static ?string $navigationLabel = 'Contact';
    
    protected static ?string $navigationGroup = 'Bantuan';
    
    protected static ?int $navigationSort = 50;
    
    protected static ?string $title = 'Hubungi Kami';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function getContactInfo(): array
    {
        return [
            'phone' => '085161597598',
            'whatsapp' => '085161597598',
            'email' => 'yusrilmahendri.yusril@gmail.com',
            'address' => 'Indonesia',
            'business_hours' => [
                'weekdays' => 'Senin - Jumat: 09:00 - 17:00',
                'saturday' => 'Sabtu: 09:00 - 13:00',
                'sunday' => 'Minggu: Tutup',
            ],
            'support_channels' => [
                [
                    'name' => 'WhatsApp',
                    'icon' => 'heroicon-o-chat-bubble-left-right',
                    'value' => '085161597598',
                    'action' => 'https://wa.me/6285161597598',
                    'description' => 'Chat langsung via WhatsApp untuk respon cepat',
                    'available' => '24/7 (Respon dalam 1-2 jam kerja)',
                ],
                [
                    'name' => 'Telepon',
                    'icon' => 'heroicon-o-phone',
                    'value' => '085161597598',
                    'action' => 'tel:+6285161597598',
                    'description' => 'Hubungi kami via telepon',
                    'available' => 'Senin - Jumat, 09:00 - 17:00 WIB',
                ],
                [
                    'name' => 'Email',
                    'icon' => 'heroicon-o-envelope',
                    'value' => 'support@presensi.com',
                    'action' => 'mailto:support@presensi.com',
                    'description' => 'Kirim email untuk pertanyaan detail',
                    'available' => 'Respon dalam 1x24 jam kerja',
                ],
            ],
            'emergency_contact' => [
                'title' => 'Kontak Darurat (24/7)',
                'phone' => '085161597598',
                'note' => 'Untuk masalah kritis yang membutuhkan penanganan segera',
            ],
            'faq_note' => 'Sebelum menghubungi, silakan cek halaman FAQ untuk jawaban cepat atas pertanyaan umum.',
        ];
    }
}

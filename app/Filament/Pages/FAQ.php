<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class FAQ extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static string $view = 'filament.pages.f-a-q';
    
    protected static ?string $navigationLabel = 'FAQ';
    
    protected static ?string $navigationGroup = 'Bantuan';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $title = 'Frequently Asked Questions';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function getFAQData(): array
    {
        return [
            [
                'category' => 'Umum',
                'items' => [
                    [
                        'question' => 'Bagaimana cara login ke sistem?',
                        'answer' => 'Gunakan username/email dan password yang telah diberikan oleh admin. Kunjungi halaman /login untuk masuk ke sistem.',
                    ],
                    [
                        'question' => 'Siapa yang bisa mengakses admin panel?',
                        'answer' => 'Hanya user dengan role admin  yang dapat mengakses admin Dashboard.',
                    ],
                    [
                        'question' => 'Bagaimana cara logout dari sistem?',
                        'answer' => 'Klik profil Anda di pojok kanan atas, kemudian pilih "Logout".',
                    ],
                ],
            ],
            [
                'category' => 'Absensi',
                'items' => [
                    [
                        'question' => 'Bagaimana cara melihat data absensi karyawan?',
                        'answer' => 'Buka menu "Data Absensi" di sidebar. Anda dapat melihat semua data absensi, filter berdasarkan tanggal, tipe, atau karyawan tertentu.',
                    ],
                    [
                        'question' => 'Bagaimana cara export data absensi?',
                        'answer' => 'Di halaman Data Absensi, klik tombol "Export Excel" atau "Export PDF" di bagian atas tabel. Anda bisa mengatur filter sebelum export.',
                    ],
                    [
                        'question' => 'Apa perbedaan Check In dan Check Out?',
                        'answer' => 'Check In adalah absensi masuk kerja, sedangkan Check Out adalah absensi pulang kerja. Karyawan harus melakukan keduanya setiap hari.',
                    ],
                    [
                        'question' => 'Bagaimana cara melihat karyawan yang terlambat?',
                        'answer' => 'Gunakan menu "Laporan Keterlambatan" untuk melihat daftar karyawan yang terlambat check in beserta durasi keterlambatan.',
                    ],
                ],
            ],
            [
                'category' => 'Karyawan & User',
                'items' => [
                    [
                        'question' => 'Bagaimana cara menambah karyawan baru?',
                        'answer' => 'Buka menu "Data User", klik tombol "New User", isi form dengan lengkap (nama, email, username, password, shift, dll), kemudian simpan.',
                    ],
                    [
                        'question' => 'Bagaimana cara mengubah shift karyawan?',
                        'answer' => 'Buka menu "Data User", klik tombol edit pada karyawan yang ingin diubah, pilih shift baru, kemudian simpan.',
                    ],
                    [
                        'question' => 'Bagaimana cara import user secara bulk?',
                        'answer' => 'Di halaman Data User, klik "Import Users", download template Excel, isi data karyawan, kemudian upload file tersebut.',
                    ],
                ],
            ],
            [
                'category' => 'Izin & Cuti',
                'items' => [
                    [
                        'question' => 'Bagaimana cara approve/reject pengajuan izin?',
                        'answer' => 'Buka menu "Data Izin/Cuti", klik pada pengajuan yang ingin direview, ubah status menjadi "Approved" atau "Rejected", tambahkan catatan jika perlu.',
                    ],
                    [
                        'question' => 'Berapa batas cuti karyawan per tahun?',
                        'answer' => 'Secara default, setiap karyawan mendapat jatah 12 hari cuti per tahun. Ini dapat dikonfigurasi sesuai kebijakan perusahaan.',
                    ],
                ],
            ],
            [
                'category' => 'Shift & Jadwal',
                'items' => [
                    [
                        'question' => 'Bagaimana cara membuat shift baru?',
                        'answer' => 'Buka menu "Data Shift", klik "New Shift", isi nama shift, waktu masuk, waktu pulang, toleransi keterlambatan, kemudian simpan.',
                    ],
                    [
                        'question' => 'Apa fungsi toleransi keterlambatan?',
                        'answer' => 'Toleransi keterlambatan adalah waktu (dalam menit) yang masih dianggap tidak terlambat. Misalnya toleransi 15 menit, maka check in sampai 15 menit setelah jam masuk masih dianggap tepat waktu.',
                    ],
                ],
            ],
            [
                'category' => 'Lokasi Absensi',
                'items' => [
                    [
                        'question' => 'Bagaimana cara menambah lokasi absensi baru?',
                        'answer' => 'Buka menu "Lokasi Absensi", klik "New Attendance Location", isi nama lokasi, pilih posisi di map, atur radius geofencing, kemudian simpan.',
                    ],
                    [
                        'question' => 'Apa itu geofencing?',
                        'answer' => 'Geofencing adalah fitur yang membatasi absensi hanya bisa dilakukan dalam radius tertentu dari lokasi yang ditentukan. Misalnya radius 200 meter dari kantor.',
                    ],
                ],
            ],
            [
                'category' => 'Laporan',
                'items' => [
                    [
                        'question' => 'Laporan apa saja yang tersedia?',
                        'answer' => 'Sistem menyediakan: Laporan Keterlambatan, Laporan Rekap Bulanan, Laporan Lembur, dan Kalender Absensi.',
                    ],
                    [
                        'question' => 'Bagaimana cara export laporan?',
                        'answer' => 'Setiap halaman laporan memiliki tombol export (Excel/PDF). Atur filter periode terlebih dahulu, kemudian klik tombol export.',
                    ],
                ],
            ],
            [
                'category' => 'Troubleshooting',
                'items' => [
                    [
                        'question' => 'Kenapa menu tidak muncul setelah login?',
                        'answer' => 'Pastikan user Anda memiliki role "admin". Jika masih bermasalah, hubungi super admin untuk memverifikasi akses Anda.',
                    ],
                    [
                        'question' => 'Kenapa tidak bisa export data?',
                        'answer' => 'Pastikan ada data yang tersedia untuk periode yang dipilih. Jika masih error, coba refresh halaman atau clear cache browser.',
                    ],
                    [
                        'question' => 'Data absensi tidak muncul?',
                        'answer' => 'Periksa filter yang aktif (tanggal, user, tipe). Reset semua filter dan coba lagi.',
                    ],
                ],
            ],
        ];
    }
}

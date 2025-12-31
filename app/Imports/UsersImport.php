<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find or create department
        $department = null;
        if (!empty($row['kode_departemen'])) {
            $department = Department::where('code', $row['kode_departemen'])
                ->where('organization_id', auth()->user()->organization_id)
                ->first();
        }

        // Find shift
        $shift = null;
        if (!empty($row['kode_shift'])) {
            $shift = Shift::where('name', $row['kode_shift'])
                ->where('organization_id', auth()->user()->organization_id)
                ->first();
        }

        return new User([
            'name' => $row['nama'],
            'nik' => $row['nik'],
            'nip' => $row['nip'] ?? null,
            'email' => $row['email'],
            'phone' => $row['telepon'] ?? null,
            'organization_id' => auth()->user()->organization_id,
            'department_id' => $department?->id,
            'shift_id' => $shift?->id,
            'role' => 'karyawan',
            'password' => Hash::make($row['password'] ?? $row['nik']), // Default password = NIK
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'required|unique:users,nik',
            'email' => 'required|email|unique:users,email',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
        ];
    }
}

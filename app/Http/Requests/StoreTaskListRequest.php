<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskListRequest extends FormRequest
{
    /**
     * Otorisasi siapa yang boleh membuat daftar tugas diurus lewat Policy
     * (di luar scope FR-01), jadi di sini cukup true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk pembuatan daftar tugas.
     *
     * Hanya 'name' dan 'description' yang terdaftar. Itu disengaja: field lain
     * yang dikirim di body (misal 'owner_id') tidak akan ikut di validated(),
     * jadi otomatis terbuang sebelum menyentuh model.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}

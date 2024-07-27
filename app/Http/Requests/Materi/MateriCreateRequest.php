<?php

namespace App\Http\Requests\Materi;

use Illuminate\Foundation\Http\FormRequest;

class MateriCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_user' => 'nullable',
            'kode_materi' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'video' => 'required|mimes:mp4',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10000'
        ];
    }
}

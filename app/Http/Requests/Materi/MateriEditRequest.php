<?php

namespace App\Http\Requests\Materi;

use Illuminate\Foundation\Http\FormRequest;

class MateriEditRequest extends FormRequest
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
            'title' => 'required',
            'video' => 'nullable|mimes:mp4',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10000'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CompleteWorkOrderRequest
 *
 * Menangani validasi untuk penyelesaian Work Order oleh pelaksana.
 */
class CompleteWorkOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activities' => 'required|array|min:1',
            'activities.*' => 'required|string',
        ];
    }
}

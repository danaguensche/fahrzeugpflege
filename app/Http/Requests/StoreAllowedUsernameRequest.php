<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAllowedUsernameRequest extends FormRequest
{
    public function authorize()
    {
        return true; // oder Admin prüfen
    }

    public function rules()
    {
        return [
            'username' => 'required|string|max:255|unique:allowed_usernames,username',
            'role' => 'required|string|in:trainee,trainer,admin',
        ];
    }
}

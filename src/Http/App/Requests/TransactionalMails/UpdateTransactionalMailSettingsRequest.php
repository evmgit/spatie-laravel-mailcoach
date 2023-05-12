<?php


namespace Spatie\Mailcoach\Http\App\Requests\TransactionalMails;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionalMailSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'store_mail' => '',
            'track_opens' => '',
            'track_clicks' => '',
        ];
    }
}

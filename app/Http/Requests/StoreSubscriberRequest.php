<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'account_number' => ['required', 'string', 'regex:/\A[0-9]{8}\z/', Rule::unique('subscribers', 'account_number')],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['required', 'string', 'max:1000'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'coordinates' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'area' => ['nullable', 'string', 'max:100'],
            'external_url' => ['nullable', 'url:http,https', 'max:2048'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'plan_id' => [
                'nullable',
                'integer',
                Rule::exists('plans', 'id')->where(fn (Builder $query) => $query->where('transaction_type', 'mrc')),
            ],
            'installation_date' => ['nullable', 'date'],
            'billing_day' => ['nullable', 'integer', 'between:1,31'],
            'bill_generation_days' => ['nullable', 'integer', Rule::in([7, 14])],
            'subscription_date' => ['nullable', 'date'],
            'credit_limit' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'pppoe_username' => ['nullable', 'string', 'max:255', Rule::unique('network_accounts', 'username')],
            'pppoe_password' => ['nullable', 'string', 'max:255'],
            'pppoe_profile' => ['nullable', 'string', 'max:255'],
            'pppoe_comment' => ['nullable', 'string', 'max:2000'],
            'olt_device' => ['nullable', 'string', 'max:255'],
            'pon_port' => ['nullable', 'string', 'max:255'],
            'nap_device' => ['nullable', 'string', 'max:255'],
            'nap_port' => ['nullable', 'string', 'max:255'],
            'onu_name' => ['nullable', 'string', 'max:255'],
            'caller_id' => ['nullable', 'string', 'max:255'],
            'mac_address' => ['nullable', 'string', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            'network_remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'plan_id' => 'plan',
            'billing_day' => 'billing day',
            'bill_generation_days' => 'bill generation',
            'pppoe_username' => 'PPPoE username',
            'pppoe_password' => 'PPPoE password',
            'pppoe_profile' => 'PPPoE profile',
        ];
    }
}

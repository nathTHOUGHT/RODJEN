<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriberRequest;
use App\Models\NetworkAccount;
use App\Models\Plan;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $queryString = static function (string $key) use ($request): string {
            $value = $request->query($key);

            return is_string($value) ? trim($value) : '';
        };

        $search = $queryString('q');
        $area = $queryString('area');
        $status = $queryString('status');

        $subscribers = Subscriber::query()
            ->with('subscription.plan')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('account_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhereHas('subscription.plan', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($area !== '', fn (Builder $query) => $query->where('area', $area))
            ->when($status !== '', fn (Builder $query) => $query->whereHas('subscription', fn (Builder $query) => $query->where('status', $status)))
            ->orderBy('account_number')
            ->simplePaginate(20)
            ->appends(['q' => $search, 'area' => $area, 'status' => $status]);

        return view('admin.subscribers.index', compact('subscribers', 'search', 'area', 'status'));
    }

    public function create(): View
    {
        $plans = Plan::query()
            ->where('transaction_type', 'mrc')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'speed_mbps']);

        return view('admin.subscribers.create', compact('plans'));
    }

    public function store(StoreSubscriberRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $request->offsetUnset('pppoe_password');

        try {
            $subscriber = DB::transaction(function () use ($data): Subscriber {
                $plan = isset($data['plan_id'])
                    ? Plan::query()
                        ->whereKey($data['plan_id'])
                        ->where('transaction_type', 'mrc')
                        ->lockForUpdate()
                        ->first()
                    : null;

                if (isset($data['plan_id']) && ! $plan) {
                    throw ValidationException::withMessages([
                        'plan_id' => __('The selected plan is invalid.'),
                    ]);
                }

                $subscriber = Subscriber::query()->create(Arr::only($data, [
                    'account_number',
                    'first_name',
                    'last_name',
                    'landmark',
                    'billing_address',
                    'contact_number',
                    'email',
                    'coordinates',
                    'remarks',
                    'area',
                    'external_url',
                    'image_url',
                ]));

                $subscription = $subscriber->subscription()->create([
                    'plan_id' => $plan?->id,
                    'installation_date' => $data['installation_date'] ?? null,
                    'billing_day' => $data['billing_day'] ?? null,
                    'subscription_date' => $data['subscription_date'] ?? null,
                    'credit_limit' => $data['credit_limit'] ?? 0,
                    'service_type' => 'fiber',
                    'status' => 'active',
                ]);

                $plan?->increment('usage_count');

                $subscription->networkAccount()->create([
                    'username' => $data['pppoe_username'] ?? null,
                    'password' => $data['pppoe_password'] ?? null,
                    'profile' => $data['pppoe_profile'] ?? null,
                    'comment' => $data['pppoe_comment'] ?? null,
                    'olt_device' => $data['olt_device'] ?? null,
                    'pon_port' => $data['pon_port'] ?? null,
                    'nap_device' => $data['nap_device'] ?? null,
                    'nap_port' => $data['nap_port'] ?? null,
                    'onu_name' => $data['onu_name'] ?? null,
                    'caller_id' => $data['caller_id'] ?? null,
                    'mac_address' => isset($data['mac_address']) ? strtoupper($data['mac_address']) : null,
                    'remarks' => $data['network_remarks'] ?? null,
                ]);

                return $subscriber;
            }, 3);
        } catch (UniqueConstraintViolationException) {
            $messages = [];

            if (Subscriber::query()->where('account_number', $data['account_number'])->exists()) {
                $messages['account_number'] = __('The account number has already been taken.');
            }

            if (filled($data['pppoe_username'] ?? null)
                && NetworkAccount::query()->where('username', $data['pppoe_username'])->exists()) {
                $messages['pppoe_username'] = __('The PPPoE username has already been taken.');
            }

            throw ValidationException::withMessages($messages ?: [
                'account_number' => __('The subscriber could not be saved because an identifier is already in use.'),
            ]);
        }

        return to_route('subscribers.details', $subscriber)->with('status', __('Subscriber added.'));
    }

    public function show(string $subscriber): View
    {
        $subscriberId = $this->subscriberId($subscriber);

        $subscriber = Subscriber::query()
            ->with(['subscription.plan', 'subscription.networkAccount'])
            ->findOrFail($subscriberId);

        return view('admin.subscribers.show', [
            'subscriber' => $subscriber,
            'subscriberData' => $this->detailsFor($subscriber),
        ]);
    }

    /** @return array<string, mixed> */
    private function detailsFor(Subscriber $subscriber): array
    {
        $subscription = $subscriber->subscription;
        $networkAccount = $subscription?->networkAccount;
        $billingDay = $subscription?->billing_day;

        return [
            'personal' => [
                'Account number' => $subscriber->account_number,
                'Name' => $subscriber->fullName(),
                'Area' => $subscriber->area,
                'Landmark' => $subscriber->landmark,
                'Address' => $subscriber->billing_address,
                'Contact number' => $subscriber->contact_number,
                'Email' => $subscriber->email,
                'Coordinates' => $subscriber->coordinates,
                'Remarks' => $subscriber->remarks,
            ],
            'service' => [
                'Plan' => $subscription?->plan?->name,
                'MRC' => $subscription?->plan ? 'PHP '.number_format((float) $subscription->plan->price, 2) : null,
                'Service type' => $subscription?->service_type ? str($subscription->service_type)->headline()->value() : null,
                'Installation date' => $subscription?->installation_date?->format('M j, Y'),
                'Subscription date' => $subscription?->subscription_date?->format('M j, Y'),
                'Bill generation day' => $billingDay ? "Day {$billingDay} of each month" : null,
                'Credit limit' => $subscription ? 'PHP '.number_format((float) $subscription->credit_limit, 2) : null,
                'Due date' => null,
                'Billing status' => $subscription?->status ? str($subscription->status)->headline()->value() : null,
                'Billing period' => $subscription ? 'Monthly' : null,
            ],
            'mikrotik' => [
                'Name' => $networkAccount?->username,
                'Password' => $networkAccount?->getRawOriginal('password') !== null ? 'Configured' : null,
                'Profile' => $networkAccount?->profile,
                'Comment' => $networkAccount?->comment,
            ],
            'parameters' => [
                'Caller ID' => $networkAccount?->caller_id,
                'MAC address' => $networkAccount?->mac_address,
            ],
            'additional' => [
                'Service state' => $subscription?->status ? str($subscription->status)->headline()->value() : null,
                'Subscription' => $subscription?->plan?->name,
                'External URL' => $subscriber->external_url,
                'Image URL' => $subscriber->image_url,
                'OLT device' => $networkAccount?->olt_device,
                'PON port' => $networkAccount?->pon_port,
                'NAP device' => $networkAccount?->nap_device,
                'NAP port' => $networkAccount?->nap_port,
                'ONU name' => $networkAccount?->onu_name,
                'Network remarks' => $networkAccount?->remarks,
                'Current balance' => null,
            ],
            'status' => $subscription?->status ?? 'active',
        ];
    }

    private function subscriberId(string $value): int
    {
        if (! ctype_digit($value)) {
            abort(404);
        }

        $normalized = ltrim($value, '0');
        $maximum = (string) PHP_INT_MAX;

        if ($normalized === ''
            || strlen($normalized) > strlen($maximum)
            || (strlen($normalized) === strlen($maximum) && strcmp($normalized, $maximum) > 0)) {
            abort(404);
        }

        return (int) $normalized;
    }
}

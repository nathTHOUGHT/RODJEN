<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $matchingTransactionTypes = collect(Plan::TRANSACTION_TYPES)
            ->filter(fn (string $label): bool => str_contains(mb_strtolower($label), mb_strtolower($search)))
            ->keys();

        $plans = Plan::query()
            ->when($search !== '', function (Builder $query) use ($search, $matchingTransactionTypes): void {
                $query->where(function (Builder $query) use ($search, $matchingTransactionTypes): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('profile_group', 'like', "%{$search}%")
                        ->orWhere('transaction_type', 'like', "%{$search}%");

                    if ($matchingTransactionTypes->isNotEmpty()) {
                        $query->orWhereIn('transaction_type', $matchingTransactionTypes);
                    }
                });
            })
            ->orderBy('name')
            ->get();

        $editingPlan = $request->integer('edit') > 0
            ? Plan::query()->find($request->integer('edit'))
            : null;

        return view('admin.products.index', [
            'plans' => $plans,
            'editingPlan' => $editingPlan,
            'profileGroups' => Plan::PROFILE_GROUPS,
            'transactionTypes' => Plan::TRANSACTION_TYPES,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $plan = $this->validatedPlan($request);

        try {
            Plan::query()->create([...$plan, 'usage_count' => 0]);
        } catch (UniqueConstraintViolationException) {
            $this->throwDuplicateNameValidation();
        }

        return to_route('products.index')->with('status', __('Plan added.'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validatedPlan = $this->validatedPlan($request, $plan);

        try {
            $plan->update($validatedPlan);
        } catch (UniqueConstraintViolationException) {
            $this->throwDuplicateNameValidation();
        }

        return to_route('products.index')->with('status', __('Plan updated.'));
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return to_route('products.index')->with('status', __('Plan deleted.'));
    }

    /** @return array{name: string, profile_group: string, transaction_type: string, speed_mbps: int, price: string} */
    private function validatedPlan(Request $request, ?Plan $plan = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')->ignore($plan),
            ],
            'profile_group' => ['required', Rule::in(array_keys(Plan::PROFILE_GROUPS))],
            'transaction_type' => ['required', Rule::in(array_keys(Plan::TRANSACTION_TYPES))],
            'speed_mbps' => ['required', 'integer', 'min:1', 'max:100000'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
        ]);

        $validated['speed_mbps'] = (int) $validated['speed_mbps'];

        return $validated;
    }

    private function throwDuplicateNameValidation(): never
    {
        throw ValidationException::withMessages([
            'name' => __('validation.unique', ['attribute' => 'name']),
        ]);
    }
}

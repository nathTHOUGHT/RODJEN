@php($planCharge = $subscriberData['service']['MRC'] ?? 'PHP —')

<article data-billing-layout="a4" class="mx-auto max-w-full border border-slate-200 bg-white px-4 pt-8 pb-4 text-xs leading-4 text-slate-700">
    <header class="grid grid-cols-3 divide-x divide-slate-900 border-b border-slate-900 px-2 pb-5">
        <div class="flex items-center px-3">
            <img src="{{ asset('brand/rodjen-wordmark.png') }}" alt="RODJEN Technet OPC"
                class="mx-auto block h-auto w-32 max-w-full object-contain">
        </div>
        <div class="px-3 py-0.5">
            <p class="font-semibold">RODJEN TECHNET OPC</p>
            <p class="text-[10px] leading-3.5">Address: 937 RODJEN HINGAL ROAD, LICAO LICAO, BRGY. MACABUD, RODRIGUEZ RIZAL 1860</p>
        </div>
        <div class="min-w-0 px-3 py-0.5 text-[10px] leading-3.5 wrap-anywhere">
            <p>Email: admin@rodjentechnet.com</p>
            <p>Contact: 09623514508</p>
        </div>
    </header>

    <h1 class="my-4 text-center text-base font-bold tracking-wide">BILLING STATEMENT</h1>

    <div class="mx-5 grid grid-cols-2 items-start gap-5">
        <section class="min-w-0 space-y-1 px-2 py-3 wrap-anywhere">
            <h2 data-receipt-name class="text-base leading-5 font-bold uppercase">{{ $subscriber->fullName() }}</h2>
            <p><span class="font-semibold">Account #:</span> {{ $subscriber->account_number }}</p>
            <p><span class="sr-only">Service address:</span> {{ filled($subscriber->billing_address) ? $subscriber->billing_address : '—' }}</p>
        </section>

        <section class="min-w-0 overflow-hidden rounded-sm border border-slate-300">
            <h2 class="a4-section-title bg-zinc-600 px-3 py-2 text-center font-bold text-white">ACCOUNT INFORMATION</h2>
            <dl class="space-y-0.5 px-4 py-3 text-[11px] leading-4 wrap-anywhere">
                <div>
                    <dt class="inline">Billing date:</dt>
                    <dd class="inline font-semibold" data-print-billing-date>Not configured</dd>
                </div>
                <div>
                    <dt class="inline">Email:</dt>
                    <dd class="inline font-semibold">{{ $subscriber->email ?: 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="inline">Contact:</dt>
                    <dd class="inline font-semibold">{{ filled($subscriber->contact_number) ? $subscriber->contact_number : 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="inline">Subscription:</dt>
                    <dd class="inline font-semibold">{{ $subscriberData['service']['Plan'] ?? 'No plan assigned' }}</dd>
                </div>
                <div>
                    <dt class="inline">Due date:</dt>
                    <dd class="inline font-semibold" data-print-due-date>Not configured</dd>
                </div>
                <div>
                    <dt class="inline">Billing period:</dt>
                    <dd class="inline font-semibold" data-print-billing-period>Billing period not configured</dd>
                </div>
                <div>
                    <dt class="inline">Internet usage:</dt>
                    <dd class="inline font-semibold">Not available</dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="mx-5 mt-3 overflow-hidden rounded-sm border border-slate-300">
        <h2 class="a4-section-title bg-zinc-600 px-3 py-2 text-center font-bold text-white">BILLING INFORMATION</h2>
        <div class="px-5 pt-3">
            <table class="w-full table-fixed border-collapse text-center text-[11px] leading-4">
                <thead>
                    <tr class="border-b border-slate-900 uppercase">
                        <th scope="col" class="px-2 py-1 font-semibold">Item</th>
                        <th scope="col" class="px-2 py-1 font-semibold">Reference</th>
                        <th scope="col" class="px-2 py-1 font-semibold">Description</th>
                        <th scope="col" class="px-2 py-1 font-semibold">Debit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-2 py-3" data-print-billing-date>Not configured</td>
                        <td class="px-2 py-3">—</td>
                        <td class="px-2 py-3">Monthly charge</td>
                        <td class="px-2 py-3">{{ $planCharge }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="relative isolate mx-2 mt-1">
            <img src="{{ asset('brand/rodjen-wordmark.png') }}" alt=""
                class="pointer-events-none absolute top-1/4 left-1/2 -z-10 h-auto w-72 max-w-[60%] -translate-x-1/2 -translate-y-1/2 opacity-10">
            <dl class="pb-3 text-[11px] leading-4">
                <div class="flex justify-between gap-4 px-6 py-0.5">
                    <dt>Previous balance:</dt>
                    <dd class="text-right">PHP —</dd>
                </div>
                @foreach (['Total debit' => $planCharge, 'Total sales' => 'PHP —', 'Discount & rebates' => 'PHP —'] as $label => $amount)
                    <div class="flex justify-between gap-4 border-t border-slate-900 px-6 py-0.5">
                        <dt>{{ $label }}:</dt>
                        <dd class="text-right">{{ $amount }}</dd>
                    </div>
                @endforeach
                <div class="flex justify-between gap-4 border-t border-slate-900 px-6 pt-1.5 text-sm font-bold">
                    <dt>Total payables:</dt>
                    <dd data-receipt-total class="text-right text-red-700">PHP —</dd>
                </div>
                @foreach (['Total payments', 'Ending balance'] as $label)
                    <div class="flex justify-between gap-4 px-6 pt-1.5 text-sm font-bold">
                        <dt>{{ $label }}:</dt>
                        <dd class="text-right text-red-700">PHP —</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
</article>

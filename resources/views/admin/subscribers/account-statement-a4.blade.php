<article data-billing-layout="a4-soa" class="mx-auto max-w-full rounded-lg border border-slate-200 bg-white p-6 text-sm leading-5 text-slate-900 shadow-sm">
    <header class="grid grid-cols-[1fr_1.4fr_1fr] items-center gap-4 border-b border-slate-900 pb-4">
        <div>
            <img src="{{ asset('brand/rodjen-wordmark.png') }}" alt="RODJEN Technet OPC"
                class="block h-auto w-[40mm] max-w-full object-contain">
        </div>
        <div class="border-l border-slate-500 pl-4">
            <p class="font-semibold uppercase">RODJEN TECHNET OPC</p>
            <p class="text-xs leading-4">937 RODJEN HINGAL ROAD, LICAO LICAO,<br>
                BRGY. MACABUD, RODRIGUEZ RIZAL 1860</p>
        </div>
        <div class="border-l border-slate-500 pl-4 text-xs leading-4">
            <p>Email: admin@rodjentechnet.com</p>
            <p>Contact: 09623514508</p>
        </div>
    </header>

    <h1 class="my-5 text-center text-xl font-bold tracking-wide">STATEMENT OF ACCOUNT</h1>

    <div class="grid grid-cols-2 gap-6">
        <section class="space-y-2 py-2">
            <h2 class="text-lg font-bold uppercase">{{ $subscriber->fullName() }}</h2>
            <p><span class="font-semibold">Account #:</span> {{ $subscriber->account_number }}</p>
            <p><span class="font-semibold">Service address:</span><br>{{ $subscriber->billing_address ?: '—' }}</p>
        </section>

        <section class="overflow-hidden rounded-md border border-slate-300">
            <h2 class="a4-soa-section-title bg-slate-700 px-4 py-2 text-center font-bold text-white">ACCOUNT INFORMATION</h2>
            <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 p-4 text-xs leading-4">
                <dt class="font-semibold">Email:</dt>
                <dd class="break-all">{{ $subscriber->email ?: 'Not provided' }}</dd>
                <dt class="font-semibold">Contact:</dt>
                <dd>{{ $subscriber->contact_number ?: 'Not provided' }}</dd>
                <dt class="font-semibold">Subscription:</dt>
                <dd>{{ $subscriberData['service']['Plan'] ?? 'No plan assigned' }}</dd>
                <dt class="font-semibold">Billing status:</dt>
                <dd>{{ $subscriberData['service']['Billing status'] ?? 'Not configured' }}</dd>
                <dt class="font-semibold">Installation date:</dt>
                <dd>{{ $subscriberData['service']['Installation date'] ?? 'Not available' }}</dd>
                <dt class="font-semibold">Billing period:</dt>
                <dd>All recorded transactions</dd>
                <dt class="font-semibold">Due date:</dt>
                <dd>{{ $subscriberData['service']['Due date'] ?? 'Not configured' }}</dd>
                <dt class="font-semibold">Internet usage:</dt>
                <dd>Not available</dd>
                <dt class="font-semibold">Total balance:</dt>
                <dd>PHP —</dd>
            </dl>
        </section>
    </div>

    <section class="mt-5 overflow-hidden rounded-md border border-slate-300">
        <h2 class="a4-soa-section-title bg-slate-700 px-4 py-2 text-center font-bold text-white">STATEMENT OF ACCOUNT SUMMARY</h2>
        <div class="p-4">
            <table class="w-full border-collapse text-left text-xs leading-5">
                <caption class="sr-only">Statement of account transaction history</caption>
                <thead>
                    <tr class="border-b border-slate-700">
                        <th scope="col" class="px-2 py-2 font-semibold">Reference</th>
                        <th scope="col" class="px-2 py-2 font-semibold">Credit</th>
                        <th scope="col" class="px-2 py-2 font-semibold">Debit</th>
                        <th scope="col" class="px-2 py-2 font-semibold">Description</th>
                        <th scope="col" class="px-2 py-2 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="px-2 py-8 text-center text-slate-600">
                            No transaction or payment records are available for this account yet.
                        </td>
                    </tr>
                </tbody>
            </table>

            <dl class="ml-auto mt-4 grid max-w-md grid-cols-[1fr_auto] gap-x-4 gap-y-1 border-t border-slate-500 pt-3 text-sm">
                <dt class="font-semibold">Total credits:</dt>
                <dd class="text-right">PHP —</dd>
                <dt class="font-semibold">Total debits:</dt>
                <dd class="text-right">PHP —</dd>
                <dt class="border-t border-slate-500 pt-2 text-base font-bold">Ending balance:</dt>
                <dd class="border-t border-slate-500 pt-2 text-right text-base font-bold">PHP —</dd>
            </dl>
        </div>
    </section>
</article>

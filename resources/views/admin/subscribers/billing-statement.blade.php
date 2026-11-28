<article data-billing-layout="thermal" class="mx-auto w-[72mm] max-w-full rounded-lg border border-slate-200 bg-white p-3 text-[10px] leading-4 text-slate-900 shadow-sm">
    <header class="text-center">
        <img src="{{ asset('brand/rodjen-wordmark.png') }}" alt="RODJEN Technet OPC"
            class="mx-auto mb-1 block h-auto w-[36mm] max-w-full object-contain">
        <p class="mb-1 text-[8px] uppercase tracking-wide">Technet OPC</p>
        <p class="text-[9px] leading-3">
            937 RODJEN HINGAL ROAD, LICAO LICAO, BRGY.<br>
            MACABUD, RODRIGUEZ RIZAL 1860<br>
            admin@rodjentechnet.com<br>
            09623514508
        </p>
    </header>

    <div class="my-2 border-y border-slate-400 py-1 text-center">
        <h1 data-receipt-title class="text-sm font-bold tracking-wide">BILLING STATEMENT</h1>
    </div>

    <section class="space-y-1 border-b border-dashed border-slate-400 pb-2">
        <h2 data-receipt-name class="text-sm font-bold uppercase">{{ $subscriber->fullName() }}</h2>
        <p><span class="font-semibold">Account:</span> #{{ $subscriber->account_number }}</p>
        <p><span class="font-semibold">Plan:</span> {{ $subscriberData['service']['Plan'] ?? 'No plan assigned' }}</p>
        <div>
            <p class="font-semibold">Service address</p>
            <p>{{ $subscriber->billing_address ?: '—' }}</p>
        </div>
    </section>

    <section class="my-2 space-y-1">
        <h2 class="text-[9px] font-bold uppercase tracking-wide">Billing details</h2>
        <p><span class="font-semibold">Billing date:</span> <span data-print-billing-date>Not configured</span></p>

        <div class="space-y-1 pt-1">
            <p class="flex justify-between gap-2"><span>Previous balance</span><span>PHP —</span></p>
            <p class="flex justify-between gap-2">
                <span>Debit amount</span>
                <span>{{ $subscriberData['service']['MRC'] ?? 'PHP —' }}</span>
            </p>
            <p class="flex justify-between gap-2"><span>Credit amount</span><span>PHP —</span></p>
        </div>
    </section>

    <section class="border-y border-slate-500 py-2">
        <p class="text-[9px] font-bold uppercase tracking-wide">Total Balance Due:</p>
        <p data-receipt-total class="text-lg font-bold">PHP —</p>
    </section>

    <section class="mt-2 border-t border-dashed border-slate-400 pt-2">
        <h2 class="mb-1 text-[9px] font-bold uppercase tracking-wide">Billing period and usage</h2>
        <p><span class="font-semibold">Due date:</span> <span data-print-due-date>Not configured</span></p>
        <p><span class="font-semibold">Billing period:</span> <span data-print-billing-period>Billing period not configured</span></p>
        <p><span class="font-semibold">Internet usage:</span> Not available</p>
    </section>

    <footer class="mt-4 border-t border-dashed border-slate-400 pt-2 text-center">
        <p class="text-[8px] font-semibold uppercase tracking-wide">Connecting communities</p>
        <p class="text-[8px] font-semibold uppercase tracking-wide">Empowering lives</p>
    </footer>
</article>

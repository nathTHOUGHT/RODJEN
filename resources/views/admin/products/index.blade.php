@php
    $dialogClasses = 'm-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-xl border border-slate-200 bg-white p-0 text-slate-900 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-white';
    $secondaryButtonClasses = 'rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800';
    $primaryButtonClasses = 'rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600';
@endphp

<x-admin.settings heading="Products and services">
    <div
        x-data
        class="space-y-6"
        @if ($errors->any() && ! $editingPlan)
            x-init="$nextTick(() => $refs.createPlanDialog.showModal())"
        @endif
    >
        @if (session('error'))
            <p role="alert" class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">
                {{ session('error') }}
            </p>
        @endif

        <x-admin.panel title="Plan catalogue">
            <x-slot:actions>
                <button
                    type="button"
                    x-ref="addPlanButton"
                    x-on:click="$refs.createPlanDialog.showModal()"
                    aria-haspopup="dialog"
                    class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                >Add plan</button>
            </x-slot:actions>

            <form method="GET" action="{{ route('products.index') }}" role="search" class="mb-6 flex flex-col gap-2 sm:ml-auto sm:max-w-xl sm:flex-row sm:items-end">
                <label for="product-search" class="grid min-w-0 flex-1 gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                    Search
                    <input
                        id="product-search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        placeholder="Search name, profile, or type"
                        class="min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                </label>
                <div class="flex gap-2">
                    <button type="submit" class="{{ $secondaryButtonClasses }}">Search</button>
                    @if ($search !== '')
                        <a href="{{ route('products.index') }}" class="{{ $secondaryButtonClasses }}">Clear</a>
                    @endif
                </div>
            </form>

            @if ($plans->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 px-6 py-12 text-center dark:border-slate-700">
                    <h3 class="font-semibold text-slate-900 dark:text-white">{{ $search !== '' ? 'No plans match your search.' : 'No plans yet.' }}</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        {{ $search !== '' ? 'Try a different name, profile, or transaction type.' : 'Use Add plan to create the first catalogue entry.' }}
                    </p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($plans as $plan)
                        <article data-test="plan-card" class="flex min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-cyan-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-950 dark:hover:border-cyan-700">
                            <header class="flex items-start justify-between gap-3 border-b border-slate-200 bg-slate-50/70 px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Plan</p>
                                    <h3 class="mt-1 break-words font-semibold text-slate-900 dark:text-white">{{ $plan->name }}</h3>
                                </div>
                                <div class="grid shrink-0 justify-items-end gap-1.5">
                                    <p class="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        {{ $plan->speed_mbps !== null ? number_format($plan->speed_mbps).' Mbps' : 'Speed not set' }}
                                    </p>
                                    <p class="rounded-full bg-cyan-100 px-3 py-1 text-sm font-bold text-cyan-900 dark:bg-cyan-950 dark:text-cyan-200">₱{{ number_format((float) $plan->price, 2) }}</p>
                                </div>
                            </header>

                            <dl class="grid flex-1 gap-4 px-4 py-4 text-sm">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Profile group</dt>
                                    <dd class="mt-1 break-words font-medium text-slate-800 dark:text-slate-200">{{ $plan->profile_group }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Transaction type</dt>
                                    <dd class="mt-1 text-slate-700 dark:text-slate-300">{{ $plan->transactionTypeLabel() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Active users</dt>
                                    <dd class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $plan->usage_count }} {{ str('user')->plural($plan->usage_count) }}</dd>
                                </div>
                            </dl>

                            <footer class="flex items-center justify-end gap-2 border-t border-slate-200 px-4 py-3 dark:border-slate-800">
                                <a href="{{ route('products.index', array_filter(['q' => $search, 'edit' => $plan->id])) }}" class="{{ $secondaryButtonClasses }}">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $plan) }}" x-on:submit="if (!confirm('Delete this plan?')) $event.preventDefault()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-red-600 dark:border-red-700 dark:bg-slate-900 dark:text-red-300 dark:hover:bg-red-950">Delete</button>
                                </form>
                            </footer>
                        </article>
                    @endforeach
                </div>
            @endif
        </x-admin.panel>

        <dialog
            x-ref="createPlanDialog"
            id="create-plan-dialog"
            aria-labelledby="create-plan-title"
            class="{{ $dialogClasses }}"
            x-on:close="$refs.addPlanButton?.focus()"
        >
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <h2 id="create-plan-title" class="text-lg font-semibold">Add Product &amp; Services</h2>
                <button type="button" x-on:click="$refs.createPlanDialog.close()" aria-label="Close add plan dialog" class="grid size-8 place-items-center rounded-md text-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:hover:bg-slate-800 dark:hover:text-white">&times;</button>
            </div>

            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                <input type="hidden" name="_plan_form" value="create">
                <div class="space-y-4 px-5 py-5">
                    @include('admin.products.form-fields', ['fieldPrefix' => 'create-plan', 'formContext' => 'create', 'plan' => null, 'autofocus' => true])
                </div>
                <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <button type="button" x-on:click="$refs.createPlanDialog.close()" class="{{ $secondaryButtonClasses }}">Cancel</button>
                    <button type="submit" class="{{ $primaryButtonClasses }}">Submit</button>
                </div>
            </form>
        </dialog>

        @if ($editingPlan)
            <dialog
                x-ref="editPlanDialog"
                id="edit-plan-dialog"
                aria-labelledby="edit-plan-title"
                class="{{ $dialogClasses }}"
                data-return-url="{{ route('products.index', $search !== '' ? ['q' => $search] : []) }}"
                x-init="$nextTick(() => $refs.editPlanDialog.showModal())"
                x-on:close="window.history.replaceState({}, '', $el.dataset.returnUrl)"
            >
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <h2 id="edit-plan-title" class="text-lg font-semibold">Edit Product &amp; Service</h2>
                    <button type="button" x-on:click="$refs.editPlanDialog.close()" aria-label="Close edit plan dialog" class="grid size-8 place-items-center rounded-md text-xl text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-cyan-600 dark:hover:bg-slate-800 dark:hover:text-white">&times;</button>
                </div>

                <form method="POST" action="{{ route('products.update', $editingPlan) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="_plan_form" value="edit:{{ $editingPlan->id }}">
                    <div class="space-y-4 px-5 py-5">
                        @include('admin.products.form-fields', ['fieldPrefix' => 'edit-plan', 'formContext' => 'edit:'.$editingPlan->id, 'plan' => $editingPlan, 'autofocus' => true])
                    </div>
                    <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                        <button type="button" x-on:click="$refs.editPlanDialog.close()" class="{{ $secondaryButtonClasses }}">Cancel</button>
                        <button type="submit" class="{{ $primaryButtonClasses }}">Save changes</button>
                    </div>
                </form>
            </dialog>
        @endif
    </div>
</x-admin.settings>

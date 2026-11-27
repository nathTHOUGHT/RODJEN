<x-layouts::app.admin-shell :title="$title ?? null">
    <div class="min-w-0 p-4 sm:p-6 lg:p-8">
        {{ $slot }}
    </div>
</x-layouts::app.admin-shell>

{{--
    Reusable "Aksi" dropdown trigger button.
    Must be placed inside a parent element with x-data="{ open: false }".
--}}
<button
    @click="open = !open"
    @click.outside="open = false"
    type="button"
    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
    :aria-expanded="open"
    aria-haspopup="true"
>
    Aksi
    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
    </svg>
</button>

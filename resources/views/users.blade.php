<x-admin-layout>
    <div class="flex flex-col items-stretch justify-start gap-5">
        <x-header-title headingTitle="Users" />
        @if (session('status'))
            <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 6000)" 
                x-show="show"
                class="text-green-400"
            >
                {{ session('status') }}
            </div>
        @endif
        @livewire('users')
    </div>
</x-admin-layout>

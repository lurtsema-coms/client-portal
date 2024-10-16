<x-admin-layout>
    <div class="flex flex-col items-center justify-start gap-5">
        <div class="flex justify-center w-full">
            <h1 class="mb-2 text-3xl font-bold text-center md:text-6xl md:mb-8 lg:text-left">Welcome to your Portal!</h1>
        </div>
        @livewire('client-info', ['client' => auth()->user()])
    </div>
</x-admin-layout>

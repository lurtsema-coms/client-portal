<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="text-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="container mx-auto">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="/" wire:navigate>
                        <x-application-logo class="block w-auto text-white fill-current h-9" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @role('admin')
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('clients')" :active="str_starts_with(request()->path(), 'clients')" wire:navigate>
                        {{ __('Clients') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('users')" :active="str_starts_with(request()->path(), 'users') || str_starts_with(request()->path(), 'add-users') || str_starts_with(request()->path(), 'edit-users')" wire:navigate>
                        {{ __('Users') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('more-info')" :active="str_starts_with(request()->path(), 'more-info')" wire:navigate>
                        {{ __('More Info') }}
                    </x-nav-link>
                </div>
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('invoices')" :active="str_starts_with(request()->path(), 'invoices')" wire:navigate>
                        {{ __('Invoices') }}
                    </x-nav-link>
                </div> --}}
                @endrole
                @role('client')
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('deliverables')" :active="request()->routeIs('deliverables') || str_starts_with(request()->path(), 'add-request') || str_starts_with(request()->path(), 'edit-request') || str_starts_with(request()->path(), 'view-request')" wire:navigate>
                        {{ __('Deliverables') }}
                    </x-nav-link>
                </div>
                @endrole
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out border border-transparent rounded-md hover:text-white focus:outline-none">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-white transition duration-150 ease-in-out rounded-md hover:text-white hover:bg-button-blue focus:outline-none focus:bg-button-blue focus:text-white">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @role('admin')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('clients')" :active="str_starts_with(request()->path(), 'clients')" wire:navigate>
                {{ __('Clients') }}
            </x-responsive-nav-link>
        </div>
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('users')" :active="str_starts_with(request()->path(), 'users') || str_starts_with(request()->path(), 'add-users') || str_starts_with(request()->path(), 'edit-users')" wire:navigate>
                {{ __('Users') }}
            </x-responsive-nav-link>
        </div>
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('more-info')" :active="str_starts_with(request()->path(), 'more-info')" wire:navigate>
                {{ __('More Info') }}
            </x-responsive-nav-link>
        </div>
        {{-- <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('invoices')" :active="str_starts_with(request()->path(), 'invoices')" wire:navigate>
                {{ __('Invoices') }}
            </x-responsive-nav-link>
        </div> --}}
        @endrole
        @role('client')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="str_starts_with(request()->path(), 'dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('deliverables')" :active="str_starts_with(request()->path(), 'deliverables') || str_starts_with(request()->path(), 'add-request') || str_starts_with(request()->path(), 'edit-request') || str_starts_with(request()->path(), 'view-request')" wire:navigate>
                {{ __('Deliverables') }}
            </x-responsive-nav-link>
        </div>
        @endrole

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium text-white" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="text-sm font-medium text-white">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" class="text-white" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="text-white">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>

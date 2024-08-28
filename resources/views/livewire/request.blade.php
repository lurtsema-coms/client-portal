<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\PersonInContact;

new class extends Component {


    public function with(): array {
        $personInContacts = PersonInContact::where('user_id', auth()->user()->id);
        return [
            'personInContacts' => (clone $personInContacts)->get(),
            'personInContact' => (clone $personInContacts)->first(),
    ];
    }
}; ?>

<div class="flex flex-col items-center justify-center w-full mx-auto">
    <div class="flex flex-col items-stretch justify-center max-w-screen-xl gap-10 lg:flex-row">
        <div class="flex flex-col items-center justify-center flex-grow gap-10 px-8 py-8 border sm:flex-row rounded-3xl">
            <img class="w-auto max-h-[12rem]" src="{{ asset('images/user.png') }}" alt="">
            <div class="flex flex-col justify-center ">
                <h1 class="mb-2 text-4xl font-bold ">{{ auth()->user()->name }}</h1>
                <h2 class="text-lg">Email: <span class="text-gray-500">{{ auth()->user()->email }}</span></h2>
                <h2 class="text-lg">Company Cell Number: <span class="text-gray-500">{{ auth()->user()->company_cell_number }}</span></h2>
                <h2 class="text-lg">Company Address: <span class="text-gray-500">{{ auth()->user()->company_address }}</span></h2>
                <h2 class="text-lg">Project Manager: <span class="text-gray-500">{{ auth()->user()->project_manager }}</span></h2>
            </div>
        </div>
        <div class="flex flex-col justify-center">
            <h1 class="mb-2 text-4xl font-bold ">Person in Contact</h1>
            <h2 class="text-lg">Name: <span class="text-gray-500">{{ $personInContact?->name }}</span></h2>
            <h2 class="text-lg">Email Address: <span class="text-gray-500">{{ $personInContact?->email }}</span></h2>
            <h2 class="mb-6 text-lg">Cell Number: <span class="text-gray-500">{{ $personInContact?->cell_number }}</span></h2>
            @if ($personInContacts->count() > 1)
            <button class="py-1 text-black transition-all duration-300 ease-in-out bg-white text-lgtracking-wide w-28 rounded-xl hover:opacity-60">View More</button>
            @endif
        </div>
    </div>
    <div class="bg-custom-gradient w-full h-[2px] -z-10 my-10"></div>
    <div class="flex flex-col-reverse justify-center w-full gap-5 md:flex-row">
        <div class="relative md:w-[25rem]">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.5 2a6.5 6.5 0 104.5 11.29l3.85 3.85a.75.75 0 001.06-1.06l-3.85-3.85A6.5 6.5 0 008.5 2zm-5 6.5a5 5 0 1110 0 5 5 0 01-10 0z" clip-rule="evenodd" />
                </svg>
            </span>
            <input type="search" wire:model="search" placeholder="Search..." class="w-full pl-10 text-black rounded-lg">
        </div>
        <a class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer bg-button-blue hover:opacity-60" href="{{ route('add-request') }}" wire:navigate>
            Add New Request
        </a>
    </div>
    @if (session('status'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 6000)" 
            x-show="show"
            class="mt-10 text-green-400"
        >
            {{ session('status') }}
        </div>
    @endif
    
    
    <div class="w-full p-3 mt-10 mb-16 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Client Requests</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Deliverable Request</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">As Needed By</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Remarks</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">Mass Texting</p>
                            <p class="italic text-gray-700 md:hidden">Client Name A</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden sm:table-cell">{{ date('D, F j, Y') }}</td>
                        <td class="hidden xl:table-cell">Details sent via email...</td>
                        <td class="rounded-r-lg">
                            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Deliverables</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Title</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">Status</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Last Update</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Remarks</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">Mass Texting</p>
                            <p class="italic text-gray-700 md:hidden">Client Name A</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden xl:table-cell">In-Progress</td>
                        <td class="hidden sm:table-cell">{{ date('D, F j, Y') }}</td>
                        <td class="hidden xl:table-cell">For Review (Sent to client)</td>
                        <td class="rounded-r-lg">
                            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    

</div>

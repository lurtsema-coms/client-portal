<?php

use Livewire\Volt\Component;
use App\Models\PersonInContact;
use App\Models\User;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {
    public $client;
    public $personInContacts;
    public $personInContact;

    public function mount(User $client) {
        $this->client = $client;
        $personInContacts = PersonInContact::where('user_id', auth()->user()->id);
        $this->personInContacts = (clone $personInContacts)->get();
        $this->personInContact = (clone $personInContacts)->first();
    }
}; ?>

<div class="flex flex-col items-center justify-center w-full mx-auto">
    <div class="flex items-center justify-between">
        <div class="flex flex-col items-stretch justify-center max-w-screen-xl gap-10 lg:flex-row">
            <div class="flex flex-col items-center justify-center flex-grow gap-10 px-8 py-8 border sm:flex-row rounded-3xl">
                <img class="w-auto max-h-[12rem]" src="{{ asset('images/user.png') }}" alt="">
                <div class="flex flex-col justify-center ">
                    <h1 class="mb-2 text-4xl font-bold ">{{ $client->name }}</h1>
                    <h2 class="text-lg">Email: <span class="text-gray-500">{{ $client->email }}</span></h2>
                    <h2 class="text-lg">Company Cell Number: <span class="text-gray-500">{{ $client->company_cell_number }}</span></h2>
                    <h2 class="text-lg">Company Address: <span class="text-gray-500">{{ $client->company_address }}</span></h2>
                    <h2 class="text-lg">Project Manager: <span class="text-gray-500">{{ $client->project_manager }}</span></h2>
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
    </div>
</div>

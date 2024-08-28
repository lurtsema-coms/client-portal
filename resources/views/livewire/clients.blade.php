<?php

use Livewire\Volt\Component;
use App\Models\User;

new class extends Component {
    public $clientTypes = ['all', 'business', 'political'];
    public $clientType = 'all';
    public $search = '';

    public function with(): array {
        $query = User::where('role', 'client')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc');

        if ($this->clientType !== 'all') {
            $query->where('client_type', $this->clientType);
        }

        return [
            'clients' => $query->get(),
        ];
    }
}; ?>
<div class="flex flex-col items-center justify-center w-full">
    <div class="flex flex-col w-full md:flex-row md:justify-between">
        <select wire:model.change="clientType" name="client_type" id="client-type" class="w-full bg-transparent border-none outline-none lg:text-3xl md:max-w-52">
            @foreach ($clientTypes as $clientType)
                <option class="text-black" value="{{ $clientType }}">{{ ucwords($clientType) }}</option>
            @endforeach
        </select>
        <div class="flex items-center justify-end w-full gap-5 md:max-w-sm">
            <input type="search" wire:model="search" placeholder="Search..." class="flex-1 text-black rounded-lg">
        </div>
    </div>
    <div class="bg-custom-gradient w-full h-[2px] -z-10 my-10"></div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Client Requests</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Deliverable Request</th>
                    <th class="hidden font-thin text-left text-gray-500 md:table-cell">Client</th>
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
                        <td class="hidden md:table-cell">Client Name A</td>
                        <td class="hidden sm:table-cell">{{ date('D, F j, Y') }}</td>
                        <td class="hidden xl:table-cell">Details sent via email...</td>
                        <td class="rounded-r-lg">
                            <a href="{{ route('requests.view-request', rand(0, 100)) }}" wire:navigate class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</a>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="grid w-full gap-8 mt-20 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($clients as $client)
        <a href="{{ route('clients.view-client', $client) }}" wire:navigate class="flex flex-col items-start justify-start hover:opacity-60">
            <img class="w-full" src="data:image/png;base64,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" alt="">
            <h3 class="mt-3 font-bold text-md">{{ $client->name }}</h3>
            <p class="text-gray-500">{{ $client->email }}</p>
        </a>
        @endforeach
    </div>
    <div>
        
    </div>
</div>


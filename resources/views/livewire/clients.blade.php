<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\ClientRequest;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    public $clientTypes = ['all', 'business', 'political'];
    public $clientType = 'all';
    public $search = '';

    public function with(): array {
        $clientQuery = User::where('role', 'client')
            ->whereNull('deleted_at')
            ->orderBy('name', 'asc');
        $requestQuery = ClientRequest::whereNull('deleted_at')
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'desc')->with('user');

        if ($this->clientType !== 'all') {
            $clientQuery->where('client_type', $this->clientType);
            $requestQuery->whereHas('user', function($query) {
                $query->where('client_type', $this->clientType);
            });
        }

        if (strlen($this->search)) {
            $clientQuery->where(function($query) {
                $query->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('role', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("DATE_FORMAT(created_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%']);
            });

            $requestQuery->where(function($query) {
                $query->orWhereHas('user', function($innerQuery) {
                    $innerQuery->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("DATE_FORMAT(created_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%'])
                    ->orWhereRaw("DATE_FORMAT(needed_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%']);
            });
        }

        return [
            'clients' => $clientQuery->paginate(12, pageName: 'clients-page'),
            'clientRequests' => $requestQuery->paginate(5, pageName: 'client-requests-page'),
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
        <div class="relative flex items-center justify-end w-full gap-5 md:max-w-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.5 2a6.5 6.5 0 104.5 11.29l3.85 3.85a.75.75 0 001.06-1.06l-3.85-3.85A6.5 6.5 0 008.5 2zm-5 6.5a5 5 0 1110 0 5 5 0 01-10 0z" clip-rule="evenodd" />
                </svg>
            </span>
            <input type="search" wire:model.live.debounce.250ms="search" placeholder="Search..." class="flex-1 w-full pl-10 text-black rounded-lg">
        </div>
    </div>
    <div class="w-full p-3 mt-5 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Client Requests</h1>
        <table class="w-full my-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Deliverable Request</th>
                    <th class="hidden font-thin text-left text-gray-500 md:table-cell">Client</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">As Needed By</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Created At</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientRequests as $clientRequest)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $clientRequest->title }}</p>
                            <p class="italic text-gray-700 md:hidden">{{ $clientRequest->user->name }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">Needed: {{ (new DateTime($clientRequest->needed_at))->format('D, F j, Y') }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">Created: {{ (new DateTime($clientRequest->created_at))->format('D, F j, Y h:i a') }}</p>
                        </td>
                        <td class="hidden md:table-cell">{{ $clientRequest->user->name }}</td>
                        <td class="hidden sm:table-cell">{{ (new DateTime($clientRequest->needed_at))->format('D, F j, Y') }}</td>
                        <td class="hidden xl:table-cell">{{ (new DateTime($clientRequest->created_at))->format('D, F j, Y h:i a') }}</td>
                        <td class="flex flex-col gap-2 py-2 rounded-r-lg md:flex-row">
                            <a href="{{ route('requests.view-request', ['client' => $clientRequest->user->id, 'clientRequest' => $clientRequest->id]) }}" wire:navigate>
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">Edit</button>
                            </a>
                            <a href="{{ route('requests.view-deliverable-details', ['client' => $clientRequest->user->id, 'id' => $clientRequest->id]) }}" wire:navigate>                                
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($clientRequests->isEmpty())
        <p class="mt-5 text-center text-gray-500">No client requests found.</p>
        @endif
        {{ $clientRequests->links() }}
    </div>
    <h2 class="self-start mt-16 text-3xl font-bold text-left">Clients</h2>
    <div class="grid w-full grid-cols-2 gap-8 my-5 place-content-center md:grid-cols-3 xl:grid-cols-4">
        @foreach ($clients as $client)
        <a href="{{ route('clients.view-client', $client) }}" wire:navigate class="flex flex-col items-start justify-start hover:opacity-60">
            <div class="relative flex items-center justify-center w-full p-3 overflow-hidden bg-white border-2 rounded-lg border-whbg-white sm:p-5 aspect-square">
                <img class="object-center max-w-full max-h-full" src="{{ $client->img_path ?? asset('images/user.png') }}" alt="">
                <span class="bg-button-blue text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded-full absolute left-2 top-2">{{ ucwords($client->client_type) }}</span>
            </div>
            <h3 class="mt-3 font-bold text-md">{{ $client->name }}</h3>
        </a>
        @endforeach
    </div>
    <div class="w-full place-self-start">
        {{ $clients->links() }}
    </div>
    <div>
        
    </div>
</div>


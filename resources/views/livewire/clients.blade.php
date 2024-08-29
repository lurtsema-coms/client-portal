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
            ->orderBy('created_at', 'desc');
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
        <div class="flex items-center justify-end w-full gap-5 md:max-w-sm">
            <input type="search" wire:model.live.debounce.250ms="search" placeholder="Search..." class="flex-1 text-black rounded-lg">
        </div>
    </div>
    <div class="bg-custom-gradient w-full h-[2px] -z-10 my-10"></div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
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
                        <td class="rounded-r-lg">
                            <a href="{{ route('requests.view-request', ['client' => $clientRequest->user->id, 'clientRequest' => $clientRequest->id]) }}" wire:navigate class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</a>
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
            <div class="relative w-full border-2 border-gray-300 aspect-square">
                <img class="absolute object-cover min-w-full min-h-full" src="{{ $client->img_path ?? asset('images/user.png') }}" alt="">
                <span class="bg-button-blue text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded-full absolute left-2 top-2">{{ ucwords($client->client_type) }}</span>
            </div>
            <h3 class="mt-3 font-bold text-md">{{ $client->name }}</h3>
            <p class="text-gray-500">{{ $client->email }}</p>
        </a>
        @endforeach
    </div>
    <div class="w-full place-self-start">
        {{ $clients->links() }}
    </div>
    <div>
        
    </div>
</div>


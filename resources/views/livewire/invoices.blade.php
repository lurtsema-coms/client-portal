<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;


new class extends Component {
    use WithPagination;

    public $search = '';

    public function  with(): array {
        $clients = User::client()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('users.name', 'asc')
            ->paginate(7);
            
        return [
            'clients' => $clients,
        ];
    }


}; ?>

<div class="flex flex-col w-full">
    <div class="flex flex-wrap items-center justify-end gap-4 mb-5">
        <input 
            class="w-full text-black rounded-lg max-w-96"
            type="search"
            placeholder="Search..." 
            wire:model.live.debounce.250ms="search"
        >
    </div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Clients</h1>
        <table class="w-full my-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Name</th>
                    <th class="hidden font-thin text-left text-gray-500 md:table-cell">Email</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">Client Type</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Invoice Count</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <tr class="border-b"wire:key="user-id{{ $client->id }}">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $client->name }}</p>
                            <p class="italic text-gray-700 md:hidden">{{ $client->email }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ $client->client_type }}</p>
                        </td>
                        <td class="hidden md:table-cell">{{ $client->email }}</td>
                        <td class="hidden xl:table-cell">{{ $client->client_type }}</td>
                        @php $invoiceCount = $client->invoice->count() @endphp
                        <td class="hidden sm:table-cell">{{ $invoiceCount }} invoice{{ $invoiceCount === 0 || $invoiceCount === 1 ? null : 's'}}</td>
                        <td class="rounded-r-lg">
                            <a href="{{ route('invoices.show-invoice', $client->id) }}" wire:navigate>                                
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!$clients->count())
            <p class="w-full text-sm italic text-center text-gray-500">No data.</p>
        @endif
        {{ $clients->links() }}
    </div>
</div>

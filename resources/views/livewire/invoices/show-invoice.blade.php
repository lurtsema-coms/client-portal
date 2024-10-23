<?php

use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    use WithFileUploads;
    use WithPagination;

    public $client;
    public $search = '';

    public function mount(User $client) {
        $this->client = $client;
    }

    public function with(): array {
        return [
            'invoices' => $this->client
                ->invoice()
                ->where(function ($query) {
                    $query->orWhere('project', 'like', '%' . $this->search . '%')
                        ->orWhere('stripe_id', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');

                })
                ->orderBy('created_at', 'desc')
                ->paginate(7),
        ];
    }

}; ?>

<div class="flex flex-col w-full">
    <x-header-title headingTitle="Invoice" />
    <div class="flex flex-wrap items-center justify-between gap-4 my-5">
        <a href="{{ route('invoices.add-invoice', $client->id) }}" wire:navigate>
            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">Add</button>
        </a>
        <input 
            class="w-full text-black rounded-lg max-w-96"
            type="search"
            placeholder="Search..." 
            wire:model.live.debounce.250ms="search"
        >
    </div>
    @if (session('status'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 6000)" 
            x-show="show"
            class="mb-5 text-green-400"
        >
            {{ session('status') }}
        </div>
    @endif

    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Client Invoices</h1>
        <p class="mt-3">{{ $client->name }}</p>
        <table class="w-full my-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Project</th>
                    <th class="hidden font-thin text-left text-gray-500 md:table-cell">Stripe Id</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">Amount</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Invoice Date</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Due Date</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Status</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    @php
                        if ($invoice->status === 'PAID') {
                            $invoiceStatus = '<span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">PAID</span>';
                        } else if ($invoice->status === 'UNPAID') {
                            $invoiceStatus = '<span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded border border-red-400">UNPAID</span>';
                        }
                    @endphp
                    <tr class="border-b"wire:key="invoice-id{{ $invoice->id }}">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $invoice->project }}</p>
                            <p class="italic text-gray-700 md:hidden">{{ $invoice->stripe_id }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ number_format($invoice->amount, 2) }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{!! $invoiceStatus !!}</p>
                        </td>
                        <td class="hidden md:table-cell">{{ $invoice->stripe_id }}</td>
                        <td class="hidden xl:table-cell">{{ number_format($invoice->amount, 2) }}</td>
                        <td class="hidden sm:table-cell">{{ $invoice->invoice_date }}</td>
                        <td class="hidden sm:table-cell">{{ $invoice->due_date }}</td>
                        <td class="hidden sm:table-cell">{!! $invoiceStatus !!}</td>
                        <td class="rounded-r-lg">
                            <a href="{{ route('invoices.edit-invoice', ['client' => $client->id, 'invoice' => $invoice->id]) }}" wire:navigate>                                
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">Edit</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!$invoices->count())
            <p class="w-full text-sm italic text-center text-gray-500">No data.</p>
        @endif
        {{ $invoices->links() }}
    </div>
</div>


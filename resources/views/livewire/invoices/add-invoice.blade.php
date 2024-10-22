<?php

use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    use WithFileUploads;
    public $client;
    public $project;
    public $stripeId;
    public $amount;
    public $invoiceDate;
    public $dueDate;
    public $status = "";
    public $invoice;

    
    public function mount(User $client) {
        $this->client = $client;
    }

    public function handleSubmit() {
        $this->validate();

        if ($this->invoice) {            
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $fileName = $uuid . '.' . $this->invoice->getClientOriginalExtension();
            $invoiceLink = url('client-invoices/pdf/' . $fileName);
            $this->invoice->storePubliclyAs('client-invoices/pdf', $fileName, 'public');
        }

        $this->client->invoice()->create([
            'stripe_id' => $this->stripeId,
            'project' => $this->project,
            'amount' => $this->amount,
            'invoice_date' => $this->invoiceDate,
            'due_date' => $this->dueDate,
            'status' => $this->status,
            'invoice_link' => $invoiceLink,
            'created_by' => auth()->user()->id,
        ]);

        $this->redirect(route('invoices.show-invoice', $this->client->id), navigate: true);
    }

    public function rules() {
        return [
            'project' => 'required|min:3',
            'stripeId' => 'required|min:3',
            'amount' => 'required|decimal:0,2',
            'invoiceDate' => 'required|date',
            'dueDate' => 'required|date',
            'status' => 'required|in:PAID,UNPAID',
            'invoice' => 'required|extensions:pdf',
        ];
    }

}; ?>

<div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
    <form action="" wire:submit="handleSubmit">
        <h1 class="font-bold lg:text-3xl">Client Invoice</h1>
        <div class="grid sm:grid-cols-2 sm:gap-x-8">
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Client</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    value="{{ $client->name }}"
                    readonly
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Project</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="project"
                >
                @error('project') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Stripe ID</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="stripeId"
                >
                @error('stripeId') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Amount</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="number"
                    wire:model="amount"
                >
                @error('amount') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Invoice Date</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="date"
                    wire:model="invoiceDate"
                >
                @error('invoiceDate')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Due Date</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="date"
                    wire:model="dueDate"
                >
                @error('dueDate')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Status</label>
                <select 
					class="w-full text-black rounded-lg"
					wire:model.change="status"
				>
                    <option value="" disabled selected>Select status</option>
                    <option value="UNPAID">UNPAID</option>
                    <option value="PAID">PAID</option>
                </select>
                @error('status')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Upload Invoice</label>
				<input 
					class="w-full max-w-lg text-black"
					type="file"
                    accept=".pdf"
					wire:model="invoice"
				>
                @error('invoice')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex justify-end">            
            <button 
                class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                type="Submit" 
            >
                Submit
            </button>
        </div>    
    </form>
</div>

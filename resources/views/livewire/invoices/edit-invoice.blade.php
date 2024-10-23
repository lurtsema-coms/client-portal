<?php

use App\Models\User;
use App\Models\Invoice;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    use WithFileUploads;
    public $client;
    public $invoice;
    public $project;
    public $stripeId;
    public $amount;
    public $invoiceDate;
    public $dueDate;
    public $status = "";
    public $invoiceFile;

    
    public function mount(User $client, Invoice $invoice) {
        $this->client = $client;
        $this->invoice = $invoice;
        $this->stripeId = $invoice->stripe_id;
        $this->project = $invoice->project;
        $this->amount = $invoice->amount;
        $this->invoiceDate = $invoice->invoice_date;
        $this->dueDate = $invoice->due_date;
        $this->status = $invoice->status;
    }

    public function handleSubmit() {
        $this->validate();

        $invoiceLink = $this->invoice->invoice_link;

        if ($this->invoiceFile) {            
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $fileName = $uuid . '.' . $this->invoiceFile->getClientOriginalExtension();
            $invoiceLink = url('client-invoices/pdf/' . $fileName);
            $this->invoiceFile->storePubliclyAs('client-invoices/pdf', $fileName, 'public');
        }

        Invoice::find($this->invoice->id)->update([
            'stripe_id' => $this->stripeId,
            'project' => $this->project,
            'amount' => $this->amount,
            'invoice_date' => $this->invoiceDate,
            'due_date' => $this->dueDate,
            'status' => $this->status,
            'invoice_link' => $invoiceLink,
            'created_by' => auth()->user()->id,
        ]);

        session()->flash('status', 'Client Invoice Updated Successfully');

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
            'invoiceFile' => 'nullable|extensions:pdf',
        ];
    }

    public function handleDelete() {
        $this->invoice->delete();

        session()->flash('status', 'Client Invoice Deleted Successfully');

        $this->redirect(route('invoices.show-invoice', $this->client->id), navigate: true);
    }

}; ?>

<div>
    <x-header-title headingTitle="Invoice" />
    <div class="w-full p-3 mt-10 text-black bg-white rounded-lg lg:p-6">
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
                        step="0.01"
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
                    <label for="" class="block tracking-wider text-gray-600">Replace Invoice</label>
                    <input 
                        class="w-full max-w-lg text-black"
                        type="file"
                        accept=".pdf"
                        wire:model="invoiceFile"
                    >
                    @error('invoice')<p class="text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            @if ($invoice->invoice_link)
            {{-- PDF --}}
            <div id="invoice-pdf" class="max-w-screen-md mt-5 aspect-[3/4]"></div>
            @endif
            
            <div class="flex gap-5 mt-10">            
                <button 
                    class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                    type="Submit" 
                >
                    Submit
                </button>
                <button 
                    data-modal-target="default-modal" data-modal-toggle="default-modal"
                    class="px-4 py-2 mt-5 text-right text-white bg-red-500 border rounded-lg hover:bg-red-600"
                    type="button" 
                >
                    Delete
                </button>
            </div>            

            <!-- Main modal -->
		<div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
			<div class="relative w-full max-w-2xl max-h-full p-4">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5">
                                    <h3 class="text-xl font-semibold text-gray-900">
                                            Delete this invoice
                                    </h3>
                                    <button type="button" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto" data-modal-hide="default-modal">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                    </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-4 space-y-4 md:p-5">
                                    <p class="text-base leading-relaxed text-gray-500">
                                            Are you sure you want to delete this client invoice? You won't be able to revert this action.
                                    </p>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5">
                                    <button wire:click="handleDelete" data-modal-hide="default-modal" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Yes, delete it</button>
                                    <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancel</button>
                            </div>
                    </div>
                </div>
            </div> 
        </form>
    </div>
</div>

@script
<script>
    initFlowbite();
    if ("{{ $invoice->invoice_link }}") {
        PDFObject.embed("{{ $invoice->invoice_link }}", "#invoice-pdf");
    }

</script>
@endscript

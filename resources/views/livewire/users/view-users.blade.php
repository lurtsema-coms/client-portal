<?php

use App\Models\User;
use Livewire\Volt\Component;
use App\Models\MoreInfoValue;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {
    public $client;
    public $assets;
    public $socials;
    public $paidInvoice;
    public $unpaidInvoice;
    public $accountManager;

    public function mount($id) {
        $client = User::find($id);
        $this->client = $client;
        $this->paidInvoice = $client->invoice()->byStatus('PAID')->get();
        $this->unpaidInvoice = $client->invoice()->byStatus('UNPAID')->get();
        $this->assets = json_decode($client->assets, true) ?? [];
        $this->socials = json_decode($client->socials, true) ?? [];
        $this->accountManager = json_decode( $client->account_manager) ?? [];
    }
}; ?>

<div class="flex flex-col items-center w-full">
    <div class="w-full max-w-screen-lg">
        <button onclick="history.back()" class="my-5 text-lg border border-t-0 border-l-0 border-r-0 place-self-start border-b-button-blue text-button-blue hover:opacity-50">← Back</button>
    </div>
    <div class="flex flex-col items-center w-full max-w-screen-lg gap-10 p-8 overflow-hidden text-black bg-white md:flex-row rounded-xl">
        <div class="relative flex w-40 overflow-hidden border rounded-full md:place-self-start aspect-square place-items-center border-slate-500">
            <img class="" src="{{ $client->img_path ?? asset('images/user.png') }}" alt="">
        </div>
        <div class="flex flex-col justify-center">
            <div class="flex flex-col">
                <h3 class="text-2xl font-bold">{{ ucwords($client->client_type) }} Client</h3>
                <div class="flex flex-col mt-3 lg:flex-row lg:gap-20">
                    <p class=""><span class="font-semibold">Personal Email: </span>{{ $client->email }}</p>
                    <p class=""><span class="font-semibold">Cell Number: </span>{{ $client->company_cell_number }}</p>
                </div>
                <p class=""><span class="font-semibold">Address: </span>{{ $client->company_address }}</p>
            </div>
            <div class="flex flex-col mt-10">
                @foreach ($client->moreInfo as $info)
                <div class="">
                    <p class=""><span class="font-semibold">{{ $info->moreInfo->label }}: </span>{{ $info->text_value ?? $info->paragraph_value ?? date('D, F j, Y', strtotime($info->date_value))  }}</p>    
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="relative flex flex-col w-full max-w-screen-lg mt-16 lg:flex-row lg:items-stretch">
        <div class="flex flex-col gap-10 max-w-[500px] lg:max-w-[600px] md:pr-5 lg:border-r lg:mr-10 border-button-blue">
            <div class="flex flex-col gap-2">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right">Person In Contact</h4>
                <div class="flex flex-col items-center justify-start gap-10">
                    @foreach ($client->personInContact as $person)
                    <div class="flex flex-col w-full gap-2">
                        <div class="flex items-center gap-5 md:gap-10">
                            <p class="w-40 font-semibold md:w-56 md:text-right">Name:</p>
                            <p>{{ $person->name }}</p>
                        </div>
                        <div class="flex items-center gap-5 md:gap-10">
                            <p class="w-40 font-semibold md:w-56 md:text-right">Email Address:</p>
                            <p>{{ $person->email }}</p>
                        </div>
                        <div class="flex items-center gap-5 md:gap-10">
                            <p class="w-40 font-semibold md:w-56 md:text-right">Cell Number:</p>
                            <p>{{ $person->cell_number }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right">Account Manager</h4>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Name:</p>
                    <p>{{ $accountManager?->name ?? '' }}</p>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Email Address:</p>
                    <p>{{ $accountManager?->email ?? '' }}</p>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Cell Number:</p>
                    <p>{{ $accountManager?->cell_number ?? '' }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right">Invoices</h4>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Paid Invoice:</p>
                    <div class="flex-1">
                        @foreach ($paidInvoice as $invoice)
                        <a href="{{ $invoice->invoice_link }}" target="_blank" class="underline hover:text-button-blue">{{ strtoupper($invoice->stripe_id) }} </a> 
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center gap-5 mt-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Unpaid Invoice:</p>
                    <div class="flex-1">
                        @foreach ($unpaidInvoice as $invoice)
                            <a href="{{ $invoice->invoice_link }}" target="_blank" class="underline hover:text-button-blue">{{ strtoupper($invoice->stripe_id) }} </a> 
                         @endforeach
                    </div>
                </div>
            </div>
        </div>
        <hr class="w-full h-px my-10 border-0 bg-button-blue text-button-blue lg:hidden">
        <div class="flex flex-col gap-10 max-w-[500px] lg:max-w-[600px] md:pr-5">
            <div class="md:w-56">
                <h4 class="mb-2 text-2xl font-bold md:text-right lg:text-left">Assets</h4>
                <div class="flex flex-col gap-2">
                    @if (!count($assets))
                    <p class="italic text-gray-400 md:text-right lg:text-left">Not available</p>
                    @endif
                    @foreach ($assets as $asset)
                    <a href="{{ $asset['link'] }}" target="_blank" class="underline cursor-pointer md:text-right lg:text-left hover:text-button-blue">{{ strtoupper($asset['label']) }}</a>
                    @endforeach
                </div>
            </div>
            <div class="md:w-56">
                <h4 class="mb-2 text-2xl font-bold md:text-right lg:text-left">Socials</h4>
                <div class="flex flex-col gap-2">
                    @if (!count($socials))
                    <p class="italic text-gray-400 md:text-right lg:text-left">Not available</p>
                    @endif
                    @foreach ($socials as $social)
                    <a href="{{ $social['link'] }}" target="_blank" class="underline cursor-pointer md:text-right lg:text-left hover:text-button-blue">{{ strtoupper($social['label']) }}</a>
                    @endforeach                
                </div>
            </div>
        </div>
    </div>
</div>

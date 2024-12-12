<?php

use Livewire\Volt\Component;
use App\Models\MoreInfoValue;

new class extends Component {
    public $client;
    public $assets;
    public $socials;
    public $paidInvoice;
    public $unpaidInvoice;
    public $accountManager;

    public function mount($client) {
        $this->client = $client;
        $this->client->moreInfos = $client->moreInfo()->whereHas('moreInfo', fn($query) => $query->whereNull('deleted_at'))->get();
        $this->paidInvoice = $client->invoice()->byStatus('PAID')->get();
        $this->unpaidInvoice = $client->invoice()->byStatus('UNPAID')->get();
        $this->assets = json_decode($client->assets, true) ?? [];
        $this->socials = json_decode($client->socials, true) ?? [];
        $this->accountManager = json_decode( $client->account_manager) ?? [];
    }
}; ?>

<div class="flex flex-col items-center w-full">
    <div class="flex flex-col items-center w-full gap-10 p-8 overflow-hidden text-black bg-white md:flex-row rounded-xl">
        <div class="relative flex w-40 p-5 overflow-hidden border rounded-full md:place-self-start aspect-square place-items-center border-slate-500">
            <img class="" src="{{ $client->img_path ?? asset('images/user.png') }}" alt="">
        </div>
        <div class="flex flex-col justify-center flex-grow">
            <div class="flex flex-col">
                <h3 class="text-2xl font-bold">{{ $client->name }}</h3>
                <div class="flex flex-col mt-3 lg:flex-row gap-x-10 lg:gap-x-20 xl:gap-x-52">
                    <p class="break-words break-all"><span class="font-semibold">Email: </span>{{ $client->email }}</p>
                    <p class=""><span class="font-semibold">Cell Number: </span>{{ $client->company_cell_number }}</p>
                </div>
                <p class=""><span class="font-semibold">Address: </span>{{ $client->company_address }}</p>
            </div>
            <div class="flex flex-col mt-10">
                @foreach ($client->moreInfos as $info)
                <div class="">
                    <p class=""><span class="font-semibold">{{ $info->moreInfo->label }}: </span>{{ $info->text_value ?? $info->paragraph_value ?? date('D, F j, Y', strtotime($info->date_value))  }}</p>    
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="relative flex flex-col justify-between w-full mt-16 lg:flex-row lg:items-stretch">
        <div class="flex flex-col gap-10 md:pr-20 lg:border-r lg:mr-10 border-button-blue xl:min-w-[800px] xl:pr-0">
            <div class="flex flex-col flex-grow gap-2">
                <h4 class="mb-2 text-2xl font-bold md:w-56">Person In Contact</h4>
                <div class="flex flex-col items-center justify-start gap-10">
                    @foreach ($client->personInContact as $person)
                    <table class="w-full">
                        <tr>
                            <td class="w-40 font-semibold align-top md:w-56">Name:</td>
                            <td class="break-all whitespace-normal align-top">{{ $person->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-40 font-semibold align-top md:w-56">Email Address:</td>
                            <td class="break-all whitespace-normal align-top">{{ $person->email }}</td>
                        </tr>
                        <tr>
                            <td class="w-40 font-semibold align-top md:w-56">Cell Number:</td>
                            <td class="break-all whitespace-normal align-top">{{ $person->cell_number }}</td>
                        </tr>
                    </table>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <h4 class="mb-2 text-2xl font-bold md:w-56">Account Manager</h4>
                <table class="w-full">
                    <tr>
                        <td class="w-40 font-semibold align-top md:w-56">Name:</td>
                        <td class="break-all whitespace-normal align-top">{{ $accountManager?->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="w-40 font-semibold align-top md:w-56">Email Address:</td>
                        <td class="break-all whitespace-normal align-top">{{ $accountManager?->email ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="w-40 font-semibold align-top md:w-56">Cell Number:</td>
                        <td class="break-all whitespace-normal align-top">{{ $accountManager?->cell_number ?? '' }}</td>
                    </tr>
                </table>
            </div>
            <div class="flex flex-col gap-1">
                <h4 class="mb-2 text-2xl font-bold md:w-56">Invoices</h4>
                <table class="w-full">
                    <tr>
                        <td class="w-40 font-semibold align-top md:w-56">Paid Invoice:</td>
                        <td class="align-top ">
                            @foreach ($paidInvoice as $invoice)
                            <a href="{{ $invoice->invoice_link }}" target="_blank" class="block underline hover:text-button-blue">{{ strtoupper($invoice->stripe_id) }} </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="w-40 font-semibold align-top md:w-56">Unpaid Invoice:</td>
                        <td class="align-top ">
                            @foreach ($unpaidInvoice as $invoice)
                            <a href="{{ $invoice->invoice_link }}" target="_blank" class="block underline hover:text-button-blue">{{ strtoupper($invoice->stripe_id) }} </a>
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <hr class="w-full h-px my-10 border-0 bg-button-blue text-button-blue lg:hidden">
        <div class="flex flex-col gap-10 max-w-[500px] lg:max-w-[600px] md:pr-5 flex-grow">
            <div class="md:w-56">
                <h4 class="mb-2 text-2xl font-bold lg:text-left">Assets</h4>
                <div class="flex flex-col gap-1">
                    @if (!count($assets))
                    <p class="italic text-gray-400 lg:text-left">Not available</p>
                    @endif
                    @foreach ($assets as $asset)
                    <a href="{{ $asset['link'] }}" target="_blank" class="underline cursor-pointer lg:text-left hover:text-button-blue">{{ strtoupper($asset['label']) }}</a>
                    @endforeach
                </div>
            </div>
            <div class="md:w-56">
                <h4 class="mb-2 text-2xl font-bold lg:text-left">Socials</h4>
                <div class="flex flex-col gap-1">
                    @if (!count($socials))
                    <p class="italic text-gray-400 lg:text-left">Not available</p>
                    @endif
                    @foreach ($socials as $social)
                    <a href="{{ $social['link'] }}" target="_blank" class="underline cursor-pointer lg:text-left hover:text-button-blue">{{ strtoupper($social['label']) }}</a>
                    @endforeach                
                </div>
            </div>
        </div>
    </div>
</div>

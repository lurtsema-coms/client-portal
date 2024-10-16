<?php

use Livewire\Volt\Component;

new class extends Component {
    public $client;

    public function mount($client) {
        $this->client = $client;
    }
}; ?>

<div class="flex flex-col items-center w-full">
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
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Name:</p>
                    <p>John Doe</p>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Email Address:</p>
                    <p>johndoe@test.email</p>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Cell Number:</p>
                    <p>9199191919</p>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right">Account Manager</h4>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Name:</p>
                    <p>Jane Dela Cruz</p>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Email Address:</p>
                    <p>janedelacruz@test.emaildsfdsd</p>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Cell Number:</p>
                    <p>9329843949</p>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right">Invoices</h4>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Paid Invoice:</p>
                    <div class="flex-1">
                        [ATTACHEMENT #3123123213, #2423123144]
                    </div>
                </div>
                <div class="flex items-center gap-5 md:gap-10">
                    <p class="w-40 font-semibold md:w-56 md:text-right">Unpaid Invoice:</p>
                    <div class="flex-1">
                        [ATTACHEMENT #3123123213, #2423123144]
                    </div>
                </div>
            </div>
        </div>
        <hr class="w-full h-px my-10 border-0 bg-button-blue text-button-blue lg:hidden">
        <div class="flex flex-col gap-10 max-w-[500px] lg:max-w-[600px] md:pr-5">
            <div class="">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right lg:text-left">Assets</h4>
                <div class="flex flex-col gap-2">
                    <p class="w-40 underline md:w-56 md:text-right lg:text-left">ONBOARDING FORM</p>
                    <p class="w-40 underline md:w-56 md:text-right lg:text-left">COMPLETED DELIVERABLES</p>
                    <p class="w-40 underline md:w-56 md:text-right lg:text-left">BRAND ASSETS</p>
                </div>
            </div>
            <div class="">
                <h4 class="mb-2 text-2xl font-bold md:w-56 md:text-right lg:text-left">Accounts</h4>
                <div class="flex flex-col gap-2">
                    <p class="w-40 underline md:w-56 md:text-right lg:text-left">WEBSITE</p>
                    <p class="w-40 underline md:w-56 md:text-right lg:text-left">FACEBOOK</p>
                </div>
            </div>
        </div>
    </div>
</div>

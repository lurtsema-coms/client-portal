<?php

use Livewire\Volt\Component;

new class extends Component {
    public $clientTypes = ['all', 'business', 'political'];
    public $search = '';
}; ?>

<div class="flex flex-col items-center justify-center w-full">
    <div class="flex flex-col gap-20 md:ml-24 md:flex-row">
        <div class="flex flex-1 flex-col gap-10 px-8 py-8 border rounded-3xl md:flex-row md:w-[47rem]">
            <img class="w-auto h-[15rem] md:h-[12rem]" src="{{ asset('images/user.png') }}" alt="">
            <div class="flex flex-col justify-center ">
                <h1 class=" text-4xl font-bold mb-2">Client Name A</h1>
                <h2 class=" text-lg">Email Address:</h2>
                <h2 class=" text-lg">Company Cell Number:</h2>
                <h2 class=" text-lg mb-6">Company Address:</h2>
                <h2 class=" text-lg">Porject Manager: Jubie</h2>
            </div>
        </div>
        <div class=" flex flex-1 flex-col justify-center ">
            <h1 class=" text-4xl font-bold mb-2">Person in Contact:</h1>
            <h2 class=" text-lg">Name:</h2>
            <h2 class=" text-lg">Email Address:</h2>
            <h2 class=" text-lg mb-6">Cell Number:</h2>
            <button class=" py-1 w-28 text-lg tracking-wide text-black transition-all duration-300 ease-in-out rounded-xl bg-white hover:opacity-60">View More</button>
        </div>
    </div>
    <div class="bg-custom-gradient w-full h-[2px] -z-10 my-10"></div>
    <div class="w-full p-3 text-black bg-white rounded-lg mb-16 lg:p-6">
        <h1 class="font-bold lg:text-3xl">Client Requests</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Deliverable Request</th>
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
                        <td class="hidden sm:table-cell">{{ date('D, F j, Y') }}</td>
                        <td class="hidden xl:table-cell">Details sent via email...</td>
                        <td class="rounded-r-lg">
                            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Deliverables</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Title</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">Status</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Last Update</th>
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
                        <td class="hidden xl:table-cell">In-Progress</td>
                        <td class="hidden sm:table-cell">{{ date('D, F j, Y') }}</td>
                        <td class="hidden xl:table-cell">For Review (Sent to client)</td>
                        <td class="rounded-r-lg">
                            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    

</div>

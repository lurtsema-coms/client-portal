<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex flex-col items-center justify-start w-full gap-10 mt-10">
    <div class="w-full p-3 mb-16 text-black bg-white rounded-lg lg:p-6">
        <div class="flex flex-row justify-between w-ful">
            <h1 class="font-bold lg:text-3xl">Business</h1>
            <a href="#" class="max-w-60" wire:navigate>
                <div class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer h-11 bg-button-blue hover:opacity-60">
                    Edit
                </div>
            </a>
        </div>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Label</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Created By</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 lg:table-cell whitespace-nowrap">Created At</th>
                </tr>
            </thead>
            <tbody>
                @for ($i=0; $i<10; $i++)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">More Info Label</p>
                            <p class="italic text-gray-700 md:hidden">John Doe</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">John Doe</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y') }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="w-full p-3 mb-16 text-black bg-white rounded-lg lg:p-6">
        <div class="flex flex-row justify-between w-ful">
            <h1 class="font-bold lg:text-3xl">Political</h1>
            <a href="#" class="max-w-60" wire:navigate>
                <div class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer h-11 bg-button-blue hover:opacity-60">
                    Edit
                </div>
            </a>
        </div>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Label</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Created By</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 lg:table-cell whitespace-nowrap">Created At</th>
                </tr>
            </thead>
            <tbody>
                @for ($i=0; $i<5; $i++)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">More Info Label</p>
                            <p class="italic text-gray-700 md:hidden">John Doe</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">John Doe</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y') }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

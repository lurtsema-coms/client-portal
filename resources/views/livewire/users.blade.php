<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex flex-col items-center justify-center w-full">
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Users</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Name</th>
                    <th class="hidden font-thin text-left text-gray-500 md:table-cell">Email</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">Role</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Created At</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 5; $i++)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">Test User 1</p>
                            <p class="italic text-gray-700 md:hidden">test@example.com</p>
                            <p class="text-sm text-gray-500 sm:hidden">client</p>
                        </td>
                        <td class="hidden md:table-cell">test@example.com</td>
                        <td class="hidden xl:table-cell">admin</td>
                        <td class="hidden sm:table-cell">{{ date('D, F j, Y') }}</td>
                        <td class="rounded-r-lg">
                            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

<?php

use App\Models\User;
use App\Models\MoreInfo;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array {
        $businessMoreInfos = MoreInfo::where('client_type', 'business')->with('user')->get();
        $businessLastUpdate = MoreInfo::where('client_type', 'business')->latest('updated_at')->first();
        $politicalMoreInfos = MoreInfo::where('client_type', 'political')->with('user')->get();
        $politicalLastUpdate = MoreInfo::where('client_type', 'political')->latest('updated_at')->first();

        return [
            'businessMoreInfos' => $businessMoreInfos,
            'businessLastUpdate' => $businessLastUpdate,
            'politicalMoreInfos' => $politicalMoreInfos,
            'politicalLastUpdate' => $politicalLastUpdate,
    ];
    }
}; ?>

<div class="flex flex-col items-center justify-start w-full gap-10 mt-10">
    <div class="w-full p-3 mb-16 text-black bg-white rounded-lg lg:p-6">
        <div class="flex flex-row justify-between w-full">
            <h1 class="font-bold lg:text-3xl">Business</h1>
            <a href="{{ route('more-info.edit', 'business') }}" class="max-w-60" wire:navigate>
                <div class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer h-11 bg-button-blue hover:opacity-60">
                    Edit
                </div>
            </a>
        </div>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Label</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Data Type</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Created By</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($businessMoreInfos as $businessInfo)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $businessInfo->label }}</p>
                            <p class="text-sm italic text-gray-500 sm:hidden">Data Type: {{ $businessInfo->data_type }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">Created by {{ $businessInfo->user->name }} on {{ date('D, F j, Y h:i a', strtotime($businessInfo->created_at)) }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $businessInfo->data_type }}</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $businessInfo->user->name }}</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y h:i a', strtotime($businessInfo->created_at)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($businessMoreInfos->isEmpty())
            <p class="w-full mt-3 text-sm text-center text-slate-500">No data.</p> 
        @else
            <p class="w-full mt-3 text-sm text-center text-slate-500">Last Update by {{ $businessLastUpdate->user->name }} on {{ date('D, F j, Y h:i a', strtotime($businessLastUpdate->updated_at)) }}</p> 
        @endif
    </div>
    <div class="w-full p-3 mb-16 text-black bg-white rounded-lg lg:p-6">
        <div class="flex flex-row justify-between w-full">
            <h1 class="font-bold lg:text-3xl">Political</h1>
            <a href="{{ route('more-info.edit', 'political') }}" class="max-w-60" wire:navigate>
                <div class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer h-11 bg-button-blue hover:opacity-60">
                    Edit
                </div>
            </a>
        </div>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Label</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Data Type</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Created By</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($politicalMoreInfos as $politicalInfo)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $politicalInfo->label }}</p>
                            <p class="text-sm italic text-gray-500 sm:hidden">Data Type: {{ $politicalInfo->data_type }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">Created by {{ $politicalInfo->user->name }} on {{ date('D, F j, Y h:i a', strtotime($politicalInfo->created_at)) }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $politicalInfo->data_type }}</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $politicalInfo->user->name }}</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y h:i a', strtotime($politicalInfo->created_at)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($politicalMoreInfos->isEmpty())
            <p class="w-full mt-3 text-sm text-center text-slate-500">No data.</p> 
        @else
            <p class="w-full mt-3 text-sm text-center text-slate-500">Last Update by {{ $politicalLastUpdate->user->name }} on {{ date('D, F j, Y h:i a', strtotime($politicalLastUpdate->updated_at)) }}</p> 
        @endif
    </div>
</div>

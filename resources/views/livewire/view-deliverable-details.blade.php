<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] 
class extends Component {

    public $request;

    public $title = '';
    public $date_need = '';
    public $remarks = '';
    public $subItems = [];

    public function mount()
    {
        $id = request()->id;

        $request = ClientRequest::find($id);
        $this->authorize('view', $request);
        
        $this->request = $request;
        $this->title = $request->title;
        $this->date_need = $request->needed_at;
        $this->remarks = $request->remarks;
        $this->subItems = json_decode($request->subitems, true) ?? [];
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <x-header-title headingTitle="Deliverables" backButton="true" />
    <div class="w-full p-10 text-black bg-white rounded-lg">
        <h1 class="font-bold text-black lg:text-3xl">Deliverable Details</h1>
        <div class="grid sm:grid-cols-2 sm:gap-x-8">
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Client</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    value="{{ $request->user->name }}"
                    disabled
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Created At</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    value="{{ date('D, F j, Y h:i a', strtotime($request->created_at)) }}"
                    disabled
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Title</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    value="{{ $request->title }}"
                    disabled
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Date Need</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="date"
                    value="{{ $request->needed_at }}"
                    disabled
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Status</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    value="{{ $request->status }}"
                    disabled
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Last Update</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    value="{{ date('D, F j, Y h:i a', strtotime($request->updated_at)) }}"
                    disabled
                >
            </div>

        </div>
        @if($remarks)
        <div class="w-full mt-10 space-y-2" wire:ignore>
            <label for="" class="block tracking-wider text-gray-600">Remarks</label>
            <div>
                {!! $remarks !!}
            </div>
        </div>
        @endif
        <div 
            x-data="{ isOpen: false }" 
            x-init="$watch('isOpen', value => document.body.style.overflow = value ? 'hidden' : 'auto')"
        >
            @php
                $extension = pathinfo($request->img_path, PATHINFO_EXTENSION);
            @endphp
            @if($request->img_path)
                <div class="mt-10 space-y-2">
                    <label for="" class="block tracking-wider text-gray-600">Attached File</label>
                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg']))
                        <img @click="isOpen = true" class="max-w-full cursor-pointer max-h-96" src="{{ $request->img_path }}" alt="">
                    @elseif ($extension === 'pdf')
                        <a href="{{ $request->img_path }}" target="_blank" class="block text-blue-500 underline">View PDF</a>
                    @endif
                </div>

                <!-- Modal -->
                <div x-show="isOpen" 
                    class="fixed inset-0 z-50 flex overflow-auto bg-black bg-opacity-75"
                    x-cloak
                >
                    
                    <!-- Close Button -->
                    <button @click="isOpen = false" 
                            class="absolute text-2xl font-bold text-white top-5 right-5">
                        &times;
                    </button>
                    
                    <!-- Zoomed Image -->
                    <div class="p-5 m-auto">
                        <img 
                            class="w-full max-w-4xl max-h-[40rem] " src="{{ $request->img_path }}" 
                            alt=""
                            @click.outside="isOpen=false"
                        >
                    </div>
                </div>
            @endif
        </div>

        {{-- sub items --}}
        @if (count($subItems))
        <div x-data="{ isOpen: false, title: '', description: '', link: '' }" x-init="$watch('isOpen', value => document.body.style.overflow = value ? 'hidden' : 'auto')">
            <div class="w-full mt-10 space-y-2" wire:ignore>
                <label for="" class="block tracking-wider text-gray-600">Sub Items</label>
                <div class="grid grid-cols-1 gap-10 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($subItems as $key => $subItem)
                    <div class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-xl md:max-w-sm">
                        <div 
                            @click="
                                title = `{{ $subItem['title'] }}`; 
                                description = `{{ isset($subItem['description']) ? $subItem['description'] : '' }}`;
                                link = `{{ isset($subItem['link']) ? $subItem['link'] : '' }}`;
                                isOpen = true;
                            " 
                            class="relative flex items-center justify-center w-full overflow-hidden cursor-pointer h-52"
                        >
                            @if ($subItem['link'])
                            <img class="absolute top-0 left-0 object-cover min-w-full min-h-full rounded-t-lg" src="{{ $subItem['link'] }}" alt="" />
                            @else
                            <p class="italic text-gray-500">No image to show...</p>
                            @endif
                        </div>
                        <div class="p-5">
                            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">{{ $subItem['title'] }}</h5>
                            <p class="mb-3 font-normal text-gray-700">{{ $subItem['description'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal -->
            <div x-cloak x-show="isOpen" id="default-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
                <div class="relative w-full max-w-2xl max-h-full p-4">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow" @click.outside="isOpen = false">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Sub Item Details
                            </h3>
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto" @click="isOpen = false">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 space-y-4 md:p-5">
                            <h1 x-text="title" class="text-xl font-bold "></h1>
                            <img x-show="!!link" class="max-w-full rounded-lg max-h-96" x-bind:src="link" alt="">
                            <p class="text-gray-500" x-text="description"></p>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5">
                            <a x-show="!!link" x-bind:href="link" class="text-white bg-button-blue hover:opacity-50 font-medium rounded-lg text-sm px-5 py-2.5 text-center" download>Download Photo</a>
                            <button @click="isOpen = false" type="button" :class="link && 'ms-3'" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        @endif
    </div>
</div>
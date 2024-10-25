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

    public function mount()
    {
        $id = request()->id;

        $request = ClientRequest::find($id);
        $this->authorize('view', $request);
        
        $this->request = $request;
        $this->title = $request->title;
        $this->date_need = $request->needed_at;
        $this->remarks = $request->remarks;
        
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <x-header-title headingTitle="Deliverables" backButton="true" />
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <div class="grid sm:grid-cols-2 sm:gap-x-8">
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Title</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="title"
                    disabled
                >
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Date Need</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="date"
                    wire:model="date_need"
                    disabled
                >
            </div>
        </div>
        <div class="w-full mt-5 space-y-2" wire:ignore>
            <label for="" class="block tracking-wider text-gray-600">Remarks</label>
            <div>
                {!! $remarks !!}
            </div>
        </div>
        <div 
            x-data="{ isOpen: false }" 
            x-init="$watch('isOpen', value => document.body.style.overflow = value ? 'hidden' : 'auto')"
        >
            @php
                $extension = pathinfo($request->img_path, PATHINFO_EXTENSION);
            @endphp
            @if($request->img_path)
                <div class="mt-4 space-y-2">
                    <label for="" class="block tracking-wider text-gray-600">Update</label>
                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg']))
                        <img @click="isOpen = true" class="max-w-3xl cursor-pointer max-h-96" src="{{ $request->img_path }}" alt="">
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


        <div class="mt-4 space-y-2">
            <label for="" class="block tracking-wider text-gray-600">Status</label>
            <p class="inline-block px-4 py-1 text-white bg-sky-600 rounded-2xl">{{ $request->status }}</p>
        </div>
        <div class="mt-4 space-y-2">
            <label for="" class="block tracking-wider text-gray-600">Last Update</label>
            <p>{{ date('D, F j, Y', strtotime($request->updated_at)) }}</p>
        </div>
    </div>
</div>
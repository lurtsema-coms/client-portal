<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] 
class extends Component {

    #[Validate('required')] 
    public $title = '';
    #[Validate('required')] 
    public $date_need = '';
    #[Validate('required')] 
    public $remarks = '';

    public function addRequest()
    {
        $this->validate();

        $user_id = ClientRequest::insertGetId([
            'title' => $this->title,
            'status' => 'PENDING',
            'user_id' => auth()->user()->id,
            'needed_at' => $this->date_need,
            'remarks' => $this->remarks,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->reset(['title', 'date_need', 'remarks']);
        
        session()->flash('status', 'Request Successfully Added');
        $this->redirect('/request', navigate: true);
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold lg:text-7xl">Create Request</h1>
        <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
    </div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <form action="" wire:submit="addRequest">
            <h1 class="font-bold lg:text-3xl">Request Form</h1>
            <div class="grid sm:grid-cols-2 sm:gap-x-8">
                <div class="mt-5 space-y-2">
                    <label for="" class="block tracking-wider text-gray-600">Title</label>
                    <input 
                        class="w-full text-black rounded-lg"
                        type="text"
                        wire:model="title"
                    >
                    @error('title') <p class="text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-5 space-y-2">
                    <label for="" class="block tracking-wider text-gray-600">Date Need</label>
                    <input 
                        class="w-full text-black rounded-lg"
                        type="date"
                        wire:model="date_need"
                    >
                    @error('date_need')<p class="text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="w-full mt-5 space-y-2" wire:ignore>
                    <label for="" class="block tracking-wider text-gray-600">Remarks</label>
                    <trix-editor
                        class="trix"
                        x-data
                        x-on:trix-change="$dispatch('input', event.target.value)"
                        x-ref="trix"
                        wire:model.debounce.60s="remarks"
                        wire:key="uniqueKey"
                    ></trix-editor>
            </div>
            @error('remarks')<p class="mt-2 text-red-500">{{ $message }}</p>@enderror
            <button 
                class="float-right px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                type="Submit" 
            >
                Submit
            </button>
        </form>
    </div>
</div>
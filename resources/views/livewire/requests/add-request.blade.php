<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {

    
    public $title = '';
    public $needed_at = '';
    public $remarks = '';


    public function mount()
    {
        $this->request_info = [
            [
                'title' => '',
                'needed_at' => '',
                'remarks' => '',
            ]
        ];

    }

    public function addRequest()
    {

        $user_id = ClientRequest::insertGetId([
            'title' => $this->title,
            'status' => 'PENDING',
            'user_id' => auth()->user()->id,
            'needed_at' => $this->needed_at,
            'remarks' => $this->remarks,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        


        $this->reset(['title', 'needed_at', 'remarks']);
        
        session()->flash('status', 'Request Successfully added');
        $this->redirect('/request', navigate: true);
    }

    public function rules()
    {
        $rules = [
            'title' => 'required',
            'needed_at' => 'required',
            'remarks' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'request_info.*.title.required' => 'The name field is required.',
            'request_info.*.needed_at.required' => 'The needed date field is required.',
            'request_info.*.remarks.required' => 'The remarks field is required.',
        ];
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
                        wire:model="needed_at"
                    >
                    @error('needed_at')<p class="text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="mt-5 space-y-2">
                    <label for="" class="block tracking-wider text-gray-600">Remarks</label>
                    <textarea 
                        id="remarks"
                        class="w-full text-black rounded-lg"
                        wire:model="remarks"
                    ></textarea>
                    @error('remarks')<p class="text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <button 
                class="float-right px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                type="Submit" 
            >
                Submit
            </button>
        </form>
    </div>
</div>


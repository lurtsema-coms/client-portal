<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] 
class extends Component {

    public $request;

    #[Validate('required')] 
    public $title = '';
    #[Validate('required')] 
    public $date_need = '';
    #[Validate('required')] 
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

    public function editRequest()
    {
        $this->validate();
        $this->authorize('update', $this->request);

        $this->request->update([
            'title' => $this->title,
            'needed_at' => $this->date_need,
            'remarks' => $this->remarks,
        ]);

        session()->flash('status', 'Request Successfully Edited');
        $this->redirect('/deliverables', navigate: true);
    }

    public function handleDelete() {
        $this->authorize('delete', $this->request);
        $this->request->delete();

        session()->flash('status', 'Request Successfully Deleted');
        $this->redirect('/deliverables', navigate: true);
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <x-header-title headingTitle="Edit Request" backButton="true" />
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <form action="" wire:submit="editRequest">
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
                class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                type="Submit" 
            >
                Submit
            </button>
            <button 
                data-modal-target="default-modal" data-modal-toggle="default-modal"
                class="px-4 py-2 mt-5 ml-3 text-right text-white bg-red-500 border rounded-lg hover:bg-red-600"
                type="button" 
            >
                Delete
            </button>
            
            <!-- Main modal -->
            <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
                <div class="relative w-full max-w-2xl max-h-full p-4">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Delete this item
                            </h3>
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto" data-modal-hide="default-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 space-y-4 md:p-5">
                            <p class="text-base leading-relaxed text-gray-500">
                                Are you sure you want to delete this item? You won't be able to revert this action.
                            </p>
                        </div>
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5">
                            <button wire:click="handleDelete" data-modal-hide="default-modal" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Yes, delete it</button>
                            <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>  
        </form>
    </div>
</div>

@script
<script>
    initFlowbite();
</script>
@endscript
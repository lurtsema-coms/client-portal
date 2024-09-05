<?php

use App\Models\ClientRequest;
use App\Models\MoreInfo;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {
    public $clientType;
    public $moreInfos = [];

    public function mount($clientType) {
        $this->clientType = $clientType;
        $moreInfos = MoreInfo::where('client_type', $this->clientType)->get()->toArray();
        $this->moreInfos = $moreInfos;
    }

    public function addItem() {
        $this->moreInfos[] = [
            'id' => '',
            'client_type' => $this->clientType,
            'label' => '',
        ];
    }

    public function removeItem($index)
    {        
        unset($this->moreInfos[$index]);
    }

    public function handleSubmit() {
        $this->validate();
        
        MoreInfo::where('client_type', $this->clientType)->update(['deleted_at' => now()]);

        foreach ($this->moreInfos as $moreInfo) {
            if (!$moreInfo['id']) {
                MoreInfo::create([
                    'client_type' => $this->clientType,
                    'label' => $moreInfo['label'],
                    'created_by' => auth()->user()->id,
                    'deleted_at' => null,
            ]);
            } else {
                MoreInfo::withTrashed()->where('id', $moreInfo['id'])->update([
                    'client_type' => $this->clientType,
                    'label' => $moreInfo['label'],
                    'updated_by' => auth()->user()->id,
                    'deleted_at' => null,
                ]);
            }
        }

        session()->flash('status', 'Data has been updated');
        $this->redirect('/more-info', navigate: true);

    }

    public function rules()
    {
        return [
            'moreInfos.*.label' => 'required|min:3',
        ];
    }

    public function messages()
    {
        return [
            'moreInfos.*.label.required' => 'This field is required.',
        ];
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold lg:text-7xl">More Info</h1>
        <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
    </div>
    <div class="w-full p-3 mt-10 mb-16 text-black bg-white rounded-lg lg:p-6">
        <form class="flex flex-col items-start justify-center max-w-screen-sm" wire:submit="handleSubmit">
            <h1 class="font-bold lg:text-3xl">{{ ucwords($clientType) }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                Renaming a label will just update the name of the label but deleting an item means also deleting all client info that is attached to the label.
            </p>
                <input type="hidden" value="">
                @foreach ($moreInfos as $index => $moreInfo)
                    <div class="w-full">
                        <div class="mt-5 space-y-2">
                            <label for="" class="block tracking-wider text-gray-600">Label</label>
                            <input 
                                class="w-full max-w-lg text-black rounded-lg"
                                type="text"
                                wire:model="moreInfos.{{ $index }}.label"
                            >
                            @error("moreInfos.$index.label") <p class="text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="removeItem({{ $index }})"
                            >
                            Delete
                        </button>
                    </div>
                @endforeach
            <div class="flex flex-row w-full gap-5">
                <button 
                    class="block px-2 py-1 mt-5 text-white bg-gray-500 border rounded-lg hover:bg-gray-600"
                    type="button"
                    wire:click="addItem"
                >
                    Add item
                </button>

                <button 
                    class="block px-2 py-1 mt-5 text-white border rounded-lg bg-button-blue hover:opacity-75"
                    type="submit" 
                >
                    Save
                </button>

            </div>
        </form>
    </div>
</div>
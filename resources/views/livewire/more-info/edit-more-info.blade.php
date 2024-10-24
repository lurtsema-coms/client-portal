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
    public $validDataTypes = ['text', 'paragraph', 'date'];

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
            'data_type' => '',
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
                    'data_type' => $moreInfo['data_type'],
                    'created_by' => auth()->user()->id,
                    'deleted_at' => null,
            ]);
            } else {
                MoreInfo::withTrashed()->where('id', $moreInfo['id'])->update([
                    'client_type' => $this->clientType,
                    'label' => $moreInfo['label'],
                    'data_type' => $moreInfo['data_type'],
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
            'moreInfos.*.data_type' => 'required|min:3|'.'in:'.implode(',', $this->validDataTypes),
        ];
    }

    public function messages()
    {
        return [
            'moreInfos.*.label.required' => 'This field is required.',
            'moreInfos.*.data_type.required' => 'This field is required.',
            'moreInfos.*.data_type.in' => 'The selected data type is invalid.',
        ];
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <x-header-title headingTitle="More Info" backButton="true" />
    <div class="w-full p-3 mb-16 text-black bg-white rounded-lg lg:p-6">
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
                        <div class="mt-5 space-y-2">
                            <label for="" class="block tracking-wider text-gray-600">Data Type</label>
                            <select 
                                class="w-full max-w-lg text-black rounded-lg"
                                wire:model.change="moreInfos.{{ $index }}.data_type"
                            >
                            <option value="" selected disabled>Select data type</option>
                            @foreach ($validDataTypes as $dataType)
                                <option value="{{ $dataType }}">{{ ucwords($dataType) }}</option>
                            @endforeach
                            </select>
                            @error("moreInfos.$index.data_type") <p class="text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="removeItem({{ $index }})"
                            >
                            Delete
                        </button>
                        <hr class="mt-5">
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
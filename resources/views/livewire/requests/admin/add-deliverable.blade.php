<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {
  use WithFileUploads;

  public $client;
  public $role;
  public $photo;
  public $img_path = '';
  public $title = '';
  public $date_need = '';
  public $status = '';
  public $remarks = '';
  public $subItems = [];

  public function mount(User $client) {
    $this->client = $client;
  }

  public function addSubItem() {
    $this->subItems[] = [
      'title' => '',
      'description' => '',
      'link' => '',
      'file' => '',
    ];
  }

  public function removeSubItem($index) {
    unset($this->subItems[$index]);
    $this->subItems = array_values($this->subItems);
  }

  public function handleSave()
    {
        $this->validate();

        $file = $this->photo;
		$subItems = [];
		foreach ($this->subItems as $subItem) {
			$subItemFile = $subItem['file'];
			if ($subItemFile) {
				$uuid = substr(Str::uuid()->toString(), 0, 8);
				$file_name = $uuid . '.' . $subItemFile->getClientOriginalExtension();
				$link = url('images/sub-items/' . $file_name);
				$subItemFile->storePubliclyAs('images/sub-items', $file_name, 'public');
				unset($subItem['file']);
				$subItem['link'] = $link;
			}
			$subItems[] = $subItem;
		}
        $clientRequest = ClientRequest::create([
            'title' => $this->title,
            'status' => $this->status,
            'user_id' => $this->client->id,
            'needed_at' => $this->date_need,
            'remarks' => $this->remarks,
			'subitems' => sizeof($subItems) ? json_encode($subItems) : null,
            'updated_by' => auth()->user()->id,
        ]);

        if ($file) {    
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $file_name = $uuid . '.' . $file->getClientOriginalExtension();
            $this->img_path = url('images/client-request/' . $file_name);
            $file->storePubliclyAs('images/client-request', $file_name, 'public');

            $clientRequest->update([
                'img_path' => $this->img_path
            ]);
        }

        session()->flash('success', 'Deliverable added successfully.');
        $this->redirect(route('clients.view-client', $this->client->id), navigate: true);
    }

	public function rules() {
		$rules = [
			'title' => 'required',
			'date_need' => 'required|date',
			'status' => 'required|in:'.implode(',',config('global.deliverable_statuses')),
			'subItems.*.title' => 'required|min:3|max:255',
			'subItems.*.description' => 'nullable',
			'subItems.*.link' => 'nullable|url',
			'subItems.*.file' => 'nullable|file|mimes:jpg,jpeg,png,svg',
		];
		return $rules;
	}

	public function messages(): array {
		return [
			'subItems.*.title.required' => 'This field is required.',
			'subItems.*.file.mimes' => 'Invalid file type.'
		];
	}
}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
  <x-header-title headingTitle="Client Deliverable" backButton="true" />
  @if (session('success'))
    <div 
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 6000)" 
        x-show="show"
        class="text-green-400"
    >
        {{ session('success') }}
    </div>
  @endif
  <div class="">
    <form action="" wire:submit="handleSave">
      <div class="mx-auto space-y-6">
        <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
        <h1 class="font-bold text-black lg:text-3xl">Deliverable Form</h1>
        <div class="grid sm:grid-cols-2 sm:gap-x-8">
          <div class="mt-5 space-y-2">
            <label for="" class="block tracking-wider text-gray-600">Client Name</label>
            <input 
              class="w-full text-black rounded-lg"
              type="text"
              value="{{ $client->name }}"
              readonly
            >
          </div>
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
        <div class="mt-5 space-y-2">
          <label for="" class="block tracking-wider text-gray-600">Status</label>
          <select 
            class="w-full text-black rounded-lg"
            wire:model.change="status"
          >
            <option value="" disabled selected>Select status</option>
            @foreach (config('global.deliverable_statuses') as $deliverableStatus)
              <option value="{{ $deliverableStatus }}">{{ $deliverableStatus }}</option>
            @endforeach
          </select>
          @error('status')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
        <div class="mt-5 space-y-2">
          @php
            $extension = pathinfo($img_path, PATHINFO_EXTENSION);
          @endphp
          <label for="" class="block tracking-wider text-gray-600">Upload Photo/PDF</label>
          <input 
            class="w-full max-w-lg text-black"
            type="file"
            wire:model="photo"
          >
          @if ($photo)
            @if (in_array($photo->extension(), ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'])) 
              <img src="{{ $photo->temporaryUrl() }}" class="mb-5 rounded-lg shadow-md max-w-48">
            @endif
          @endif
          @error('photo')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
      </div>
      <div class="w-full mt-5 space-y-2 sm:col-span-2" wire:ignore>
        <label for="" class="block tracking-wider text-gray-600">Remarks</label>
        <trix-editor
          class="text-black trix"
          x-data
          x-on:trix-change="$dispatch('input', event.target.value)"
          x-ref="trix"
          wire:model.debounce.60s="remarks"
          wire:key="uniqueKey"
        ></trix-editor>
          @error('remarks')<p class="mt-2 text-red-500">{{ $message }}</p>@enderror
      </div>

      {{-- sub-items --}}
      <div class="mt-5 space-y-2">
        <label for="" class="block tracking-wider text-gray-600">Sub Items</label>
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
          @foreach ($subItems as $index => $subItem)
            <div class="grid grid-cols-4 overflow-hidden text-sm text-black border border-gray-300 rounded-md">
              <div class="flex items-center flex-grow-0 px-2 py-1 bg-white border-r border-gray-300">
				<p class="text-gray-500">Title</p>	
              </div>
              <div class="flex flex-col items-start justify-center col-span-3 px-2 py-1 bg-white">
                <input wire:model="subItems.{{ $index }}.title" type="text" class="w-full py-1 border-t-0 border-b border-l-0 border-r-0 focus:outline-0 focus:border-button-blue border-button-blue">
				@error("subItems.$index.title") <p class="text-red-500">{{ $message }}</p> @enderror
              </div>
              <div class="flex items-center px-2 py-1 bg-white border-t border-r border-gray-300">
                <p class="text-gray-500">Description </p>
              </div>
              <div class="flex flex-col items-start justify-center col-span-3 px-2 py-1 bg-white border-t">
                <input wire:model="subItems.{{ $index }}.description" type="text" class="w-full py-1 border-t-0 border-b border-l-0 border-r-0 focus:outline-0 focus:border-button-blue border-button-blue">
				@error("subItems.$index.description") <p class="text-red-500">{{ $message }}</p> @enderror
              </div>
              <div class="flex items-start px-2 py-1 bg-white border-t border-r border-gray-300">
                <p class="text-gray-500">Attach Image</p>
              </div>
              <div class="flex flex-col items-start justify-center col-span-3 px-2 py-1 bg-white border-t">
                <input accept=".jpg,.jpeg,.png,.svg" wire:model="subItems.{{ $index }}.file" type="file" class="w-full py-1 border-t-0 border-b border-l-0 border-r-0 focus:outline-0 focus:border-button-blue border-button-blue">
				@error("subItems.$index.file") <p class="text-red-500">{{ $message }}</p> @enderror
				<div class="w-full h-48">
					@if (($subItem['file'] && in_array($subItem['file']->extension(), ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'])) ?? $subItem['link'])
					<img src="{{ $subItem['file']->temporaryUrl() ?? $subItem['link'] }}" class="max-w-full mb-5 shadow max-h-48" alt="Attached Subitem Image">
					@else
					<p class="mt-3 text-sm text-gray-400">No image selected...</p>
					@endif
				</div>
              </div>
              <div class="flex items-center px-2 py-1 bg-white border-t border-r border-gray-300">
                <p class="text-gray-500">Action</p>
              </div>
              <div class="flex items-center col-span-3 px-2 py-1 bg-white border-t">
                <button type="button" wire:click="removeSubItem('{{ $index }}')" class="px-2 py-1 text-white bg-red-600 rounded-md hover:opacity-50">Delete</button>
              </div>
            </div>

          @endforeach
        </div>
        <button wire:click="addSubItem" type="button" class="px-2 py-1 rounded-md bg-button-blue hover:opacity-50">Add Item</button>
      </div>
      <hr class="mt-10">
      <button 
        class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
        type="Submit" 
        >
        Submit
      </button>      
    </form>
  </div>
</div>

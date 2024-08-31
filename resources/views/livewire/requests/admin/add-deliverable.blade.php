<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {
  use WithFileUploads;

  public $client;
  public $role;
  public $photo;
  public $img_path = '';
  #[Validate('required')] 
  public $title = '';
  #[Validate('required')] 
  public $date_need = '';
  #[Validate('required')] 
  public $status = '';
  public $remarks = '';

  public function mount(User $client) {
    $this->client = $client;
  }

  public function handleSave()
    {
        $this->validate();

        $file = $this->photo;
        $clientRequest = ClientRequest::create([
            'title' => $this->title,
            'status' => $this->status,
            'user_id' => $this->client->id,
            'needed_at' => $this->date_need,
            'remarks' => $this->remarks,
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
}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
  <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold lg:text-7xl">Client Deliverable</h1>
      <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
  </div>
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
  <div class="py-12">
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
        <button 
          class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
          type="Submit" 
        >
        Submit
      </button>      
    </form>
  </div>
</div>

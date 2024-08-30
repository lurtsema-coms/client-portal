<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithFileUploads;


new class extends Component {
  use WithFileUploads;

  public $client;
  public $clientRequest;
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

  public function mount(User $client, ClientRequest $clientRequest) {
    $this->client = $client;
    $this->clientRequest = $clientRequest;
    $this->title = $clientRequest->title;
    $this->date_need = $clientRequest->needed_at;
    $this->status = $clientRequest->status;
    $this->remarks = $clientRequest->remarks;
    $this->img_path = $clientRequest->img_path;
  }

  public function handleSave()
    {
        $this->validate();

        $image = $this->photo;
        
        if ($image) {    
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $file_name = $uuid . '.' . $image->getClientOriginalExtension();
            $this->img_path = url('images/client-request/' . $file_name);
            $image->storePubliclyAs('images/client-request', $file_name, 'public');

            $this->clientRequest->update([
                'img_path' => $this->img_path
            ]);
        }

        $user_id = $this->clientRequest->update([
            'title' => $this->title,
            'status' => $this->status,
            'updated_by' => auth()->user()->id,
            'needed_at' => $this->date_need,
            'remarks' => $this->remarks,
        ]);

        $this->reset(['title', 'date_need', 'remarks']);
        
        session()->flash('success', 'Request successfully updated.');
        $this->redirect(url()->previous(), navigate: true);
    }
}; ?>

<div class="py-12">
	<form action="" wire:submit="handleSave">
		<div class="mx-auto space-y-6">
			<div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
			<h1 class="font-bold text-black lg:text-3xl">Deliverable Form</h1>
			<div class="grid sm:grid-cols-2 sm:gap-x-8">
				<div class="mt-5 space-y-2">
					<label for="" class="block tracking-wider text-gray-600">Created By</label>
					<input 
						class="w-full text-black rounded-lg"
						type="text"
						value="{{ $client->name }}"
						readonly
					>
				</div>
				<div class="mt-5 space-y-2">
					<label for="" class="block tracking-wider text-gray-600">Created At</label>
					<input 
						class="w-full text-black rounded-lg"
						type="text"
						value="{{ (new DateTime($clientRequest->created_at))->format('D, F j, Y h:i a') }}"
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
					@if ($photo) 
					<img src="{{ $photo->temporaryUrl() }}" class="mb-5 rounded-lg shadow-md max-w-48">
					@elseif($img_path)
						<img src="{{ $img_path }}?{{ now()->timestamp }}" class="mb-5 rounded-lg shadow-md max-w-48">
					@endif
				<label for="" class="block tracking-wider text-gray-600">Upload Photo</label>
				<input 
					class="w-full max-w-lg"
					type="file"
					wire:model="photo"
				>
				@error('photo')<p class="text-red-500">{{ $message }}</p>@enderror
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
			</div>
			<button 
				class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
				type="Submit" 
			>
			Submit
		</button>      
	</form>
</div>
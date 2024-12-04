<?php

use App\Models\ClientRequest;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;


new class extends Component {
	use WithFileUploads;

	public $client;
	public $clientRequest;
	public $role;
	public $photo;
	public $img_path = '';
	public $title = '';
	public $date_need = '';
	public $status = '';
	public $remarks = '';
  public $subItems = [];

	public function mount(User $client, ClientRequest $clientRequest) {
		$this->client = $client;
		$this->clientRequest = $clientRequest;
		$this->title = $clientRequest->title;
		$this->date_need = $clientRequest->needed_at;
		$this->status = $clientRequest->status;
		$this->remarks = $clientRequest->remarks;
		$this->img_path = $clientRequest->img_path;
		$this->subItems = json_decode($clientRequest->subitems, true) ?? [];
	}

	public function addSubItem() {
		$this->subItems[] = [
			'title' => '',
			'description' => '',
			'link' => '',
			'file' => null,
		];
	}

	public function handleSave()
    {
        $this->validate();

        $file = $this->photo;
		$subItems = [];
		foreach ($this->subItems as $subItem) {
			$subItemFile = isset($subItem['file']) ? $subItem['file'] : null;
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
        
        if ($file) {    
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $file_name = $uuid . '.' . $file->getClientOriginalExtension();
            $this->img_path = url('images/client-request/' . $file_name);
            $file->storePubliclyAs('images/client-request', $file_name, 'public');

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
			'subitems' => sizeof($subItems) ? json_encode($subItems) : null,
            'updated_by' => auth()->user()->id,
        ]);

        session()->flash('success', 'Data has been updated.');
        $this->redirect(route('clients.view-client', $this->client->id), navigate: true);
    }

	public function handleDelete() {
		$this->authorize('delete', $this->clientRequest);
		$this->clientRequest->delete();

		session()->flash('status', 'Request Successfully Deleted');
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

<div class="pb-12">
	<form action="" wire:submit="handleSave">
		<div class="mx-auto space-y-6">
			<div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
			<h1 class="font-bold text-black lg:text-3xl">Deliverable Form</h1>
			<div class="grid sm:grid-cols-2 sm:gap-x-8">
				<div class="mt-5 space-y-2">
					<label for="" class="block tracking-wider text-gray-600">Client</label>
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
				@if ($img_path)
					@if (in_array($extension, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg']))
						<img src="{{ $img_path }}?{{ now()->timestamp }}" class="mb-5 rounded-lg shadow-md max-w-48">
					@elseif ($extension === 'pdf')
						<a href="{{ $img_path }}" target="_blank" class="block text-blue-500 underline hover:opacity-70">View PDF</a>
					@endif
				@endif
				@error('photo')<p class="text-red-500">{{ $message }}</p>@enderror
			</div>
		</div>
		<div class="w-full mt-5 space-y-2" wire:ignore>
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
					<p class="text-gray-500">Attach/Replace Image</p>
				  </div>
				  <div class="flex flex-col items-start justify-center col-span-3 px-2 py-1 bg-white border-t">
					<input accept=".jpg,.jpeg,.png,.svg" wire:model="subItems.{{ $index }}.file" type="file" class="w-full py-1 border-t-0 border-b border-l-0 border-r-0 focus:outline-0 focus:border-button-blue border-button-blue">
					@error("subItems.$index.file") <p class="text-red-500">{{ $message }}</p> @enderror
					<div class="w-full h-48">
						@if (isset($subItem['file']) && in_array($subItem['file']->extension(), ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg']))
						<img src="{{ $subItem['file']->temporaryUrl() }}" class="max-w-full mb-5 shadow max-h-48" alt="Attached Subitem Image">
						@elseif ($subItem['link'])
						<img src="{{ $subItem['link'] }}" class="max-w-full mb-5 shadow max-h-48" alt="Attached Subitem Image">
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

@script
<script>
    initFlowbite();
</script>
@endscript
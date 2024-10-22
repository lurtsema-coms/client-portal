<?php

use App\Models\User;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    use WithFileUploads;
    public $client;

    
    public function mount(User $client) {
        $this->client = $client;
    }

}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
  <x-header-title headingTitle="Client Invoices" />
  <div class="mt-10">
    <a href="{{ route('invoices.add-invoice', $client->id) }}" wire:navigate>
        <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">Add</button>
    </a>
    <div class="w-full">

    </div>
  </div>
</div>

</div>

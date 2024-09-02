<?php

use App\Models\ClientRequest;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {

  public $client;
  public $clientRequest;

  public function mount(User $client, ClientRequest $clientRequest) {
    $this->authorize('view', $clientRequest);
    $this->client = $client;
    $this->clientRequest = $clientRequest;
  }
}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
  <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold lg:text-7xl">Client Request</h1>
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
  @livewire('requests.admin.edit-request', ['client' => $client, 'clientRequest' => $clientRequest])
</div>
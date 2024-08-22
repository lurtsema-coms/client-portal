<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] 
class extends Component {
    public $client;

    public function mount(User $client) {
        $this->client = $client;
    }
}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold lg:text-7xl">{{ $client->name }}</h1>
        <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
    </div>
</div>

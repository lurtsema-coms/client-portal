<?php

use App\Models\User;
use Livewire\Volt\Component;
use App\Models\MoreInfoValue;
use Livewire\Attributes\Layout;


new #[Layout('layouts.admin')] 
class extends Component {
    public $client;
    public $assets;
    public $socials;
    public $paidInvoice;
    public $unpaidInvoice;
    public $accountManager;

    public function mount($id) {
        $client = User::find($id);
        $this->client = $client;
        $this->paidInvoice = $client->invoice()->byStatus('PAID')->get();
        $this->unpaidInvoice = $client->invoice()->byStatus('UNPAID')->get();
        $this->assets = json_decode($client->assets, true) ?? [];
        $this->socials = json_decode($client->socials, true) ?? [];
        $this->accountManager = json_decode( $client->account_manager) ?? [];
    }
}; ?>

<div class="flex flex-col items-center w-full">
    <div class="w-full">
        <button onclick="history.back()" class="my-5 text-lg border border-t-0 border-l-0 border-r-0 place-self-start border-b-button-blue text-button-blue hover:opacity-50">← Back </button>
    </div>
    @livewire('client-info', ['client' => $client])
</div>

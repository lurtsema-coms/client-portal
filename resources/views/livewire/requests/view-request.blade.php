<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    public $title = '';
    public $livewire_content = '';
    
    public function mount()
    {
        $url_segment = request()->segment(1);

        if($url_segment == 'create-request') {
            $this->title = 'Create Request';
            $this->livewire_content = 'requests.add-request';
        } elseif($url_segment == 'edit-users'){
            $this->title = 'Edit users';
            $this->livewire_content = 'users.edit-users';
        }
    }
}; ?>

<div class="flex flex-col items-stretch justify-start gap-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold lg:text-7xl">{{ $title }}</h1>
        <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
    </div>
    @livewire($livewire_content)
</div>  

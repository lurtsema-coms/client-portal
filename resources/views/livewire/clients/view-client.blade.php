<?php

use App\Models\User;
use App\Models\MoreInfoValue;
use App\Models\ClientRequest;
use App\Models\PersonInContact;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')]
class extends Component {

    use WithPagination;
    
    #[Url]
    public $search;
    public $person_in_contact;
    public $client;

    public function mount(User $client)
    {
        $this->client = $client;
        $user = User::with('personInContact')->find($client->id);
        $this->person_in_contact = $user->personInContact;
    }

    public function with(): array 
    {
        $personInContacts = PersonInContact::where('user_id', $this->client->id);
        $userRequests = ClientRequest::with('user')
            ->where('user_id', $this->client->id)
            ->where('status', 'PENDING')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('remarks', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("DATE_FORMAT(needed_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->paginate(5, pageName: 'user-requests-page');
        $deliverables = ClientRequest::with('user', 'updatedBy')
            ->where('user_id', $this->client->id)
            ->whereNotIn('status', ['PENDING', 'COMPLETED'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhere('remarks', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("DATE_FORMAT(created_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%'])
                        ->orWhereRaw("DATE_FORMAT(needed_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->paginate(5, pageName: 'deliverables-page');
        $completed = ClientRequest::with('user', 'updatedBy')
            ->where('user_id', $this->client->id)
            ->where('status', 'COMPLETED')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhere('remarks', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("DATE_FORMAT(created_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%'])
                        ->orWhereRaw("DATE_FORMAT(needed_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->paginate(5, pageName: 'completed-page');

        $moreInfo = MoreInfoValue::where('user_id', $this->client->id)->get();
        
        return [
            'personInContacts' => (clone $personInContacts)->get(),
            'personInContact' => (clone $personInContacts)->first(),
            'userRequests' => $userRequests,
            'deliverables' => $deliverables,
            'completed' => $completed,
            'moreInfo' => $moreInfo,
        ];
    }
}; ?>

<div class="flex flex-col items-center justify-center w-full mx-auto">
    <div class="flex flex-col items-stretch justify-center max-w-screen-xl gap-10 lg:flex-row">
        <div class="flex flex-col items-center justify-center flex-grow max-w-screen-md gap-10 px-8 py-8 border sm:flex-row rounded-3xl">
            <div class="relative flex items-center justify-center w-full p-3 overflow-hidden bg-white border-2 rounded-lg max-w-64 border-whbg-white sm:p-5 aspect-square">
                <img class="object-center max-w-full max-h-full" src="{{ $client->img_path ?? asset('images/user.png') }}" alt="">
            </div>
            <div class="flex flex-col justify-center ">
                <h1 class="mb-2 text-4xl font-bold ">{{ $client->name }}</h1>
                <hr class="my-3">
                <h2 class="text-lg">Email: <span class="text-gray-500">{{ $client->email }}</span></h2>
                <h2 class="text-lg">Company Cell Number: <span class="text-gray-500">{{ $client->company_cell_number }}</span></h2>
                <h2 class="text-lg">Company Address: <span class="text-gray-500">{{ $client->company_address }}</span></h2>
                <h2 class="text-lg">Project Manager: <span class="text-gray-500">{{ $client->project_manager }}</span></h2>
                @if ($moreInfo->count() > 0)
                <div 
                    x-data="{isOpen: false}"
                    x-init="$watch('isOpen', value => document.body.style.overflow = value ? 'hidden' : 'auto')"
                >
                    <button 
                        class="py-1 mt-3 text-black transition-all duration-300 ease-in-out bg-white text-lgtracking-wide w-28 rounded-xl hover:opacity-60"
                        @click="isOpen=true"
                    >
                        View More
                    </button>
                
                    <div x-show="isOpen" 
                        class="fixed inset-0 z-50 flex bg-black bg-opacity-75"
                        x-cloak
                    >
                        <!-- Close Button -->
                        <button @click="isOpen=false" 
                                class="absolute text-xl font-bold text-white top-5 right-5">
                            &times;
                        </button>
                        
                        <!-- Zoomed Image -->
                        <div class="flex w-full p-5">
                            <div 
                                class="w-full max-w-4xl p-10 m-auto text-black bg-white shadow-lg rounded-2xl"
                                @click.outside="isOpen=false"
                            >
                                <div class="space-y-5 overflow-auto max-h-96">
                                    <h1 class="mb-4 font-bold lg:text-xl">More Info</h1>
                                    @foreach ($moreInfo as $info)
                                    <hr>
                                    <div class="">
                                        <p class="font-bold">{{ $info->moreInfo->label }}</p>
                                        <p class="">{{ $info->text_value ?? $info->paragraph_value ?? date('D, F j, Y', strtotime($info->date_value))  }}</p>

                                    </div>
                                    @endforeach
                                            
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
                @endif
            </div>
        </div>
        <div class="flex flex-col justify-center">
            <h1 class="mb-2 text-4xl font-bold ">Person in Contact</h1>
            <h2 class="text-lg">Name: <span class="text-gray-500">{{ $personInContact?->name }}</span></h2>
            <h2 class="text-lg">Email Address: <span class="text-gray-500">{{ $personInContact?->email }}</span></h2>
            <h2 class="mb-6 text-lg">Cell Number: <span class="text-gray-500">{{ $personInContact?->cell_number }}</span></h2>
            @if ($personInContacts->count() > 1)
                <div 
                    x-data="{isOpen: false}"
                    x-init="$watch('isOpen', value => document.body.style.overflow = value ? 'hidden' : 'auto')"
                >
                    <button 
                        class="py-1 text-black transition-all duration-300 ease-in-out bg-white text-lgtracking-wide w-28 rounded-xl hover:opacity-60"
                        @click="isOpen=true"
                    >
                        View More
                    </button>
                
                    <div x-show="isOpen" 
                        class="fixed inset-0 z-50 flex bg-black bg-opacity-75"
                        x-cloak
                    >
                        <!-- Close Button -->
                        <button @click="isOpen=false" 
                                class="absolute text-xl font-bold text-white top-5 right-5">
                            &times;
                        </button>
                        
                        <!-- Zoomed Image -->
                        <div class="flex w-full p-5">
                            <div 
                                class="w-full max-w-4xl p-10 m-auto text-black bg-white shadow-lg rounded-2xl"
                                @click.outside="isOpen=false"
                            >
                                <div class="space-y-5 overflow-auto max-h-96">
                                    @if ($person_in_contact->count() > 1)
                                        @foreach ($person_in_contact as $index => $contact)
                                            <div wire:key="contact-users-{{ $index }}">
                                                <h1 class="mb-4 font-bold lg:text-xl">Person in Contact {{ $index !== 0 ? $index : '' }}</h1>
                                                <div>
                                                    <p>
                                                        <span class="text-gray-600">Name:</span> {{ $contact->name }}
                                                    </p>
                                                    <p>
                                                        <span class="text-gray-600">Cell Number:</span> {{ $contact->cell_number }}
                                                    </p>
                                                    <p>
                                                        <span class="text-gray-600">Email:</span> {{ $contact->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            @endif
        </div>
    </div>
    <div class="bg-custom-gradient w-full h-[2px] -z-10 my-10"></div>
    <div class="flex flex-col w-full gap-5 lg:items-center lg:justify-between lg:flex-row">
        <div class="flex flex-col-reverse flex-1 gap-5 shrink-0 lg:flex-row">
            <div class="relative w-full max-w-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.5 2a6.5 6.5 0 104.5 11.29l3.85 3.85a.75.75 0 001.06-1.06l-3.85-3.85A6.5 6.5 0 008.5 2zm-5 6.5a5 5 0 1110 0 5 5 0 01-10 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input 
                    type="search" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Search..." 
                    class="w-full max-w-sm pl-10 text-black rounded-lg h-11">
            </div>
            <a href="{{ route('requests.add-deliverable', $client->id) }}" class="max-w-60" wire:navigate>
                <div class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer h-11 bg-button-blue hover:opacity-60">
                    Add Deliverable
                </div>
            </a>
        </div>
        @if ($client->url_sharepoint)
        <a href="{{ $client->url_sharepoint }}" class="" target="_blank">
            <div class="inline-block px-4 py-1 text-sm font-bold border rounded-full border-sky-600 hover:opacity-70">
                DOWNLOAD FILES HERE
            </div>
        </a>
        @endif
    </div>
    @if (session('status') ?? session('success'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 6000)" 
            x-show="show"
            class="mt-10 text-green-400"
        >
            {{ session('status') ?? session('success') }}
        </div>
    @endif
    
    
    <div class="w-full p-3 mt-10 mb-16 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Client Requests</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Deliverable Request</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 xl:table-cell whitespace-nowrap">Requested At</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">As Needed By</th>
                    <th class="pl-6 font-thin text-left text-gray-500 whitespace-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($userRequests as $request)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $request->title }}</p>
                            <p class="italic text-gray-700 md:hidden">{{ $request->user->name }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y', strtotime($request->needed_at)) }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($request->created_at)) }}</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($request->needed_at)) }}</td>
                        <td class="py-5 pl-6 rounded-r-lg">
                            <a href="{{ route('requests.view-request', ['client' => $request->user->id, 'clientRequest' => $request->id]) }}" wire:navigate class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($userRequests->isEmpty())
        <p class="mt-5 text-center text-gray-500">No data.</p>
        @endif
        <div class="mt-5">
            {{ $userRequests->links() }}
        </div>
    </div>
    <div class="w-full p-3 mb-16 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Deliverables</h1>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Title</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Status</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 xl:table-cell whitespace-nowrap">Last Update</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 xl:table-cell whitespace-nowrap">Updated By</th>
                    <th class="pl-6 font-thin text-left text-gray-500 whitespace-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deliverables as $deliverable)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $deliverable->title }}</p>
                            <p class="italic text-gray-700 md:hidden">Client Name A</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $deliverable->status }}</td>
                        <td class="hidden px-6 py-5 xl:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($deliverable->updated_at)) }}</td>
                        <td class="hidden px-6 py-5 xl:table-cell">{{ $deliverable->updatedBy?->name }}</td>
                        <td class="py-5 pl-6 rounded-r-lg">
                            <a href="{{ route('requests.view-deliverable', ['client' => $deliverable->user->id, 'clientRequest' => $deliverable->id]) }}" wire:navigate class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($deliverables->isEmpty())
        <p class="mt-5 text-center text-gray-500">No data.</p>
        @endif
        <div class="mt-5">
            {{ $deliverables->links() }}
        </div>
    </div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <div class="flex flex-col gap-4 md:items-center md:flex-row md:justify-between">
            <h1 class="font-bold lg:text-3xl">Completed</h1>
            @if ($client->url_sharepoint)
            <a href="{{ $client->url_sharepoint }}" class="" target="_blank">
                <div class="inline-block px-4 py-1 text-sm font-bold text-black border rounded-full border-sky-600 hover:opacity-70">
                    DOWNLOAD FILES HERE
                </div>
            </a>
            @endif
        </div>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Title</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Status</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 xl:table-cell whitespace-nowrap">Last Update</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 xl:table-cell whitespace-nowrap">Updated By</th>
                    <th class="pl-6 font-thin text-left text-gray-500 whitespace-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completed as $complete)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $complete->title }}</p>
                            <p class="italic text-gray-700 md:hidden">Client Name A</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $complete->status }}</td>
                        <td class="hidden px-6 py-5 xl:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($complete->updated_at)) }}</td>
                        <td class="hidden px-6 py-5 xl:table-cell">{{ $complete->updatedBy?->name }}</td>
                        <td class="py-5 pl-6 rounded-r-lg">
                            <a href="{{ route('requests.view-deliverable', ['client' => $complete->user->id, 'clientRequest' => $complete->id]) }}" wire:navigate class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($completed->isEmpty())
        <p class="mt-5 text-center text-gray-500">No data.</p>
        @endif
        <div class="mt-5">
            {{ $completed->links() }}
        </div>
    </div>

</div>

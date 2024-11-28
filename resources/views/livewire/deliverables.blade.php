<?php

use App\Models\User;
use App\Models\MoreInfoValue;
use App\Models\ClientRequest;
use App\Models\PersonInContact;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new class extends Component {

    use WithPagination;
    
    #[Url]
    public $search;
    public $person_in_contact;

    public function mount()
    {
        $user = User::with('personInContact')->find(auth()->user()->id);
        $this->person_in_contact = $user->personInContact;
    }

    public function with(): array 
    {
        $userRequests = ClientRequest::with('user')
            ->where('user_id', auth()->user()->id)
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
            ->where('user_id', auth()->user()->id)
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
            ->where('user_id', auth()->user()->id)
            ->where('status', 'COMPLETED')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("DATE_FORMAT(needed_at, '%a, %M %e, %Y') LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->paginate(5, pageName: 'completed-page');

        $moreInfo = MoreInfoValue::where('user_id', auth()->user()->id)->get();
        
        return [
            'userRequests' => $userRequests,
            'deliverables' => $deliverables,
            'completed' => $completed,
        ];
    }
}; ?>

<div class="flex flex-col items-center justify-center w-full mx-auto">
    <div class="flex flex-col w-full gap-5 lg:items-center lg:justify-between lg:flex-row">
        <div class="flex flex-col-reverse flex-1 gap-5 shrink-0 lg:flex-row">
            <a href="{{ route('add-request') }}" class="max-w-60" wire:navigate>
                <div class="flex items-center justify-center px-5 py-1 font-bold text-center text-black transition-all duration-300 ease-in-out rounded-md cursor-pointer h-11 bg-button-blue hover:opacity-60">
                    Add New Request
                </div>
            </a>
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
        </div>
        @if (auth()->user()->url_sharepoint)
        <a href="{{ auth()->user()->url_sharepoint }}" class="" target="_blank">
            <div class="inline-block px-4 py-1 text-sm font-bold border rounded-full border-sky-600 hover:opacity-70">
                DOWNLOAD YOUR FILES HERE
            </div>
        </a>
        @endif
    </div>
    @if (session('status'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 6000)" 
            x-show="show"
            class="mt-10 text-green-400"
        >
            {{ session('status') }}
        </div>
    @endif
    
    
    <div class="w-full p-3 mt-10 mb-16 overflow-auto text-black bg-white rounded-lg lg:p-6">
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
                        <td class="hidden px-6 py-5 xl:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($request->created_at)) }}</td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($request->needed_at)) }}</td>
                        <td class="py-5 pl-6 rounded-r-lg">
                            <a href="{{ route('edit-request', $request->id) }}" wire:navigate>
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                            </a>
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
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
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
                            <p class="italic text-gray-700 md:hidden">{{ $deliverable->user->name }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ $deliverable->status }}</td>
                        <td class="hidden px-6 py-5 xl:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($deliverable->updated_at)) }}</td>
                        <td class="hidden px-6 py-5 xl:table-cell">{{ $deliverable->updatedBy->name }}</td>
                        <td class="py-5 pl-6 rounded-r-lg">
                            <a href="{{ route('view-deliverables', $deliverable->id) }}" wire:navigate>
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                            </a>
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
    <div class="w-full p-3 mt-16 text-black bg-white rounded-lg lg:p-6">
        <div class="flex flex-col gap-4 md:items-center md:flex-row md:justify-between">
            <h1 class="font-bold lg:text-3xl">Completed</h1>
            @if (auth()->user()->url_sharepoint)
            <a href="{{ auth()->user()->url_sharepoint }}" class="" target="_blank">
                <div class="inline-block px-4 py-1 text-sm font-bold text-black border rounded-full border-sky-600 hover:opacity-70">
                    DOWNLOAD YOUR FILES HERE
                </div>
            </a>
            @endif
        </div>
        <table class="w-full mt-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 font-thin text-left text-gray-500 whitespace-nowrap">Deliverable Request</th>
                    <th class="hidden px-6 font-thin text-left text-gray-500 sm:table-cell whitespace-nowrap">Due Date</th>
                    <th class="pl-6 font-thin text-left text-gray-500 whitespace-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completed as $complete)
                    <tr class="border-b">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $complete->title }}</p>
                            <p class="italic text-gray-700 md:hidden">{{ $complete->user->name }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ date('D, F j, Y') }}</p>
                        </td>
                        <td class="hidden px-6 py-5 sm:table-cell whitespace-nowrap">{{ date('D, F j, Y', strtotime($complete->needed_at)) }}</td>
                        <td class="py-5 pl-6 rounded-r-lg">
                            <a href="{{ route('view-completed', $complete->id) }}" wire:navigate>
                                <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                            </a>
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

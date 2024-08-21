<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    

    public function with() : array
    {
        return [
            'users' => $this->loadUsers()
        ];
    }

    public function loadUsers()
    {
        $query = User::paginate(5);

        return $query;
    }
}; ?>

<div class="flex flex-col w-full">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <a href="{{ route('add-users') }}" wire:navigate>
            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">Add</button>
        </a>
        <input type="search" wire:model="search" placeholder="Search..." class="w-full text-black rounded-lg max-w-96">
    </div>
    <div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
        <h1 class="font-bold lg:text-3xl">Users</h1>
        <table class="w-full my-5 border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="font-thin text-left text-gray-500">Name</th>
                    <th class="hidden font-thin text-left text-gray-500 md:table-cell">Email</th>
                    <th class="hidden font-thin text-left text-gray-500 sm:table-cell">Role</th>
                    <th class="hidden font-thin text-left text-gray-500 xl:table-cell">Created At</th>
                    <th class="font-thin text-left text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    use Carbon\Carbon;
                @endphp
                @foreach($users as $user)
                    <tr class="border-b"wire:key="user-id{{ $user->id }}">
                        <td class="px-3 py-5">
                            <p class="font-bold">{{ $user->name }}</p>
                            <p class="italic text-gray-700 md:hidden">{{ $user->email }}</p>
                            <p class="text-sm text-gray-500 sm:hidden">{{ $user->role }}</p>
                        </td>
                        <td class="hidden md:table-cell">{{ $user->email }}</td>
                        <td class="hidden xl:table-cell">{{ $user->role }}</td>
                        <td class="hidden sm:table-cell">{{ Carbon::parse($user->created_at)->format('D, F j, Y')}}</td>
                        <td class="rounded-r-lg">
                            <button class="px-5 py-1 font-bold text-black transition-all duration-300 ease-in-out rounded-md bg-button-blue hover:opacity-60">View</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>

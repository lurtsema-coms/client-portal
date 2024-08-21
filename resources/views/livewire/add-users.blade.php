<?php

use App\Models\User;
use App\Models\PersonInContact;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new class extends Component {
    
    public $name = '';
    public $company_cell = '';
    public $company_address = '';
    public $email = '';
    public $role = '';
    public $project_manager = '';
    public $password = '';
    public $person_in_contact = [];

    public function mount()
    {
        $this->person_in_contact = [
            [
                'name' => '',
                'cell_number' => '',
                'email_address' => '',
            ]
        ];

    }

    public function addUser()
    {
        $this->validate();

        $user_id = User::insertGetId([
            'name' => $this->name,
            'role' => $this->role,
            'email' => $this->email,
            'company_cell_number' => $this->company_cell,
            'company_address' => $this->company_address,
            'password' => Hash::make($this->password),
            'created_by' => auth()->user()->id,
        ]);
        
        if($this->role == 'client'){            
            foreach($this->person_in_contact as $person) {
                PersonInContact::create([
                    'name' => $person['name'],
                    'user_id' => $user_id,
                    'cell_number' => $person['cell_number'],
                    'email' => $person['email_address'],
                ]);
            }
        }

        $this->reset(['name', 'role', 'email', 'company_cell', 'company_address', 'person_in_contact', 'password', 'project_manager']);
        return $this->redirect('/users', navigate: true);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|min:3',
            'role' => 'required|in:admin,client',
            'company_cell' => 'required|min:3',
            'company_address' => 'required|min:3',
            'email' => 'required|email',
            'project_manager' => 'required|min:3',
            'password' => 'required|min:4',
        ];

        if ($this->role === 'client') {
            $rules = array_merge($rules, [
                'person_in_contact.*.name' => 'required|min:3',
                'person_in_contact.*.cell_number' => 'required|min:3',
                'person_in_contact.*.email_address' => 'required|email',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'person_in_contact.*.name.required' => 'The name field is required for each person in contact.',
            'person_in_contact.*.name.min' => 'The name must be at least :min characters long.',
            'person_in_contact.*.cell_number.required' => 'The cell number field is required for each person in contact.',
            'person_in_contact.*.cell_number.min' => 'The cell number must be at least :min characters long.',
            'person_in_contact.*.email_address.required' => 'The email address field is required for each person in contact.',
            'person_in_contact.*.email_address.email' => 'Each email address must be a valid email address.',
        ];
    }

    public function addMorePersonContact()
    {
        $this->person_in_contact[] = [
            'name' => '',
            'cell_number' => '',
            'email_address' => '',
        ];
    }

    public function deleteMorePersonContact($index)
    {
        unset($this->person_in_contact[$index]);
        $this->person_in_contact = array_values($this->person_in_contact);
    }
}; ?>

<div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
    <form action="" wire:submit="addUser">
        <h1 class="font-bold lg:text-3xl">Personal Information</h1>
        <div class="grid sm:grid-cols-2 sm:gap-x-8">
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Name</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="name"
                >
                @error('name') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Company Cell Number</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="company_cell"
                >
                @error('company_cell') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Email Address</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="email"
                >
                @error('email') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Project Manager</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="project_manager"
                >
                @error('project_manager')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Role</label>
                <select 
                    class="w-full text-black rounded-lg"
                    wire:model.change="role"
                >
                    <option value="" disabled>Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="client">Client</option>
                </select>
                @error('role')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Company Address</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="company_address"
                >
                @error('company_address')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Password</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="password"
                    wire:model="password"
                >
                @error('password')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
        @if($role == 'client')
            @foreach ($person_in_contact as $index => $contact)
                <div wire:key="add-users-{{ $index }}">
                    <h1 class="mt-10 font-bold lg:text-3xl">Person in Contact {{ $index !== 0 ? $index : '' }}</h1>
                    <div class="mt-5 space-y-2">
                        <label for="" class="block tracking-wider text-gray-600">Name</label>
                        <input 
                            class="w-full max-w-lg text-black rounded-lg"
                            type="text"
                            wire:model="person_in_contact.{{ $index }}.name"
                        >
                        @error("person_in_contact.$index.name") <p class="text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-5 space-y-2">
                        <label for="" class="block tracking-wider text-gray-600">Cell Number</label>
                        <input 
                            class="w-full max-w-lg text-black rounded-lg"
                            type="text"
                            wire:model="person_in_contact.{{ $index }}.cell_number"
                        >
                        @error("person_in_contact.$index.cell_number") <p class="text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-5 space-y-2">
                        <label for="" class="block tracking-wider text-gray-600">Email Address</label>
                        <input 
                            class="w-full max-w-lg text-black rounded-lg"
                            type="text"
                            wire:model="person_in_contact.{{ $index }}.email_address"
                        >
                        @error("person_in_contact.$index.email_address") <p class="text-red-500">{{ $message }}</p> @enderror
                    </div>
                    @if ($index !== 0)
                        <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="deleteMorePersonContact({{ $index }})"
                        >
                            Delete
                        </button>
                    @endif
                </div>
            @endforeach
            <button 
                class="block px-2 py-1 mt-5 text-white bg-gray-500 border rounded-lg hover:bg-gray-600"
                type="button" 
                wire:click="addMorePersonContact"
            >
                Add more person in contact
            </button>
        @endif
        <button 
            class="float-right px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
            type="Submit" 
        >
            Submit
        </button>
    </form>
</div>

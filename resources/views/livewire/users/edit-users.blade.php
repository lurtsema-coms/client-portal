<?php

use App\Models\User;
use App\Models\PersonInContact;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    use WithFileUploads;
    
    public $user;
    public $name = '';
    public $company_cell = '';
    public $company_address = '';
    public $url_sharepoint = '';
    public $email = '';
    public $role = '';
    public $client_type = '';
    public $project_manager = '';
    public $password = '';
    public $img_path = '';
    public $photo;
    public $person_in_contact = [];

    public function mount()
    {
        $user_id = request()->route('id');
        
        $user = User::with('personInContact')->find($user_id);
        $this->user = $user;
        
        $this->name = $user->name;
        $this->company_cell = $user->company_cell_number;
        $this->company_address = $user->company_address;
        $this->url_sharepoint = $user->url_sharepoint;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->client_type = $user->client_type;
        $this->project_manager = $user->project_manager;
        $this->img_path = $user->img_path;
        
        if (!$user->personInContact->isEmpty()) {

            $contacts = $user->personInContact->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'cell_number' => $contact->cell_number,
                    'email' => $contact->email,
                ];
            });

            $this->person_in_contact = $contacts->toArray();
        }

    }

    public function editUser()
    {
        $validatedData = $this->validate();
        
        $image = $this->photo;
        $img_path = '';
        
        if($image){    
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $file_name = $uuid . '.' . $image->getClientOriginalExtension();
            $img_path = url('images/user-logo/' . $file_name);
            $image->storePubliclyAs('images/user-logo', $file_name, 'public');

            $this->user->update([
                'img_path' => $img_path
            ]);
        }

        $user = $this->user->update([
            'name' => $this->name,
            'role' => $this->role,
            'email' => $this->email,
            'company_cell_number' => $this->company_cell === '' ? null : $this->company_cell,
            'company_address' => $this->company_address === '' ? null : $this->company_address,
            'project_manager' => $this->project_manager === '' ? null : $this->project_manager,
            'client_type' => $this->client_type === '' ? null : $this->client_type,
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if($this->password != ''){
            $user->update([
                'password' => Hash::make($this->password),
            ]);
        }
        
        if($this->role == 'client'){                      
            foreach($this->person_in_contact as $person) {
                PersonInContact::updateOrCreate(
                    ['id' => $person['id']],
                    [
                        'name' => $person['name'],
                        'user_id' => $this->user->id,
                        'cell_number' => $person['cell_number'],
                        'email' => $person['email'],
                    ]
                );
            }
        }

        session()->flash('status', 'User Successfully updated');
        $this->redirect('/users', navigate: true);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|min:3',
            'role' => 'required|in:admin,client',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'password' => 'nullable|min:4',
        ];

        if ($this->role === 'client') {
            $rules = array_merge($rules, [
                'photo' => 'nullable|image|max:1024',
                'company_cell' => 'required|min:3',
                'company_address' => 'required|min:3',
                'url_sharepoint' => 'required|url',
                'project_manager' => 'required|min:3',
                'client_type' => 'required|in:business,political',
                'person_in_contact.*.name' => 'required|min:3',
                'person_in_contact.*.cell_number' => 'required|min:3',
                'person_in_contact.*.email' => 'required|email',
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
            'person_in_contact.*.email.required' => 'The email address field is required for each person in contact.',
            'person_in_contact.*.email' => 'Each email address must be a valid email address.',
        ];
    }

    public function addPersonInContact()
    {
        $this->person_in_contact[] = [
            'id' => '',
            'name' => '',
            'cell_number' => '',
            'email' => '',
        ];
    }

    public function removePersonInContact($index)
    {
        $person_in_contact = PersonInContact::find($this->person_in_contact[$index]['id']);

        if($person_in_contact){
            $person_in_contact->delete();
        }
        
        unset($this->person_in_contact[$index]);
        $this->person_in_contact = array_values($this->person_in_contact);
    }
}; ?>

<div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
    <form action="" wire:submit="editUser">
        <h1 class="font-bold lg:text-3xl">Personal Information</h1>
        <div class="grid sm:grid-cols-2 sm:gap-x-8">
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
                <label for="" class="block tracking-wider text-gray-600">Name</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="name"
                >
                @error('name') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            @if($role === 'client')
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Company Cell Number</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="company_cell"
                >
                @error('company_cell') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            @endif
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Email Address</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="email"
                >
                @error('email') <p class="text-red-500">{{ $message }}</p> @enderror
            </div>
            @if($role === 'client')
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Project Manager</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="project_manager"
                >
                @error('project_manager')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            @endif
            @if($role === 'client')
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Client Type</label>
                <select 
                    class="w-full text-black rounded-lg"
                    wire:model.change="client_type"
                >
                    <option value="" disabled>Select client type</option>
                    <option value="business">Business</option>
                    <option value="political">Political</option>
                </select>
                @error('client_type')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            @endif
            @if($role === 'client')
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Company Address</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="company_address"
                >
                @error('company_address')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            @endif
            @if($role === 'client')
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Url Sharepoint</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="url_sharepoint"
                >
                @error('url_sharepoint')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            @endif
            <div class="mt-5 space-y-2">
                <label for="" class="block tracking-wider text-gray-600">Change Password</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="password"
                    wire:model="password"
                >
                @error('password')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
        
        <div class="mt-5 space-y-2">
            @if ($photo) 
                <img src="{{ $photo->temporaryUrl() }}" class="mb-5 rounded-lg shadow-md max-w-48">
                @else
                @if($img_path)
                    <img src="{{ $img_path }}?{{ now()->timestamp }}" class="mb-5 rounded-lg shadow-md max-w-48">
                @endif
            @endif
            <label for="" class="block tracking-wider text-gray-600">Upload Logo</label>
            <input 
                class="w-full max-w-lg"
                type="file"
                wire:model="photo"
            >
            @error('photo')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
        
        @if($role == 'client')
            @foreach ($person_in_contact as $index => $contact)
                <div wire:key="edit-users-{{ $index }}">
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
                            wire:model="person_in_contact.{{ $index }}.email"
                        >
                        @error("person_in_contact.$index.email") <p class="text-red-500">{{ $message }}</p> @enderror
                    </div>
                    @if ($index !== 0)
                        <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="removePersonInContact({{ $index }})"
                        >
                            Delete
                        </button>
                    @endif
                </div>
            @endforeach
            <button 
                class="block px-2 py-1 mt-5 text-white bg-gray-500 border rounded-lg hover:bg-gray-600"
                type="button" 
                wire:click="addPersonInContact"
            >
                Add more person in contact
            </button>
        @endif
        <div class="flex justify-end">
            <button 
                class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                type="Submit" 
            >
                Submit
            </button>
        </div>
    </form>
</div>

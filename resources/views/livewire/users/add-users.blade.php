<?php

use App\Models\User;
use App\Models\PersonInContact;
use App\Models\MoreInfoValue;
use App\Models\MoreInfo;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] 
class extends Component {

    use WithFileUploads;
    
    public $name = '';
    public $company_cell = '';
    public $company_address = '';
    public $email = '';
    public $role = '';
    public $client_type = '';
    public $project_manager = '';
    public $url_sharepoint = '';
    public $password = '';
    public $photo;
    public $person_in_contact = [];
    public $more_info_business = [];
    public $more_info_political = [];
    public $moreInfo = [];
    public $moreInfoValuesBusiness = [];
    public $moreInfoValuesPolitical = [];
    public $socials = [];
    public $assets = [];

    public function mount()
    {
        $moreInfoBusiness = MoreInfo::where('client_type', 'business')->get();
        $moreInfoPolitical = MoreInfo::where('client_type', 'political')->get();
        $this->more_info_business = $moreInfoBusiness;
        $this->more_info_political = $moreInfoPolitical;

    }

    public function addUser()
    {
        $this->validate();
        
        $image = $this->photo;
        $img_path = '';

        if($image){            
            $uuid = substr(Str::uuid()->toString(), 0, 8);
            $file_name = $uuid . '.' . $image->getClientOriginalExtension();
            $img_path = url('images/user-logo/' . $file_name);
            $image->storePubliclyAs('images/user-logo', $file_name, 'public');
        }

        $user_id = User::insertGetId([
            'name' => $this->name,
            'role' => $this->role,
            'email' => $this->email,
            'company_cell_number' => $this->company_cell === '' ? null : $this->company_cell,
            'company_address' => $this->company_address === '' ? null : $this->company_address,
            'url_sharepoint' => $this->url_sharepoint === '' ? null : $this->url_sharepoint,
            'project_manager' => $this->project_manager === '' ? null : $this->project_manager,
            'client_type' => $this->client_type === '' ? null : $this->client_type,
            'assets' => json_encode($this->assets),
            'socials' => json_encode($this->socials),
            'img_path' => $img_path === '' ? null : $img_path,
            'password' => Hash::make($this->password),
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        
        if($this->role == 'client'){                      
            foreach($this->person_in_contact as $person) {
                PersonInContact::create([
                    'name' => $person['name'],
                    'user_id' => $user_id,
                    'cell_number' => $person['cell_number'],
                    'email' => $person['email'],
                ]);
            }

            if ($this->client_type === 'business') {
                $moreInfos = $this->more_info_business;
                $moreInfoValues = $this->moreInfoValuesBusiness;
            } else if ($this->client_type === 'political') {
                $moreInfos = $this->more_info_political;
                $moreInfoValues = $this->moreInfoValuesPolitical;
            }

            foreach ($moreInfos as $moreInfo) {
                $id = $moreInfo->id;
                $data_type =  $moreInfo->data_type;
                if (!isset($moreInfoValues[$id])) continue;
                $value = $moreInfoValues[$id];
                $dataToInsert = [
                    'user_id' => $user_id,
                    'more_info_id' => $id,
                ];
                if ($data_type === 'text') {
                    $dataToInsert['text_value'] = $value;
                    $dataToInsert['paragraph_value'] = null;
                    $dataToInsert['date_value'] = null;
                } else if ($data_type === 'paragraph') {
                    $dataToInsert['text_value'] = null;
                    $dataToInsert['paragraph_value'] = $value;
                    $dataToInsert['date_value'] = null;
                } else if ($data_type === 'date') {
                    $dataToInsert['text_value'] = null;
                    $dataToInsert['paragraph_value'] = null;
                    $dataToInsert['date_value'] = $value;
                }

                MoreInfoValue::create($dataToInsert);
            }
        }

        $this->reset([
            'name', 
            'role', 
            'email', 
            'company_cell', 
            'company_address', 
            'person_in_contact', 
            'password', 
            'project_manager', 
            'client_type'
        ]);
        
        session()->flash('status', 'User Successfully added');
        $this->redirect('/users', navigate: true);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|min:3',
            'role' => 'required|in:admin,client',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
        ];

        $moreRules = [];

        if ($this->role === 'client') {
            $rules = array_merge($rules, [
                'photo' => 'nullable|image|max:1024',
                'company_cell' => 'required|min:3',
                'company_address' => 'required|min:3',
                'project_manager' => 'required|min:3',
                'client_type' => 'required|in:business,political',
                'url_sharepoint' => 'nullable|url',
                'person_in_contact.*.name' => 'required|min:3',
                'person_in_contact.*.cell_number' => 'required|min:3',
                'person_in_contact.*.email' => 'required|email',
                'assets.*.label' => 'required|min:3',
                'assets.*.link' => 'required|url',
                'socials.*.label' => 'required|min:1',
                'socials.*.link' => 'required|url',
            ]);

            foreach ($this->more_info_business as $moreInfo) {
                if ($moreInfo->data_type === 'date') {
                    $moreRules['moreInfoValuesBusiness.'.$moreInfo->id] = 'nullable|date';
                } 
            }

            foreach ($this->more_info_political as $moreInfo) {
                if ($moreInfo->data_type === 'date') {
                    $moreRules['moreInfoValuesPolitical.'.$moreInfo->id] = 'nullable|date';
                } 
            }
        }

        return array_merge($rules, $moreRules);
    }

    public function messages()
    {
        return [
            'person_in_contact.*.name.required' => 'The name field is required for each person in contact.',
            'person_in_contact.*.name.min' => 'The name must be at least :min characters long.',
            'person_in_contact.*.cell_number.required' => 'The cell number field is required for each person in contact.',
            'person_in_contact.*.cell_number.min' => 'The cell number must be at least :min characters long.',
            'person_in_contact.*.email.required' => 'The email address field is required for each person in contact.',
            'person_in_contact.*.email.email' => 'Each email address must be a valid email address.',
            'moreInfoValuesPolitical.*.date' => 'The date must be a valid date.',
            'moreInfoValuesBusiness.*.date' => 'The date must be a valid date.',
            'assets.*.label.required' => 'The label field is required for each asset.',
            'assets.*.label.min' => 'The label must be at least :min characters long.',
            'assets.*.link.required' => 'The link field is required for each asset.',
            'assets.*.link.url' => 'Each link must be a valid URL.',
            'socials.*.label.required' => 'The label field is required for each social.',
            'socials.*.label.min' => 'The label must be at least :min characters long.',
            'socials.*.link.required' => 'The link field is required for each social.',
        ];
    }

    public function addPersonInContact()
    {
        $this->person_in_contact[] = [
            'name' => '',
            'cell_number' => '',
            'email_address' => '',
        ];
    }

    public function removePersonInContact($index)
    {
        unset($this->person_in_contact[$index]);
        $this->person_in_contact = array_values($this->person_in_contact);
    }

    public function addAsset() {
        $this->assets[] = [
            'label' => '',
            'link' => '',
        ];
    }

    public function removeAsset($index) {
        unset($this->assets[$index]);
        $this->assets = array_values($this->assets);
    }

    public function addSocial() {
        $this->socials[] = [
            'label' => '',
            'link' => '',
        ];
    }

    public function removeSocial($index) {
        unset($this->socials[$index]);
        $this->socials = array_values($this->socials);
    }

}; ?>

<div class="w-full p-3 text-black bg-white rounded-lg lg:p-6">
    <form action="" wire:submit="addUser">
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
                <label for="" class="block tracking-wider text-gray-600">URL Sharepoint</label>
                <input 
                    class="w-full text-black rounded-lg"
                    type="text"
                    wire:model="url_sharepoint"
                >
                @error('url_sharepoint')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            @endif
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
        
        <div class="mt-5 space-y-2">
            @if ($photo) 
                <img src="{{ $photo->temporaryUrl() }}" class="mb-5 rounded-lg shadow-md max-w-48">
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
            {{-- more Info --}}
            @php
            if ($client_type === 'business') {
            $moreInfo = $more_info_business;
            } else if ($client_type === 'political') {
            $moreInfo = $more_info_political;
            }
            $errorClientType = ucwords($client_type);
            @endphp
            @if ($moreInfo)
            <div>
                <hr class="my-10">
                <h1 class="font-bold lg:text-3xl">More Info</h1>
                <div class="grid sm:grid-cols-2 gap-5 lg:grid-cols-3 lg:max-w-[unset] lg:gap-5">
                @foreach ($moreInfo as $index => $info)
                    <div class="flex-grow mt-5 space-y-2">
                        <label for="" class="block tracking-wider text-gray-600">{{ $info->label }}</label>
                        @if ($info->data_type === 'text')
                        <input 
                            class="w-full max-w-lg text-black rounded-lg"
                            type="text"
                            wire:model="moreInfoValues{{ ucwords($client_type) }}.{{ $info->id }}"
                        >
                        @elseif ($info->data_type === 'date')
                        <input 
                            class="w-full max-w-lg text-black rounded-lg"
                            type="date"
                            wire:model="moreInfoValues{{ ucwords($client_type) }}.{{ $info->id }}"
                        >
                        @elseif ($info->data_type === 'paragraph')
                        <textarea 
                            class="w-full max-w-lg text-black rounded-lg"
                            wire:model="moreInfoValues{{ ucwords($client_type) }}.{{ $info->id }}"
                        ></textarea>
                        @endif
                        @error("moreInfoValues$errorClientType.$info->id") <p class="text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endforeach
                </div>
            </div>
            @endif
            <div>
                <hr class="my-10">
                <h1 class="font-bold lg:text-3xl">Person in Contact</h1>
                @foreach ($person_in_contact as $index => $contact)
                    <div wire:key="add-users-{{ $index }}">
                        <div class="flex flex-col sm:max-w-[50%] lg:max-w-[unset] lg:flex-row lg:gap-5">
                            <div class="flex-grow mt-5 space-y-2">
                                <label for="" class="block tracking-wider text-gray-600">Name</label>
                                <input 
                                    class="w-full max-w-lg text-black rounded-lg"
                                    type="text"
                                    wire:model="person_in_contact.{{ $index }}.name"
                                >
                                @error("person_in_contact.$index.name") <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex-grow mt-5 space-y-2">
                                <label for="" class="block tracking-wider text-gray-600">Cell Number</label>
                                <input 
                                    class="w-full max-w-lg text-black rounded-lg"
                                    type="text"
                                    wire:model="person_in_contact.{{ $index }}.cell_number"
                                >
                                @error("person_in_contact.$index.cell_number") <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex-grow mt-5 space-y-2">
                                <label for="" class="block tracking-wider text-gray-600">Email Address</label>
                                <input 
                                    class="w-full max-w-lg text-black rounded-lg"
                                    type="text"
                                    wire:model="person_in_contact.{{ $index }}.email"
                                >
                                @error("person_in_contact.$index.email") <p class="text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="removePersonInContact({{ $index }})"
                        >
                            Delete
                        </button>
                    </div>
                @endforeach
                <button 
                    class="block px-2 py-1 mt-5 text-white bg-gray-500 border rounded-lg hover:bg-gray-600"
                    type="button" 
                    wire:click="addPersonInContact"
                >
                    Add more person in contact
                </button>
            </div>
            <div>
                <hr class="my-10">
                <h1 class="font-bold lg:text-3xl">Assets</h1>
                @foreach ($assets as $index => $asset)
                    <div class="flex flex-col max-w-screen-md lg:flex-row lg:gap-5">
                        <div class="flex-grow mt-5 space-y-2">
                            <label for="" class="block tracking-wider text-gray-600">Label</label>
                            <input 
                                class="w-full max-w-lg text-black rounded-lg"
                                type="text"
                                wire:model="assets.{{ $index }}.label"
                            >
                            @error("assets.$index.label") <p class="text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex-grow mt-5 space-y-2">
                            <label for="" class="block tracking-wider text-gray-600">Link</label>
                            <input 
                                class="w-full max-w-lg text-black rounded-lg"
                                type="text"
                                placeholder="https://"
                                wire:model="assets.{{ $index }}.link"
                            >
                            @error("assets.$index.link") <p class="text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="removeAsset({{ $index }})"
                        >
                            Delete
                        </button>
                @endforeach
                <button 
                    class="block px-2 py-1 mt-5 text-white bg-gray-500 border rounded-lg hover:bg-gray-600"
                    type="button" 
                    wire:click="addAsset"
                >
                    Add more asset
                </button>

            </div>
            <div>
                <hr class="my-10">
                <h1 class="font-bold lg:text-3xl">Socials</h1>
                @foreach ($socials as $index => $social)
                    <div class="flex flex-col max-w-screen-md lg:flex-row lg:gap-5">
                        <div class="flex-grow mt-5 space-y-2">
                            <label for="" class="block tracking-wider text-gray-600">Label</label>
                            <input 
                                class="w-full max-w-lg text-black rounded-lg"
                                type="text"
                                wire:model="socials.{{ $index }}.label"
                            >
                            @error("socials.$index.label") <p class="text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex-grow mt-5 space-y-2">
                            <label for="" class="block tracking-wider text-gray-600">Link</label>
                            <input 
                                class="w-full max-w-lg text-black rounded-lg"
                                type="text"
                                placeholder="https://"
                                wire:model="socials.{{ $index }}.link"
                            >
                            @error("socials.$index.link") <p class="text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button 
                            class="px-2 py-1 mt-5 text-white bg-red-500 border rounded-lg hover:bg-red-600"
                            type="button"
                            wire:click="removeSocial({{ $index }})"
                        >
                            Delete
                        </button>
                @endforeach
                <button 
                    class="block px-2 py-1 mt-5 text-white bg-gray-500 border rounded-lg hover:bg-gray-600"
                    type="button" 
                    wire:click="addSocial"
                >
                    Add more social
                </button>
            </div>
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

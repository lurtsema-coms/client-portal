<?php

use App\Models\User;
use App\Models\PersonInContact;
use App\Models\MoreInfoValue;
use App\Models\MoreInfo;
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
    public $password = '';
    public $img_path = '';
    public $photo;
    public $person_in_contact = [];
    public $more_info_business = [];
    public $more_info_political = [];
    public $moreInfo = [];
    public $moreInfoValuesBusiness = [];
    public $moreInfoValuesPolitical = [];
    public $socials = [];
    public $assets = [];
    public $account_manager = [];

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
        $this->account_manager = json_decode($user->account_manager, true) ?? [];
        $this->img_path = $user->img_path;
        $this->socials = json_decode($user->socials, true) ?? [];
        $this->assets = json_decode($user->assets, true) ?? [];
        
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

        $moreInfoBusiness = MoreInfo::where('client_type', 'business')->get();
        $moreInfoPolitical = MoreInfo::where('client_type', 'political')->get();
        $this->more_info_business = $moreInfoBusiness;
        $this->more_info_political = $moreInfoPolitical;

        $moreInfoValuesBusiness = MoreInfoValue::where('user_id', $user_id)
            ->whereHas('moreInfo', function ($query) {
            $query->where('client_type', 'business');
        })->get();

        foreach ($moreInfoValuesBusiness as $value) {
            $this->moreInfoValuesBusiness[$value->moreInfo->id] = $value->text_value ?? $value->paragraph_value ?? $value->date_value;
        }

        $moreInfoValuesPolitical = MoreInfoValue::where('user_id', $user_id)
            ->whereHas('moreInfo', function ($query) {
            $query->where('client_type', 'political');
        })->get();

        
        foreach ($moreInfoValuesPolitical as $value) {
            $this->moreInfoValuesPolitical[$value->moreInfo->id] = $value->text_value ?? $value->paragraph_value ?? $value->date_value;
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
            'url_sharepoint' => $this->url_sharepoint === '' ? null : $this->url_sharepoint,
            'client_type' => $this->client_type === '' ? null : $this->client_type,
            'account_manager' => !empty($this->account_manager) ? json_encode($this->account_manager) : null,
            'assets' => !empty($this->assets) ? json_encode($this->assets) : null,
            'socials' => !empty($this->socials) ? json_encode($this->socials) : null,
            'created_by' => auth()->user()->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if($this->password != ''){
            $this->user->update([
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

            if ($this->client_type === 'business') {
                $moreInfos = $this->more_info_business;
                $moreInfoValues = $this->moreInfoValuesBusiness;
            } else if ($this->client_type === 'political') {
                $moreInfos = $this->more_info_political;
                $moreInfoValues = $this->moreInfoValuesPolitical;
            }

            MoreInfoValue::where('user_id', $this->user->id)->forceDelete();

            foreach ($moreInfos as $moreInfo) {
                $id = $moreInfo->id;
                $data_type =  $moreInfo->data_type;
                if (!isset($moreInfoValues[$id])) continue;
                $value = $moreInfoValues[$id];
                if (!$value) continue;
                $dataToInsert = [
                    'user_id' => $this->user->id,
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
            'photo' => 'nullable|mimes:jpg,png,svg,webp|max:1024',
        ];

        $moreRules = [];

        if ($this->role === 'client') {
            $rules = array_merge($rules, [
                'company_cell' => 'required|min:3',
                'company_address' => 'required|min:3',
                'url_sharepoint' => 'nullable|url',
                'account_manager.name' => 'required|min:3',
                'account_manager.cell_number' => 'nullable|min:3',
                'account_manager.email' => 'nullable|email',
                'client_type' => 'nullable|in:business,political',
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
            'person_in_contact.*.email' => 'Each email address must be a valid email address.',
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

    public function handleDelete() {
        $this->user->delete();

        session()->flash('status', 'User Account Successfully Deleted');
        $this->redirect('/users', navigate: true);
    }
}; ?>

<div>
    <x-header-title headingTitle="Edit User" backButton="true" />
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
                @if ($photo && in_array($photo->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'svg', 'webp']))
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
                    accept=".jpg, .jpeg, .png, .svg, webp"
                >
                @error('photo')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>
            
            @if($role == 'client')
                <div>
                    <hr class="my-10">
                    <h1 class="font-bold lg:text-3xl">Account Manager</h1>
                        <div>
                            <div class="flex flex-col sm:max-w-[50%] lg:max-w-[unset] lg:flex-row lg:gap-5">
                                <div class="flex-grow mt-5 space-y-2">
                                    <label for="" class="block tracking-wider text-gray-600">Name</label>
                                    <input 
                                        class="w-full max-w-lg text-black rounded-lg"
                                        type="text"
                                        wire:model="account_manager.name"
                                    >
                                    @error("account_manager.name") <p class="text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex-grow mt-5 space-y-2">
                                    <label for="" class="block tracking-wider text-gray-600">Cell Number</label>
                                    <input 
                                        class="w-full max-w-lg text-black rounded-lg"
                                        type="text"
                                        wire:model="account_manager.cell_number"
                                    >
                                    @error("account_manager.cell_number") <p class="text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex-grow mt-5 space-y-2">
                                    <label for="" class="block tracking-wider text-gray-600">Email Address</label>
                                    <input 
                                        class="w-full max-w-lg text-black rounded-lg"
                                        type="text"
                                        wire:model="account_manager.email"
                                    >
                                    @error("account_manager.email") <p class="text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                </div>
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
                        <div cla wire:key="edit-users-{{ $index }}">
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
            <div class="flex gap-5 mt-10">
                <button 
                    class="px-4 py-2 mt-5 text-right text-white bg-blue-500 border rounded-lg hover:bg-blue-600"
                    type="Submit" 
                >
                    Submit
                </button>
                <button 
                    data-modal-target="default-modal" data-modal-toggle="default-modal"
                    class="px-4 py-2 mt-5 text-right text-white bg-red-500 border rounded-lg hover:bg-red-600"
                    type="button" 
                >
                    Delete
                </button>
            </div>
            <!-- Main modal -->
            <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
                <div class="relative w-full max-w-2xl max-h-full p-4">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5">
                                        <h3 class="text-xl font-semibold text-gray-900">
                                                Delete this user account
                                        </h3>
                                        <button type="button" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto" data-modal-hide="default-modal">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                        </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-4 space-y-4 md:p-5">
                                        <p class="text-base leading-relaxed text-gray-500">
                                                Are you sure you want to delete this user account? You won't be able to revert this action.
                                        </p>
                                </div>
                                <!-- Modal footer -->
                                <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5">
                                        <button wire:click="handleDelete" data-modal-hide="default-modal" type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Yes, delete it</button>
                                        <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Cancel</button>
                                </div>
                        </div>
                </div>
            </div> 
        </form>
    </div>
</div>

@script
<script>
    initFlowbite();
</script>
@endscript

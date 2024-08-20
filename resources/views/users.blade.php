<x-admin-layout>
  <div class="flex flex-col items-stretch justify-start gap-5">
      <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold lg:text-7xl">Users</h1>
          <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
      </div>
      @livewire('users')
  </div>
</x-admin-layout>

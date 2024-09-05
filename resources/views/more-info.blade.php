<x-admin-layout>
  <div class="flex flex-col items-stretch justify-start gap-5">
      <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold lg:text-7xl">More Info</h1>
          <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
      </div>
      @if (session('status'))
          <div 
              x-data="{ show: true }"
              x-init="setTimeout(() => show = false, 6000)" 
              x-show="show"
              class="text-green-400"
          >
              {{ session('status') }}
          </div>
      @endif
      @livewire('more-info')
  </div>
</x-admin-layout>

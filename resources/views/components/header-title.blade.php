<div class="">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold lg:text-7xl">{{ $headingTitle }}</h1>
    <x-application-logo class="block w-auto h-10 text-white fill-current lg:h-20" />
  </div>
  @if (isset($backButton))
  <button onclick="history.back()" class="my-5 text-lg border border-t-0 border-l-0 border-r-0 border-b-button-blue text-button-blue hover:opacity-50">← Back</button>
  @endif
</div>

@php // Partial: include in layouts where you want update notification to appear @endphp
<div x-cloak x-data x-show="Alpine.store('update').hasUpdate" x-transition class="fixed top-4 right-4 z-50">
  <div class="alert alert-info shadow-lg max-w-md">
    <div class="flex items-start gap-3">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 7v.01"></path></svg>
      <div class="flex-1">
        <div class="font-bold">Update available: <span x-text="Alpine.store('update').latest?.name || Alpine.store('update').latest?.tag_name"></span></div>
        <div class="text-sm mt-1" x-text="(Alpine.store('update').latest?.body || '').split('\n')[0]"></div>
        <div class="mt-2 text-right">
          <a :href="Alpine.store('update').latest?.html_url" class="btn btn-sm btn-primary mr-2" target="_blank">View release</a>
          <button @click="Alpine.store('update').dismiss()" class="btn btn-sm">Dismiss</button>
        </div>
      </div>
    </div>
  </div>
</div>

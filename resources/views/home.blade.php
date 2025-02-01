<x-layout>
  <div x-data="{ open: false }">
    <button @click="open = true">Expand</button>
 
    <span x-show="open">
        <div class="bg-slate-500 max-w-md">
          <p class="text-8xl">Hello World!</p>
        </div>
    </span>
</div>
</x-layout>
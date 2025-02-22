<x-layout>
  <div x-data="{ open: false }">
    <button @click="open = true">Expand</button>
    <span x-show="open">
        <div class="bg-slate-500 max-w-md">
          <p class="text-8xl">Hello World!</p>
        </div>
    </span>
</div>

<div x-data="dropdown">
  <button @click="toggle">Expand</button>

  <span x-show="open">Content...</span>
</div>

<div x-data="dropdown">
  <button @click="toggle">Expand</button>

  <span x-show="open">Some Other Content...</span>
</div>

</x-layout>
<template>
  <div class="min-h-screen grid grid-cols-1 md:grid-cols-2 font-sans bg-[#f7f6f3]">

    <!-- Left Brand Panel -->
    <aside class="hidden md:flex relative bg-[#0f0f0f] overflow-hidden">
      <div class="relative z-10 flex flex-col justify-between p-12 w-full">

        <!-- Logo -->
        <div class="w-12 h-12 bg-[#e8e0d0] rounded-xl flex items-center justify-center">
          <span class="font-serif text-[#0f0f0f] text-sm font-bold tracking-tight">PM</span>
        </div>

        <!-- Copy -->
        <div>
          <h1 class="font-serif text-[#f7f6f3] text-4xl xl:text-5xl leading-tight tracking-tight">
            Manage work,<br />
            <em class="italic text-[#c9b99a]">not chaos.</em>
          </h1>
          <p class="mt-4 text-[#888] text-sm font-light leading-relaxed max-w-[28ch]">
            An integrated workspace built for focused teams.
          </p>
        </div>

        <!-- Footer -->
        <span class="text-[#444] text-xs">© {{ year }} ProjectHub</span>
      </div>

      <!-- Grid overlay -->
      <div
        class="absolute inset-0 z-0"
        style="background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px;"
      ></div>
    </aside>

    <!-- Right Form Panel -->
    <main class="flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-sm">
        <slot />
      </div>
    </main>

  </div>
</template>

<script setup>
const year = new Date().getFullYear()
</script>
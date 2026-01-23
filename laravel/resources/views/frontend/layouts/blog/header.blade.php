<!-- Main Header -->
<header id="page-header" class="flex flex-none flex-col gap-6">
    <div class="flex items-center justify-center gap-2">
        <!-- Photo -->
        <a href="{{ url('/') }}"
            class="group flex size-32 items-center justify-center rounded-full
    bg-radial from-orange-200 via-rose-200 to-orange-200
    shadow-lg shadow-orange-300/40
    transition hover:scale-105 hover:shadow-orange-400/50">
            <img src="{{ asset('img/avatar.png') }}" alt="Thiébault Michaël"
                class="aspect-square size-32 rounded-full object-cover ring-2 ring-orange-400/60" />
            <span class="sr-only">Thiébault Michaël</span>
        </a>
        <!-- END Photo -->
    </div>
</header>
<!-- END Main Header -->

<!-- Separator -->
<div class="relative mx-auto my-8 h-[0.875rem] w-full flex-none px-4 sm:my-12 lg:px-8">
    <div
        class="absolute right-0 bottom-0 left-0 h-1.5 bg-linear-to-r from-orange-200 via-rose-200 to-orange-200 dark:from-orange-700/40 dark:via-red-600/40 dark:to-orange-700/40">
    </div>
    <div class="absolute right-0 bottom-0 left-0 z-1 h-1.5 bg-linear-to-t from-white dark:from-zinc-900"></div>
    <div class="absolute bottom-0 left-0 z-2 h-1.5 w-60 bg-linear-to-r from-white dark:from-zinc-900"></div>
    <div class="absolute right-0 bottom-0 z-2 h-1.5 w-60 bg-linear-to-l from-white dark:from-zinc-900"></div>
</div>
<!-- END Separator -->

<!doctype html>
<html lang="{{ app()->getLocale() }}" x-data="darkModeData()" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />

    <title>Blog Thiébault Michaël Web developer</title>

    @include('frontend.layouts.blog.tailwindcss')
</head>

<body class="antialiased bg-zinc-100 text-zinc-800 dark:bg-neutral-950 dark:text-zinc-100">
    <!-- Page Container -->
    <div class="relative min-h-dvh min-w-80 bg-zinc-100 text-zinc-800 dark:bg-neutral-950 dark:text-zinc-100">
        <div class="absolute inset-x-0 -top-px h-[600px] bg-zinc-50 bg-[image:repeating-linear-gradient(135deg,_currentColor_0,_currentColor_1px,_transparent_0,_transparent_50%)] [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))] bg-[size:10px_10px] text-zinc-400/10 dark:bg-neutral-950 dark:text-zinc-100/10"
            aria-hidden="true"></div>
        <!-- Page Content -->
        <main id="page-content"
            class="relative mx-auto flex min-h-dvh max-w-7xl flex-auto flex-col border-x border-zinc-200 bg-zinc-50  p-6 ring-4 ring-zinc-200/25 lg:px-40 lg:py-8 dark:border-zinc-800 dark:bg-[#191927] dark:ring-zinc-900">
            @include('frontend.layouts.blog.darkmode-toggle')

            @include('frontend.layouts.blog.header')

            <!-- Main Content -->
            <div class="flex grow flex-col gap-16">
                @yield('content')
            </div>
            <!-- END Main Content -->
            @include('frontend.layouts.blog.footer')
        </main>
        <!-- END Page Content -->
    </div>
    <!-- END Page Container -->
    @include('frontend.layouts.blog.js')
</body>

</html>

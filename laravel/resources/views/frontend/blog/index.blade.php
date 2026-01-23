    @extends('frontend.layouts.blog.html')
    @section('content')
        <!-- Heading -->
        <div class="text-center">
            <div
                class="mx-auto mb-2 inline-flex rounded-lg bg-zinc-100 px-2.5 py-1 text-sm font-medium text-zinc-500 dark:bg-zinc-800/50 dark:text-neutral-400">
                Thoughts & Learnings
            </div>
            <h1 class="font-[Space Grotesk] text-3xl font-black lg:text-5xl">Welcome to my blog</h1>
            <h2
                class="font-[Space Grotesk] mx-auto mt-5 max-w-2xl leading-relaxed text-balance text-zinc-500 dark:text-neutral-400">
                Here, I share selected projects I’ve built as a web developer.<br />
                You’ll also find tutorials and insights inspired by these projects.
                Explore, learn, and get inspired by my hands-on work.
            </h2>
        </div>
        <!-- END Heading -->

        <livewire:frontend.posts.posts-list />
    @endsection

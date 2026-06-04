    @extends('frontend.layouts.blog.html')
    @section('content')
        <!-- Heading -->
        <div class="text-center">
            <div
                class="mx-auto mb-2 inline-flex rounded-lg bg-zinc-100 px-2.5 py-1 text-sm font-medium text-zinc-500 dark:bg-zinc-800/50 dark:text-neutral-400">
                Réflexions & Apprentissages
            </div>
            <h1 class="font-[Space Grotesk] text-3xl font-black lg:text-5xl">Bienvenue sur mon blog</h1>
            <h2
                class="font-[Space Grotesk] mx-auto mt-5 max-w-2xl leading-relaxed text-balance text-zinc-500 dark:text-neutral-400">
                Ici, je partage une sélection de projets que j’ai réalisés en tant que développeur web.<br />
                Vous y trouverez également des tutoriels et des réflexions inspirés de ces projets. Explorez, apprenez
                et laissez-vous inspirer par mon expérience de terrain.
            </h2>
        </div>
        <!-- END Heading -->

        <livewire:frontend.posts.posts-list />
    @endsection

<div>
    <!-- Search Bar -->
    <div class="flex justify-center mb-6">
        <div class="group relative w-full max-w-xl">
            <div
                class="pointer-events-none absolute inset-0 rounded-xl bg-gradient-to-br from-orange-400/10 via-transparent to-rose-400/10 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
            </div>
            <div
                class="relative flex items-center rounded-xl border border-neutral-200 bg-white/80 px-4 py-3 shadow-sm transition-all duration-300 focus-within:border-orange-400 focus-within:shadow-orange-100 hover:border-orange-300 dark:border-neutral-800 dark:bg-[#1f1f35] dark:focus-within:border-orange-500/40 dark:focus-within:shadow-orange-500/10">
                <svg class="mr-3 h-5 w-5 text-zinc-500 transition group-focus-within:text-orange-500 dark:text-neutral-400"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                </svg> <input type="text" placeholder="Search posts by title, tags or categories"
                    wire:model.live="search"
                    class="w-full bg-transparent text-sm text-zinc-800 outline-none placeholder:text-zinc-500 dark:text-zinc-200 dark:placeholder:text-neutral-500" />
            </div>
        </div>
    </div> <!-- Posts -->
    <div class="mx-auto flex max-w-7xl flex-col gap-8">
        @forelse ($posts as $post)
            <div
                class="group relative rounded-2xl border border-neutral-200 bg-white/80 p-6 transition-all duration-300 hover:-translate-y-1 hover:border-orange-300 hover:shadow-lg hover:shadow-orange-100 dark:border-neutral-800 dark:bg-[#1f1f35] dark:hover:border-orange-500/40 dark:hover:shadow-orange-500/10">
                <div class="relative z-10"> <!-- Date + Category -->
                    <div class="mb-3 flex flex-wrap items-center gap-3">
                        <p class="text-sm font-medium text-zinc-600 dark:text-neutral-400">
                            {{ $post->created_at->format('F j, Y') }} </p>
                        @foreach ($post->categories as $cat)
                            <a wire:click.prevent="selectTerm({{ $cat->id }})" href="#"
                                class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 transition hover:bg-orange-200 dark:bg-orange-500/15 dark:text-orange-300 dark:hover:bg-orange-500/25">
                                {{ $cat->name }} </a>
                        @endforeach
                    </div> <!-- Title -->
                    <h4 class="font-[Space Grotesk] mb-3 text-lg font-bold sm:text-xl"> <a
                            href="{{ route('blog.post', $post->slug) }}"
                            class="leading-7 text-zinc-800 hover:text-zinc-600 dark:text-zinc-200 dark:hover:text-zinc-400">
                            {{ $post->title }} </a> </h4> <!-- Tags -->
                    <div class="mb-3 flex flex-wrap gap-2">
                        @foreach ($post->tags as $tag)
                            <a wire:click.prevent="selectTerm({{ $tag->id }})" href="#"
                                class="rounded-md
                                border border-zinc-200 bg-zinc-50 px-2.5 py-1 text-xs font-medium text-zinc-700
                                transition hover:bg-zinc-100 dark:border-neutral-700 dark:bg-neutral-800
                                dark:text-neutral-300 dark:hover:bg-neutral-700">
                                #{{ $tag->name }} </a>
                        @endforeach
                    </div> <!-- Excerpt -->
                    <p class="mb-3 text-sm leading-relaxed text-zinc-600 dark:text-neutral-400">
                        {{ \Illuminate\Support\Str::words(strip_tags($post->content), 80) }} </p> <!-- Read more --> <a
                        href="{{ route('blog.post', $post->slug) }}"
                        class="text-sm font-medium text-orange-600 hover:text-orange-500 dark:text-orange-400 dark:hover:text-orange-300">
                        Read more → </a>
                </div>
        </div> @empty <p class="text-center text-zinc-500 dark:text-neutral-400">No posts found.</p>
        @endforelse
    </div> <!-- Pagination -->
    <div class="mt-6"> {{ $posts->links() }} </div>
</div>

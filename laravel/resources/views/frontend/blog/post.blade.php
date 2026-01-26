@extends('frontend.layouts.blog.html')

@section('content')
    <!-- Main Content -->
    <div class="flex grow flex-col gap-16">

        <!-- Back link -->
        <div>
            <a href="{{ route('blog.posts') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition">
                ← All posts
            </a>
        </div>

        <!-- Heading -->
        <div class="text-center">
            <div
                class="mx-auto mb-2 inline-flex rounded-lg bg-zinc-100 px-2.5 py-1 text-sm font-medium text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                {{ $post->published_at?->format('F j, Y') ?? $post->created_at->format('F j, Y') }}
            </div>

            <h1 class="text-3xl font-black lg:text-5xl">
                {{ $post->title }}
            </h1>

            @if ($post->categories->count() || $post->tags->count())
                <div class="mt-6 flex flex-wrap justify-center gap-3 text-sm">
                    @foreach ($post->categories as $category)
                        <span
                            class="rounded-full bg-orange-100 px-3 py-2 text-xs font-semibold text-orange-700 transition hover:bg-orange-200 dark:bg-orange-500/15 dark:text-orange-300 dark:hover:bg-orange-500/25">
                            {{ $category->name }}
                        </span>
                    @endforeach

                    @foreach ($post->tags as $tag)
                        <span
                            class="rounded-full border border-zinc-200 px-3 py-1 text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
        <!-- END Heading -->

        <!-- Post -->
        <article
            class="mx-auto w-full max-w-4xl text-zinc-700 dark:text-zinc-300
            [&>h2]:mt-14 [&>h2]:text-2xl [&>h2]:font-bold
            [&>h3]:mt-12 [&>h3]:text-xl [&>h3]:font-bold
            [&>p]:mt-5 [&>p]:leading-relaxed
            [&>pre]:mt-5 [&>pre]:rounded-xl [&>pre]:bg-zinc-800 [&>pre]:p-4
            [&>pre]:text-sm [&>pre]:font-medium [&>pre]:text-zinc-100
            [&>ul]:mt-5 [&>ul]:list-disc [&>ul]:pl-6">
            {!! $post->content !!}
        </article>
        <!-- END Post -->

    </div>
    <!-- END Main Content -->
@endsection

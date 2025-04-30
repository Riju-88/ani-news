<div>
    {{-- Display errors if any --}}
    @if($error)
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @endif

    {{-- Display loading state (optional but good practice) --}}
    {{-- Note: wire:loading applies to actions, might need specific targets for page changes --}}
    <div wire:loading>
        Loading anime...
    </div>

    {{-- Display the anime list when not loading and no error occurred --}}
    {{-- Ensure wire:loading.remove works as expected or target loading state more specifically --}}
    <div wire:loading.remove>
        <h1>Top Airing Anime</h1>

        {{-- Check if the paginator has items for the current page --}}
        @if($topAnimePaginator->count() > 0)
             <div class="container mx-auto my-8 bg-base-100">
                {{-- 8. Loop through the items in the paginator --}}
                @foreach($topAnimePaginator as $anime)
                    <div class="card flex gap-6 p-6 mb-2 shadow rounded-lg bg-white dark:bg-zinc-700 dark:border-zinc-800 dark:text-white dark:focus-within:shadow-zinc-600 dark:hover:bg-slate-700" wire:key="anime-{{ $anime['mal_id'] }}">
                        <img src="{{ $anime['images']['jpg']['large_image_url'] ?? 'placeholder.jpg' }}" alt="{{ $anime['title'] ?? 'N/A' }} Poster" class="rounded w-16 h-24">
                        <div>
                            <h2>{{ $anime['title'] ?? 'Title Not Available' }}</h2>
                            <p><strong>Score:</strong> {{ $anime['score'] ?? 'N/A' }}</p>
                            <p><strong>Episodes:</strong> {{ $anime['episodes'] ?? 'N/A' }}</p>
                            <p><a href="{{ $anime['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer">More Info on MyAnimeList</a></p>
                            {{-- Add more details as needed --}}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 9. Render the pagination links --}}
            <div style="margin-top: 20px;">
                {{ $topAnimePaginator->links() }}
            </div>

        @elseif(!$error)
            {{-- Only show this if there wasn't an error, but no items were returned --}}
            <p>No top airing anime found.</p>
        @endif
    </div>
</div>
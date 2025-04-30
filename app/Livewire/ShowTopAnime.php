<?php

namespace App\Livewire;

use Livewire\Component;
use Eliabrian\LaravelJikan\Facades\Anime;
use Illuminate\Support\Arr; // Or use data_get()
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowTopAnime extends Component
{
    use WithPagination;

    public $perPage = 15;
    public $error = null;
    protected $paginationTheme = 'tailwind';

    

    public function render()
    {
        $this->error = null; // Reset error on each render
        $topAnimeData = [];
        $total = 0;
        $currentPage = $this->getPage(); // Get current page from Livewire trait

        try {
            // 4. Fetch data for the CURRENT page from Jikan
            $response = Anime::top([
                'filter' => 'airing',
                'page'   => $currentPage, // Pass the current page number
                'limit'  => $this->perPage, // Pass the items per page
            ])->get();

            // Safely get the items for the current page and pagination info
            $topAnimeRawData = data_get($response, 'data', []);
            $paginationData = data_get($response, 'pagination');
            // Remove duplicate anime by mal_id
$topAnimeData = collect($topAnimeRawData)->unique('mal_id')->values()->all();

            // Check if we got valid data and pagination info
            if (!is_array($topAnimeData) || !is_array($paginationData)) {
                 // Set error if response structure is wrong, even if API call didn't fail
                 $this->error = 'Unexpected API response format.';
                 Log::warning('Jikan API response structure unexpected.', ['response' => $response]);
                 $topAnimeData = []; // Ensure empty data on format error
                 $total = 0;
            } else {
                 // 5. Extract total count from Jikan's pagination data
                 $total = data_get($paginationData, 'items.total', 0);
            }

        } catch (\Exception $e) {
            $this->error = 'Failed to fetch anime data. Please check connection or try again later.';
            Log::error('Jikan API Error (eliabrian/laravel-jikan): ' . $e->getMessage(), [
                'exception_class' => get_class($e),
            ]);
            // Ensure component state is clean on error
            $topAnimeData = [];
            $total = 0;
        }

        // 6. Manually create the LengthAwarePaginator instance
        $topAnimePaginator = new LengthAwarePaginator(
            $topAnimeData,      // Items for the current page
            $total,             // Total items across all pages
            $this->perPage,     // Items per page
            $currentPage,       // Current page number
            [ // Options: ensure Livewire handles the path correctly
                'path' => request()->url(), // Use the current path for links
                'pageName' => 'page' // Default page query string name
            ]
        );

        // 7. Pass the Paginator instance to the view
        return view('livewire.show-top-anime', [
            'topAnimePaginator' => $topAnimePaginator
        ]);
    }
}
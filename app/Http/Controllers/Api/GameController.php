<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Services\UploadService;
use App\Services\ZipExtractorService;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Display all published games.
     */
    public function index()
    {
        try {
            $query = request('q');

            $games = Game::with('category')
                ->where('status', true);

            if (!empty($query)) {
                $games->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhere('developer', 'like', '%' . $query . '%')
                        ->orWhere('publisher', 'like', '%' . $query . '%');
                });
            }

            $games = $games->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Data games berhasil diambil.',
                'data' => $games
            ]);

        } catch (\Exception $e) {

            Log::error('Game Index Error : '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan mengambil data game.'
            ], 500);

        }
    }

    /**
     * Display game detail.
     */
    public function show(string $slug)
    {
        try {

            $game = Game::with([
                'category',
                'images'
            ])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $game
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Game tidak ditemukan.'
            ], 404);

        }
    }

    /**
     * Store new game.
     */
    public function store(
        StoreGameRequest $request,
        UploadService $upload,
        ZipExtractorService $zip
    )
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Upload Thumbnail
            |--------------------------------------------------------------------------
            */

            $thumbnail = null;

            if ($request->hasFile('thumbnail')) {

                $thumbnail = $upload->uploadImage(
                    $request->file('thumbnail'),
                    'games/thumbnails'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Upload Banner
            |--------------------------------------------------------------------------
            */

            $banner = null;

            if ($request->hasFile('banner')) {

                $banner = $upload->uploadImage(
                    $request->file('banner'),
                    'games/banners'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Upload Game File
            |--------------------------------------------------------------------------
            */

            $gameFile = null;
            $playUrl = null;
            $downloadUrl = null;

            if ($request->hasFile('game')) {

                $folder = $request->type === 'html'
                    ? 'games/html'
                    : 'games/exe';

                $gameFile = $upload->uploadGame(
                    $request->file('game'),
                    $folder
                );

                /*
                |--------------------------------------------------------------------------
                | HTML Game
                |--------------------------------------------------------------------------
                */

                if ($request->type === 'html') {

                    $playUrl = $zip->extract(
                        storage_path('app/public/'.$gameFile),
                        $request->slug
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Desktop Game
                |--------------------------------------------------------------------------
                */

                else {

                    $downloadUrl = asset(
                        'storage/'.$gameFile
                    );

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Save Game
            |--------------------------------------------------------------------------
            */

            $game = Game::create([

                'category_id' => $request->category_id,

                'title' => $request->title,

                'slug' => $request->slug,

                'description' => $request->description,

                'developer' => $request->developer,

                'publisher' => $request->publisher,

                'type' => $request->type,

                'engine' => $request->engine,

                'platform' => $request->platform,

                'version' => $request->version,

                'size' => $request->size,

                'thumbnail' => $thumbnail,

                'banner' => $banner,

                'game_file' => $gameFile,

                'play_url' => $playUrl,

                'download_url' => $downloadUrl,

                'status' => true

            ]);

            /*
            |--------------------------------------------------------------------------
            | Gallery
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('gallery')) {

                foreach ($request->file('gallery') as $index => $image) {

                    $path = $upload->uploadImage(
                        $image,
                        'games/gallery'
                    );

                    $game->images()->create([
                        'image' => $path,
                        'sort_order' => $index
                    ]);

                }

            }

            return response()->json([
                'success' => true,
                'message' => 'Game berhasil dipublish.',
                'data' => $game
            ], 201);

        } catch (\Exception $e) {

            Log::error('Game Store Error : '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    /**
     * Play HTML game.
     */
    public function play(string $slug)
    {
        $game = Game::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        if ($game->type !== 'html') {

            return response()->json([
                'success' => false,
                'message' => 'Game ini tidak dapat dimainkan di browser.'
            ], 400);

        }

        return response()->json([
            'success' => true,
            'play_url' => asset($game->play_url)
        ]);
    }
}
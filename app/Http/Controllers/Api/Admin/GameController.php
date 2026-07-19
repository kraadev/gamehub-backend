<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Services\UploadService;
use App\Services\ZipExtractorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Display all games.
     */
    public function index()
    {
        $games = Game::with('category')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $games
        ]);
    }

    /**
     * Display single game.
     */
    public function show(Game $game)
    {
        $game->load([
            'category',
            'images'
        ]);

        return response()->json([
            'success' => true,
            'data' => $game
        ]);
    }

    /**
     * Store game.
     */
    public function store(
        StoreGameRequest $request,
        UploadService $upload,
        ZipExtractorService $zip
    ) {
        try {

            $thumbnail = null;
            $banner = null;
            $gameFile = null;
            $playUrl = null;
            $downloadUrl = null;

            /*
            |--------------------------------------------------------------------------
            | Thumbnail
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('thumbnail')) {

                $thumbnail = $upload->uploadImage(
                    $request->file('thumbnail'),
                    'games/thumbnails'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Banner
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('banner')) {

                $banner = $upload->uploadImage(
                    $request->file('banner'),
                    'games/banners'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Upload Game
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('game')) {

                $folder = $request->type === 'html'
                    ? 'games/html'
                    : 'games/exe';

                $gameFile = $upload->uploadGame(
                    $request->file('game'),
                    $folder
                );

                if ($request->type === 'html') {

                    $playUrl = $zip->extract(
                        storage_path('app/public/'.$gameFile),
                        Str::slug($request->slug)
                    );

                } else {

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

                'version' => $request->version,

                'engine' => $request->engine,

                'platform' => $request->platform,

                'size' => $request->size,

                'type' => $request->type,

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
                'data' => $game->load([
                    'category',
                    'images'
                ])
            ], 201);

        } catch (\Exception $e) {

            Log::error($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    /**
     * Update game.
     */
    public function update(
        StoreGameRequest $request,
        Game $game
    ) {
        $game->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Game berhasil diperbarui.',
            'data' => $game
        ]);
    }

    /**
     * Delete game.
     */
    public function destroy(Game $game)
    {
        if ($game->thumbnail) {
            Storage::disk('public')->delete($game->thumbnail);
        }

        if ($game->banner) {
            Storage::disk('public')->delete($game->banner);
        }

        if ($game->game_file) {
            Storage::disk('public')->delete($game->game_file);
        }

        if ($game->images) {

            foreach ($game->images as $image) {

                Storage::disk('public')->delete(
                    $image->image
                );

            }

            $game->images()->delete();
        }

        $game->delete();

        return response()->json([
            'success' => true,
            'message' => 'Game berhasil dihapus.'
        ]);
    }
}
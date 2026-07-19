<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::withCount('games')
                ->orderBy('name')
                ->get();

            if ($categories->isEmpty()) {
                $defaults = [
                    ['name' => 'Action', 'slug' => 'action'],
                    ['name' => 'Adventure', 'slug' => 'adventure'],
                    ['name' => 'Puzzle', 'slug' => 'puzzle'],
                    ['name' => 'Arcade', 'slug' => 'arcade'],
                    ['name' => 'Simulation', 'slug' => 'simulation'],
                    ['name' => 'Horror', 'slug' => 'horror'],
                ];

                foreach ($defaults as $default) {
                    Category::firstOrCreate(
                        ['slug' => $default['slug']],
                        ['name' => $default['name']]
                    );
                }

                $categories = Category::withCount('games')
                    ->orderBy('name')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'Categories berhasil diambil.',
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil categories.'
            ], 500);
        }
    }
}

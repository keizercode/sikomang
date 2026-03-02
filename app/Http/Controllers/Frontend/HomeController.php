<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\MangroveLocation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display homepage with real data from database
     */
    public function index()
    {
        // Ambil artikel featured dulu, lalu isi sisanya dari artikel terbaru
        $featuredArticles = Article::published()
            ->featured()
            ->with('user')
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Jika featured kurang dari 3, tambahkan artikel terbaru
        if ($featuredArticles->count() < 3) {
            $featuredIds = $featuredArticles->pluck('id');

            $latestArticles = Article::published()
                ->whereNotIn('id', $featuredIds)
                ->with('user')
                ->latest('published_at')
                ->limit(3 - $featuredArticles->count())
                ->get();

            $articles = $featuredArticles->merge($latestArticles);
        } else {
            $articles = $featuredArticles;
        }

        $stats = [
            'total_locations' => MangroveLocation::where('is_active', true)->count(),
            'total_area'      => MangroveLocation::where('is_active', true)->sum('area'),
            'total_articles'  => Article::published()->count(),
        ];

        return view('pages.frontend.home', compact('articles', 'stats'));
    }
}

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
        // Get published articles (latest 3)
        $articles = Article::published()
            ->with('user')
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Get statistics from database
        $stats = [
            'total_locations' => MangroveLocation::where('is_active', true)->count(),
            'total_area' => MangroveLocation::where('is_active', true)->sum('area'),
            'total_articles' => Article::published()->count(),
        ];

        return view('pages.home', compact('articles', 'stats'));
    }
}

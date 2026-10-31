<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSavedSearchController extends Controller
{
    public function index()
    {
        $searches = Auth::user()->savedSearches()->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.searches', compact('searches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'make' => ['nullable', 'string', 'max:80'],
            'model' => ['nullable', 'string', 'max:80'],
            'price_min' => ['nullable', 'numeric'],
            'price_max' => ['nullable', 'numeric'],
            'year_min' => ['nullable', 'integer'],
            'body_type' => ['nullable', 'string', 'max:40'],
            'province' => ['nullable', 'string', 'max:60'],
            'city' => ['nullable', 'string', 'max:120'],
        ]);

        $data['user_id'] = Auth::id();
        $data['filters'] = array_filter([
            'make' => $data['make'] ?? null,
            'model' => $data['model'] ?? null,
            'price_min' => $data['price_min'] ?? null,
            'price_max' => $data['price_max'] ?? null,
            'year_min' => $data['year_min'] ?? null,
            'body_type' => $data['body_type'] ?? null,
            'province' => $data['province'] ?? null,
            'city' => $data['city'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        unset($data['make'], $data['model'], $data['price_min'], $data['price_max'], $data['year_min'], $data['body_type'], $data['province'], $data['city']);

        SavedSearch::create($data);

        return back()->with('status', 'Search saved. You can return to it anytime from your dashboard.');
    }

    public function destroy(SavedSearch $search)
    {
        abort_unless($search->user_id === Auth::id(), 403);
        $search->delete();

        return back()->with('status', 'Saved search removed.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComparisonController extends Controller
{
    public function index(Request $request): View
    {
        $ids = array_values(array_filter(array_map('intval', is_array($request->input('ids')) ? $request->input('ids') : ($request->has('ids') ? explode(',', $request->input('ids')) : []))));

        $vehicles = Vehicle::published()
            ->whereIn('id', $ids)
            ->with(['dealership', 'privateSeller.user', 'activeImages', 'campaign'])
            ->withEffectiveReward()
            ->limit(4)
            ->get()
            ->keyBy('id')
            ->sortBy(fn ($v) => array_search($v->id, $ids))
            ->values();

        $attributes = [
            ['key' => 'year', 'label' => 'Year'],
            ['key' => 'mileage', 'label' => 'Mileage'],
            ['key' => 'transmission', 'label' => 'Transmission'],
            ['key' => 'fuel_type', 'label' => 'Fuel'],
            ['key' => 'engine_size', 'label' => 'Engine'],
            ['key' => 'power_kw', 'label' => 'Power'],
            ['key' => 'body_type', 'label' => 'Body'],
            ['key' => 'doors', 'label' => 'Doors'],
            ['key' => 'seats', 'label' => 'Seats'],
            ['key' => 'colour', 'label' => 'Colour'],
        ];

        return view('vehicles.compare', ['vehicles' => $vehicles, 'attributes' => $attributes]);
    }
}
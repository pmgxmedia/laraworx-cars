<?php

namespace App\Http\Controllers;

use App\Models\Dealership;
use App\Models\Vehicle;
use App\Models\VehicleCampaign;

class SitemapController extends Controller
{
    public function index()
    {
        $base = url('/');

        $vehicles = Vehicle::published()
            ->select(['slug', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->limit(20000)
            ->get();

        $campaigns = VehicleCampaign::where('status', VehicleCampaign::STATUS_ACTIVE)->select(['slug', 'updated_at'])->get();
        $dealerships = Dealership::where('status', Dealership::STATUS_VERIFIED)->where('is_active', true)->select(['ulid', 'updated_at'])->get();

        $content = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ([
            ['/', now()],
            ['/vehicles', now()],
            ['/campaigns', now()],
            ['/dealerships', now()],
            ['/how-it-works', now()],
            ['/sell-your-car', now()],
            ['/about', now()],
            ['/contact', now()],
        ] as [$loc, $lastmod]) {
            $content .= '  <url><loc>'.$base.$loc.'</loc><lastmod>'.$lastmod->toIso8601String().'</lastmod><changefreq>daily</changefreq><priority>0.9</priority></url>'."\n";
        }

        foreach ($vehicles as $vehicle) {
            $content .= '  <url><loc>'.$base.'/vehicles/'.$vehicle->slug.'</loc><lastmod>'.$vehicle->updated_at?->toIso8601String().'</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>'."\n";
        }

        foreach ($campaigns as $campaign) {
            $content .= '  <url><loc>'.$base.'/campaigns/'.$campaign->slug.'</loc><lastmod>'.$campaign->updated_at?->toIso8601String().'</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>'."\n";
        }

        foreach ($dealerships as $dealership) {
            $content .= '  <url><loc>'.$base.'/dealerships/'.$dealership->ulid.'</loc><lastmod>'.$dealership->updated_at?->toIso8601String().'</lastmod><changefreq>weekly</changefreq><priority>0.6</priority></url>'."\n";
        }

        $content .= '</urlset>';

        return response($content)
            ->header('Content-Type', 'application/xml')
            ->setCache(['public' => true, 'max_age' => 3600]);
    }

    public function robots()
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /my/',
            'Disallow: /admin/',
            'Disallow: /dealer/',
            'Disallow: /claim/',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ]);

        return response($content)->header('Content-Type', 'text/plain');
    }
}
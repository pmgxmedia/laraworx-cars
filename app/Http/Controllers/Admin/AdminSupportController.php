<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Faq;
use App\Models\Page;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSupportController extends Controller
{
    public function pages(): View
    {
        $pages = Page::orderBy('slug')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function editPage(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function updatePage(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $request->input('slug') ?: Str::slug($data['title']);

        if ($request->boolean('is_published') && ! $page->is_published) {
            $data['published_at'] = $page->published_at ?? now();
        }

        $page->update($data);

        AuditLogger::log('page_updated', 'page', $page->id, 'Page updated from admin panel', null, ['slug' => $page->slug]);

        return redirect()->route('admin.pages.index')->with('success', 'Page saved.');
    }

    public function faqs(): View
    {
        $faqs = Faq::orderBy('category')->orderBy('sort_order')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        Faq::create($data + ['category' => $data['category'] ?: 'general']);

        return back()->with('success', 'FAQ added.');
    }

    public function updateFaq(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]));

        return back()->with('success', 'FAQ updated.');
    }

    public function destroyFaq(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'FAQ removed.');
    }

    public function banners(): View
    {
        $banners = Banner::orderByDesc('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function storeBanner(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'position' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,gif', 'max:5120'],
        ]);

        $data['banner_path'] = $request->file('image')->store('banners', 'public');

        Banner::create($data + ['position' => $data['position'] ?: 'homepage']);

        return back()->with('success', 'Banner created.');
    }

    public function destroyBanner(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return back()->with('success', 'Banner removed.');
    }
}
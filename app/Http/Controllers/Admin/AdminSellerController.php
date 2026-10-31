<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateSeller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSellerController extends Controller
{
    public function index(Request $request): View
    {
        $rows = PrivateSeller::query()
            ->with('user')
            ->withCount(['vehicles', 'purchaseClaims'])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->input('q')}%")
                    ->orWhere('surname', 'like', "%{$request->input('q')}%")
                    ->orWhere('email', 'like', "%{$request->input('q')}%"))
                ->orWhere('contact_email', 'like', "%{$request->input('q')}%")
                ->orWhere('city', 'like', "%{$request->input('q')}%")))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('province'), fn ($q) => $q->where('province', $request->input('province')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $filters = [
            'statuses' => PrivateSeller::statuses(),
            'provinces' => PrivateSeller::whereNotNull('province')->distinct()->orderBy('province')->pluck('province'),
        ];

        return view('admin.sellers.index', compact('rows', 'filters'));
    }

    public function create(): View
    {
        return view('admin.sellers.create', ['statuses' => PrivateSeller::statuses()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(array_keys(PrivateSeller::statuses()))],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['contact_email'] = $data['contact_email'] ?? User::find($data['user_id'])?->email;

        $seller = PrivateSeller::create($data);

        AuditLogger::log('seller_created', 'private_seller', $seller->id, 'Private seller created from admin panel');

        return redirect()->route('admin.sellers.show', $seller)->with('success', 'Private seller created successfully.');
    }

    public function show(PrivateSeller $seller): View
    {
        $seller->load(['user', 'vehicles', 'purchaseClaims']);

        return view('admin.sellers.show', compact('seller'));
    }

    public function edit(PrivateSeller $seller): View
    {
        return view('admin.sellers.edit', [
            'seller' => $seller,
            'statuses' => PrivateSeller::statuses(),
        ]);
    }

    public function update(Request $request, PrivateSeller $seller): RedirectResponse
    {
        $seller->update($request->validate([
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(array_keys(PrivateSeller::statuses()))],
            'is_active' => ['nullable', 'boolean'],
        ]));

        AuditLogger::log('seller_updated', 'private_seller', $seller->id, 'Private seller details updated from admin panel');

        return redirect()->route('admin.sellers.show', $seller)->with('success', 'Private seller updated successfully.');
    }

    public function destroy(PrivateSeller $seller): RedirectResponse
    {
        $seller->delete();

        AuditLogger::log('seller_deleted', 'private_seller', $seller->id, 'Private seller removed');

        return redirect()->route('admin.sellers.index')->with('success', 'Private seller removed.');
    }

    public function verify(PrivateSeller $seller): RedirectResponse
    {
        $seller->update([
            'status' => PrivateSeller::STATUS_VERIFIED,
            'verified_at' => now(),
            'is_active' => true,
        ]);

        AuditLogger::log('seller_verified', 'private_seller', $seller->id, 'Private seller verified and activated');

        return back()->with('success', 'Private seller verified and activated.');
    }

    public function suspend(PrivateSeller $seller): RedirectResponse
    {
        $seller->update([
            'status' => PrivateSeller::STATUS_SUSPENDED,
            'is_active' => false,
        ]);

        AuditLogger::log('seller_suspended', 'private_seller', $seller->id, 'Private seller suspended');

        return back()->with('success', 'Private seller suspended.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DealershipApproved;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDealershipController extends Controller
{
    public function index(Request $request): View
    {
        $rows = Dealership::query()
            ->withCount(['vehicles', 'userAccounts', 'purchaseClaims'])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('business_name', 'like', "%{$request->input('q')}%")
                ->orWhere('trading_name', 'like', "%{$request->input('q')}%")
                ->orWhere('email', 'like', "%{$request->input('q')}%")
                ->orWhere('city', 'like', "%{$request->input('q')}%")
                ->orWhere('registration_number', 'like', "%{$request->input('q')}%")))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('province'), fn ($q) => $q->where('province', $request->input('province')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $filters = [
            'statuses' => Dealership::statuses(),
            'provinces' => Dealership::whereNotNull('province')->distinct()->orderBy('province')->pluck('province'),
        ];

        return view('admin.dealerships.index', compact('rows', 'filters'));
    }

    public function create(): View
    {
        return view('admin.dealerships.create', ['statuses' => Dealership::statuses()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dealership = Dealership::create($request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:120'],
            'vat_number' => ['nullable', 'string', 'max:120'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(array_keys(Dealership::statuses()))],
            'is_active' => ['nullable', 'boolean'],
        ]));

        AuditLogger::log('dealership_created', 'dealership', $dealership->id, 'Dealership created from admin panel');

        return redirect()->route('admin.dealerships.show', $dealership)->with('success', 'Dealership created successfully.');
    }

    public function show(Dealership $dealership): View
    {
        $dealership->load(['userAccounts', 'vehicles.commissions']);

        $documents = Document::where('documentable_type', Dealership::class)
            ->where('documentable_id', $dealership->id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dealerships.show', compact('dealership', 'documents'));
    }

    public function edit(Dealership $dealership): View
    {
        return view('admin.dealerships.edit', [
            'dealership' => $dealership,
            'statuses' => Dealership::statuses(),
        ]);
    }

    public function update(Request $request, Dealership $dealership): RedirectResponse
    {
        $dealership->update($request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:120'],
            'vat_number' => ['nullable', 'string', 'max:120'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(array_keys(Dealership::statuses()))],
            'is_active' => ['nullable', 'boolean'],
        ]));

        AuditLogger::log('dealership_updated', 'dealership', $dealership->id, 'Dealership details updated from admin panel');

        return redirect()->route('admin.dealerships.show', $dealership)->with('success', 'Dealership updated successfully.');
    }

    public function destroy(Dealership $dealership): RedirectResponse
    {
        $dealership->delete();

        AuditLogger::log('dealership_deleted', 'dealership', $dealership->id, 'Dealership removed');

        return redirect()->route('admin.dealerships.index')->with('success', 'Dealership removed.');
    }

    public function verify(Dealership $dealership): RedirectResponse
    {
        $dealership->update([
            'status' => Dealership::STATUS_VERIFIED,
            'verified_at' => now(),
            'is_active' => true,
        ]);

        foreach ($dealership->userAccounts as $account) {
            $account->notify(new DealershipApproved($dealership));
        }

        AuditLogger::log('dealership_verified', 'dealership', $dealership->id, 'Dealership verified and activated');

        return back()->with('success', 'Dealership verified and the account users have been notified.');
    }

    public function suspend(Dealership $dealership): RedirectResponse
    {
        $dealership->update([
            'status' => Dealership::STATUS_SUSPENDED,
            'is_active' => false,
        ]);

        AuditLogger::log('dealership_suspended', 'dealership', $dealership->id, 'Dealership suspended');

        return back()->with('success', 'Dealership suspended and deactivated.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyerEnquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $rows = BuyerEnquiry::query()
            ->with(['vehicle', 'user', 'assignedTo'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->input('assigned_to')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$request->input('q')}%")
                ->orWhere('email', 'like', "%{$request->input('q')}%")
                ->orWhere('phone', 'like', "%{$request->input('q')}%")
                ->orWhereHas('vehicle', fn ($v) => $v->where('make', 'like', "%{$request->input('q')}%")
                    ->orWhere('model', 'like', "%{$request->input('q')}%"))))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $admins = User::whereHas('roles', fn ($q) => $q->whereIn('name', [User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS, User::ROLE_SUPPORT]))
            ->orderBy('name')
            ->get();

        return view('admin.enquiries.index', [
            'rows' => $rows,
            'admins' => $admins,
            'statuses' => BuyerEnquiry::statuses(),
        ]);
    }

    public function show(BuyerEnquiry $enquiry): View
    {
        $enquiry->load(['vehicle.dealership', 'vehicle.privateSeller.user', 'user', 'assignedTo']);

        $admins = User::whereHas('roles', fn ($q) => $q->whereIn('name', [User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS, User::ROLE_SUPPORT]))
            ->orderBy('name')
            ->get();

        return view('admin.enquiries.show', [
            'enquiry' => $enquiry,
            'admins' => $admins,
            'statuses' => BuyerEnquiry::statuses(),
        ]);
    }

    public function update(Request $request, BuyerEnquiry $enquiry): RedirectResponse
    {
        $enquiry->update($request->validate([
            'status' => ['required', Rule::in(array_keys(BuyerEnquiry::statuses()))],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'internal_note' => ['nullable', 'string', 'max:5000'],
        ]));

        return back()->with('success', 'Enquiry updated.');
    }
}
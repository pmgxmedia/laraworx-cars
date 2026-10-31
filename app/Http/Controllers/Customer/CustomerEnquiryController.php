<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class CustomerEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = auth()->user()->enquiries()
            ->with(['vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.enquiries', compact('enquiries'));
    }
}
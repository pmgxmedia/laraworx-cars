<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        $contact = [
            'email' => Setting::get('contact.email', 'hello@worxcars.local'),
            'phone' => Setting::get('contact.phone', '+27 11 000 0000'),
            'address' => Setting::get('contact.address', 'Johannesburg, South Africa'),
            'hours' => Setting::get('contact.hours', 'Mon–Fri, 08:00–17:00 SAST'),
        ];

        $seo = [
            'title' => 'Contact Us | WorxCars',
            'description' => 'Get in touch with the WorxCars team — buyer rewards, seller partnerships, support and media enquiries.',
        ];

        return view('contact', compact('contact', 'seo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        \App\Support\AuditLogger::log('contact_message', 'contact', null, 'Website contact form submission', $request->input('email'), ['subject' => $data['subject']]);

        return back()->with('status', 'Thank you for contacting WorxCars. We\'ll respond within one business day.');
    }
}
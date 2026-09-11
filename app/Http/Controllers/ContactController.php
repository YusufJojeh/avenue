<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $recipient = Setting::get('site.contact_email', config('mail.from.address'));

        Mail::to($recipient)->send(new ContactMessageMail($validated));

        return redirect()
            ->route('contact')
            ->with('contact_success', true);
    }
}

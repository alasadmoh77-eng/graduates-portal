<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function show()
    {
        return view('alumni.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $contactMessage = ContactMessage::create($validated);

        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        Notification::send($admins, new NewContactMessage($contactMessage));

        // إرسال الرسالة إلى البريد الإلكتروني الخاص بالإدارة
        if (config('mail.contact_receiver')) {
            Mail::to(config('mail.contact_receiver'))->send(new ContactMessageMail($contactMessage));
        }

        return redirect()->route('alumni.contact')
            ->with('success', __('app.contact_message_sent'));
    }
}

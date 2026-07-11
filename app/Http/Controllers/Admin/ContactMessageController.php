<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        return view('admin.contact-messages.index', compact('messages'));
    }

    public function markRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read_at' => now()]);
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'تم تحديد الرسالة كمقروءة.');
    }
}

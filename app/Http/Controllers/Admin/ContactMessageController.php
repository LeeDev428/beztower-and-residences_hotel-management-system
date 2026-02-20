<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'LIKE', "%{$s}%")
                  ->orWhere('email', 'LIKE', "%{$s}%")
                  ->orWhere('subject', 'LIKE', "%{$s}%")
                  ->orWhere('message', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_read', $request->status === 'read');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $messages = $query->paginate(15)->withQueryString();

        $stats = [
            'total'  => ContactMessage::count(),
            'unread' => ContactMessage::where('is_read', false)->count(),
            'read'   => ContactMessage::where('is_read', true)->count(),
        ];

        return view('admin.contact-messages.index', compact('messages', 'stats'));
    }

    public function markRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => true]);
        return back()->with('success', 'Message marked as read.');
    }

    public function markUnread(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => false]);
        return back()->with('success', 'Message marked as unread.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return back()->with('success', 'Message deleted.');
    }
}

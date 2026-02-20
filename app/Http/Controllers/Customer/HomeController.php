<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['roomType', 'amenities', 'photos'])
            ->whereIn('status', ['available', 'occupied']) // Exclude dirty and maintenance
            ->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'rooms' => view('customer.home.partials.room-cards', compact('rooms'))->render(),
                'pagination' => view('customer.home.partials.pagination', compact('rooms'))->render()
            ]);
        }

        return view('customer.home', compact('rooms'));
    }

    public function about()
    {
        return view('customer.about');
    }

    public function services()
    {
        return view('customer.services');
    }

    public function contact()
    {
        return view('customer.contact');
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Always save to database so admin can view it
        ContactMessage::create($validated);

        // Also send email notification (non-blocking)
        try {
            Mail::raw(
                "Name: {$validated['name']}\nEmail: {$validated['email']}\n" .
                (!empty($validated['phone']) ? "Phone: {$validated['phone']}\n" : '') .
                "\nMessage:\n{$validated['message']}",
                function ($mail) use ($validated) {
                    $mail->to(config('mail.from.address'))
                         ->replyTo($validated['email'], $validated['name'])
                         ->subject('Contact Form: ' . $validated['subject']);
                }
            );
        } catch (\Exception $e) {
            // Email failure does not prevent the message from being saved
        }

        return back()->with('success', 'Your message has been sent! We will get back to you soon.');
    }
}


<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['roomType', 'amenities', 'photos'])
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
}


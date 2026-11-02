<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:upcoming,completed,cancelled'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Booking::with(['user', 'service']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);
        $services = Service::orderBy('title')->get();

        return view('admin.bookings.index', compact('bookings', 'services'));
    }

    public function create()
    {
        return redirect()->route('bookings.create');
    }

    public function store(Request $request)
    {
        abort(405);
    }

    public function show(string $id)
    {
        $booking = Booking::with(['user', 'service'])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(string $id)
    {
        return redirect()->route('admin.bookings.show', Booking::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:upcoming,completed,cancelled',
        ]);

        $booking->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking status updated successfully!');
    }

    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully!');
    }
}

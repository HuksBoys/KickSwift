<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display admin control dashboard with statistics and booking management.
     */
    public function index(Request $request)
    {
        // Pastikan hanya admin yang bisa mengakses
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Akses ditolak. Halaman khusus Admin.');
        }

        $today = Carbon::today()->format('Y-m-d');
        $statusFilter = $request->query('status');
        $dateFilter = $request->query('date', $today);
        $fieldFilter = $request->query('field_id');

        $query = Booking::with(['field', 'user'])->orderBy('booking_date', 'desc')->orderBy('start_time', 'asc');

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($dateFilter) {
            $query->where('booking_date', $dateFilter);
        }

        if ($fieldFilter) {
            $query->where('field_id', $fieldFilter);
        }

        $bookings = $query->paginate(15)->withQueryString();

        // Statistik Dashboard
        $stats = [
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),
            'today_revenue' => Booking::where('payment_status', 'paid')->where('booking_date', $today)->sum('total_price'),
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::where('booking_date', $today)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_fields' => Field::where('is_active', true)->count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        $fields = Field::all();

        return view('admin.dashboard', compact('bookings', 'stats', 'fields', 'statusFilter', 'dateFilter', 'fieldFilter'));
    }

    /**
     * Update booking status from admin panel.
     */
    public function updateStatus(string $bookingCode, Request $request): RedirectResponse
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();
        $status = $request->input('status');

        if (in_array($status, ['confirmed', 'completed', 'cancelled'])) {
            $updateData = ['status' => $status];
            if ($status === 'completed' && $booking->payment_status !== 'paid') {
                $updateData['payment_status'] = 'paid';
                $updateData['paid_at'] = now();
            }
            $booking->update($updateData);

            return back()->with('success', "Status booking {$booking->booking_code} berhasil diperbarui menjadi {$status}.");
        }

        return back()->with('error', 'Status tidak valid.');
    }
}

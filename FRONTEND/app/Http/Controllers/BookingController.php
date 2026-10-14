<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use App\Services\BookingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Store a new booking from the web interface.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'duration_hours' => 'required|integer|min:1|max:5',
            'payment_method' => 'nullable|string|in:qris,bank_transfer,cash',
            'notes' => 'nullable|string|max:500',
        ]);

        // Pastikan ada user (jika belum login, gunakan customer demo default)
        $user = Auth::user();
        if (!$user) {
            $user = User::where('role', 'customer')->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Tamu KickSwift',
                    'email' => 'tamu_' . uniqid() . '@kickswift.com',
                    'role' => 'customer',
                    'password' => bcrypt('password'),
                ]);
            }
            Auth::login($user);
        }

        try {
            $booking = $this->bookingService->createBooking($user, $validated);

            return redirect()
                ->route('bookings.show', $booking->booking_code)
                ->with('success', 'Reservasi berhasil dibuat! Silakan selesaikan pembayaran.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display booking receipt and payment page.
     */
    public function show(string $bookingCode)
    {
        $booking = Booking::with(['field', 'user'])
            ->where('booking_code', $bookingCode)
            ->firstOrFail();

        return view('bookings.receipt', compact('booking'));
    }

    /**
     * Simulate payment completion (QRIS / Instant Verification).
     */
    public function pay(string $bookingCode, Request $request): RedirectResponse
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        $paymentMethod = $request->input('payment_method', 'qris');
        $this->bookingService->simulatePayment($booking, $paymentMethod);

        return redirect()
            ->route('bookings.show', $booking->booking_code)
            ->with('success', 'Pembayaran berhasil dikonfirmasi! Jadwal Anda telah terkunci.');
    }

    /**
     * Cancel a pending booking.
     */
    public function cancel(string $bookingCode): RedirectResponse
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        if ($booking->status === 'completed') {
            return back()->with('error', 'Booking yang sudah selesai tidak dapat dibatalkan.');
        }

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => ($booking->payment_status === 'paid') ? 'refunded' : 'failed',
        ]);

        return back()->with('info', 'Pemesanan telah dibatalkan.');
    }

    /**
     * View current user's booking history.
     */
    public function history()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('info', 'Silakan login untuk melihat riwayat booking.');
        }

        $bookings = Booking::with('field')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.history', compact('bookings'));
    }

    /**
     * API: Calculate total price breakdown.
     */
    public function apiCalculatePrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'booking_date' => 'required|date',
            'start_time' => 'required|string',
            'duration_hours' => 'required|integer|min:1|max:8',
        ]);

        $field = Field::findOrFail($validated['field_id']);
        $pricing = $this->bookingService->calculatePrice(
            $field,
            $validated['booking_date'],
            $validated['start_time'],
            $validated['duration_hours']
        );

        return response()->json([
            'status' => 'success',
            'data' => $pricing,
        ]);
    }
}

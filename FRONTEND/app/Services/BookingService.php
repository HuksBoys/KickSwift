<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Get 1-hour time slots for a field on a specific date, with availability status.
     *
     * @param Field $field
     * @param string $date Y-m-d format
     * @return array
     */
    public function getAvailableSlots(Field $field, string $date): array
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ...

        // Ambil template jadwal operasional untuk hari tersebut
        $schedules = Schedule::where('field_id', $field->id)
            ->where(function ($query) use ($dayOfWeek, $date) {
                $query->where('day_of_week', $dayOfWeek)
                      ->orWhere('date', $date);
            })
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();

        // Ambil booking yang aktif pada tanggal tersebut (pending & confirmed)
        $existingBookings = Booking::where('field_id', $field->id)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            $slotStart = Carbon::parse($schedule->start_time)->format('H:i');
            $slotEnd = Carbon::parse($schedule->end_time)->format('H:i');

            // Cek apakah jam ini overlap dengan booking yang ada
            $isBooked = $existingBookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                $bookingStart = Carbon::parse($booking->start_time)->format('H:i');
                $bookingEnd = Carbon::parse($booking->end_time)->format('H:i');

                // Overlap check: slotStart < bookingEnd && slotEnd > bookingStart
                return ($slotStart < $bookingEnd && $slotEnd > $bookingStart);
            });

            // Tentukan harga (jika ada custom_price untuk peak hour, gunakan itu)
            $price = $schedule->custom_price ?? $field->price_per_hour;

            $slots[] = [
                'schedule_id' => $schedule->id,
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'price' => (float) $price,
                'formatted_price' => 'Rp ' . number_format($price, 0, ',', '.'),
                'is_peak' => ($schedule->custom_price !== null),
                'is_available' => !$isBooked,
                'status' => $isBooked ? 'booked' : 'available',
            ];
        }

        return $slots;
    }

    /**
     * Calculate total price for given date, start time, and duration.
     *
     * @param Field $field
     * @param string $date
     * @param string $startTime H:i:s or H:i
     * @param int $durationHours
     * @return array
     */
    public function calculatePrice(Field $field, string $date, string $startTime, int $durationHours): array
    {
        $start = Carbon::parse($startTime);
        $totalPrice = 0;
        $slotBreakdown = [];

        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek;

        for ($i = 0; $i < $durationHours; $i++) {
            $slotStart = $start->copy()->addHours($i)->format('H:i:00');
            $slotEnd = $start->copy()->addHours($i + 1)->format('H:i:00');

            $schedule = Schedule::where('field_id', $field->id)
                ->where(function ($query) use ($dayOfWeek, $date) {
                    $query->where('day_of_week', $dayOfWeek)
                          ->orWhere('date', $date);
                })
                ->where('start_time', $slotStart)
                ->first();

            $hourPrice = ($schedule && $schedule->custom_price) 
                ? (float) $schedule->custom_price 
                : (float) $field->price_per_hour;

            $totalPrice += $hourPrice;
            $slotBreakdown[] = [
                'hour' => ($i + 1),
                'time' => substr($slotStart, 0, 5) . ' - ' . substr($slotEnd, 0, 5),
                'price' => $hourPrice,
                'formatted_price' => 'Rp ' . number_format($hourPrice, 0, ',', '.'),
            ];
        }

        return [
            'base_price_per_hour' => (float) $field->price_per_hour,
            'duration_hours' => $durationHours,
            'total_price' => (float) $totalPrice,
            'formatted_total' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            'slots' => $slotBreakdown,
        ];
    }

    /**
     * Create a new booking with database locking to prevent double booking race conditions.
     *
     * @param User $user
     * @param array $data
     * @return Booking
     * @throws Exception
     */
    public function createBooking(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $field = Field::findOrFail($data['field_id']);
            $date = $data['booking_date'];
            $startTime = Carbon::parse($data['start_time'])->format('H:i:00');
            $duration = (int) ($data['duration_hours'] ?? 1);
            $endTime = Carbon::parse($startTime)->addHours($duration)->format('H:i:00');

            // Cek ketersediaan dengan PESSIMISTIC LOCKING pada bookings
            $hasConflict = Booking::where('field_id', $field->id)
                ->where('booking_date', $date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw new Exception('Maaf, slot lapangan pada jam tersebut sudah dipesan oleh pengguna lain.');
            }

            // Hitung total harga
            $pricing = $this->calculatePrice($field, $date, $startTime, $duration);

            $booking = Booking::create([
                'booking_code' => 'KS-' . Carbon::parse($date)->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => $user->id,
                'field_id' => $field->id,
                'booking_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_hours' => $duration,
                'price_per_hour' => $field->price_per_hour,
                'total_price' => $pricing['total_price'],
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $data['payment_method'] ?? 'qris',
                'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
                'notes' => $data['notes'] ?? null,
            ]);

            return $booking;
        });
    }

    /**
     * Process mock payment for prototype checkout simulation.
     *
     * @param Booking $booking
     * @param string $paymentMethod
     * @return Booking
     */
    public function simulatePayment(Booking $booking, string $paymentMethod = 'qris'): Booking
    {
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        return $booking;
    }
}

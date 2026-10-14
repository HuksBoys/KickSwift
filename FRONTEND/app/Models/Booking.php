<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_code',
        'user_id',
        'field_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'price_per_hour',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'duration_hours' => 'integer',
        'price_per_hour' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Boot model to automatically generate booking code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'KS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    /**
     * Booking belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booking belongs to a field.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}

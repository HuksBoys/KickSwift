<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'field_id',
        'day_of_week',
        'date',
        'start_time',
        'end_time',
        'custom_price',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'date' => 'date',
        'custom_price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Schedule belongs to a field.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display field details and interactive booking interface.
     */
    public function show(string $slug, Request $request)
    {
        $field = Field::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $selectedDate = $request->query('date', Carbon::today()->format('Y-m-d'));

        $slots = $this->bookingService->getAvailableSlots($field, $selectedDate);

        return view('fields.show', compact('field', 'selectedDate', 'slots'));
    }

    /**
     * API: Get available slots JSON for dynamic date picker switching.
     */
    public function apiSlots(string $slug, Request $request): JsonResponse
    {
        $field = Field::where('slug', $slug)->firstOrFail();
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));

        $slots = $this->bookingService->getAvailableSlots($field, $date);

        return response()->json([
            'status' => 'success',
            'field' => [
                'id' => $field->id,
                'name' => $field->name,
                'slug' => $field->slug,
                'price_per_hour' => $field->price_per_hour,
            ],
            'date' => $date,
            'slots' => $slots,
        ]);
    }

    /**
     * API: List all fields.
     */
    public function apiIndex(): JsonResponse
    {
        $fields = Field::where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => $fields,
        ]);
    }
}

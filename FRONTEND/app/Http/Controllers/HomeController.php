<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the main landing page with available futsal fields.
     */
    public function index(Request $request)
    {
        $typeFilter = $request->query('type');

        $query = Field::where('is_active', true);

        if ($typeFilter && in_array($typeFilter, ['vinyl', 'synthetic_grass', 'interlocking'])) {
            $query->where('type', $typeFilter);
        }

        $fields = $query->get();

        return view('home', compact('fields', 'typeFilter'));
    }
}

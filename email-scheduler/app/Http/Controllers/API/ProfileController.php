<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstName' => 'required|string|max:150',
            'lastName' => 'required|string|max:250',
            'email' => 'required|email|unique:profiles,email',
            'tel' => 'nullable|string|max:255',
            'birthDay' => 'nullable|date',
            'sexe' => 'nullable|string|max:120',
        ]);

        $profile = Profile::create($validatedData);

        return response()->json(['profile' => $profile], 201);
    }
}

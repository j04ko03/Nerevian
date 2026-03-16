<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationRequestController extends Controller
{
    /**
     * Show the registration request form.
     */
    public function create(): Response
    {
        return Inertia::render('auth/RegistrationRequest');
    }

    /**
     * Store a new registration request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        RegistrationRequest::create($validated);

        return redirect()->route('registration-request.success');
    }

    /**
     * Show the success page after submitting a registration request.
     */
    public function success(): Response
    {
        return Inertia::render('auth/RegistrationRequestSent');
    }
}

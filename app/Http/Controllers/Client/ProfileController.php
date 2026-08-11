<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the client profile.
     */
    public function edit(): View
    {
        return view('client.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update the client profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
            'notifications_email' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $settings = $user->settings ?? [];
        $settings['notifications']['email'] = $request->boolean('notifications_email');
        $user->settings = $settings;

        $user->save();

        return redirect()->route('client.profile.edit')
            ->with('success', 'Perfil actualizado con éxito.');
    }
}

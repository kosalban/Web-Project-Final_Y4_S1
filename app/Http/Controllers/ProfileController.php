<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('settings.index', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', __('Profile updated successfully.'));
    }

    public function updateTheme(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        $request->user()->update($data);

        return back()->with('success', __('Theme updated successfully.'));
    }

    public function updateLocale(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:en,km'],
        ]);

        if ($request->user() !== null) {
            $request->user()->update($data);
        } else {
            $request->session()->put('locale', $data['locale']);
        }

        return back()->with('success', __('Language preference updated successfully.'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        return view('fitlife.profile', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('fitlife.profile-edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'github_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'avatar_url' => 'nullable|url|max:255',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:30720',
        ], [
            'avatar_file.max' => 'Image file too big',
        ]);

        $data = $validated;

        // Handle Avatar File Upload
        if ($request->hasFile('avatar_file')) {
            $path = $request->file('avatar_file')->store('avatars', 'public');
            $data['avatar'] = asset('storage/' . $path);
        } elseif (!empty($validated['avatar_url'])) {
            $data['avatar'] = $validated['avatar_url'];
        }

        // Remove temporary validation keys from data
        unset($data['avatar_url']);
        unset($data['avatar_file']);

        $user->update($data);

        return redirect()->route('profile.show', $user->id)->with('success', 'Profile updated successfully!');
    }
}

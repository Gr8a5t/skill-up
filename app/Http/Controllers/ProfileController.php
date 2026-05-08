<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

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

        // Handle Avatar File Upload with Resizing
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $filename = time() . '.webp'; // Convert to WebP for excellent compression
            $path = 'avatars/' . $filename;

            // Initialize Intervention Image Manager
            $manager = new ImageManager(new Driver());
            
            // Read, Resize (Cover 500x500), and Encode
            $image = $manager->decode($file);
            $image->cover(500, 500);
            $encoded = $image->encodeUsingFileExtension('webp', 80); // Encode as WebP with 80% quality
            
            // Save to public storage
            Storage::disk('public')->put($path, (string) $encoded);
            
            $data['avatar'] = asset('storage/' . $path);
        } elseif (!empty($validated['avatar_url'])) {
            $data['avatar'] = $validated['avatar_url'];
        }

        // Remove temporary validation keys from data
        unset($data['avatar_url']);
        unset($data['avatar_file']);

        $user->update($data);

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully!');
    }
}

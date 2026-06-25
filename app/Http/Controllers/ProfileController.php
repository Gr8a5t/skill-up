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
        return \Inertia\Inertia::render('Profile/Show', [
            'user' => $user,
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
        return \Inertia\Inertia::render('Profile/Edit', [
            'user' => $user,
        ]);
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
            $file = $request->file('avatar_file');

            $cloudinaryConfig = config('services.cloudinary');
            $hasCloudinary = !empty($cloudinaryConfig['url']) || 
                (!empty($cloudinaryConfig['cloud_name']) && 
                 !empty($cloudinaryConfig['api_key']) && 
                 !empty($cloudinaryConfig['api_secret']));

            if ($hasCloudinary) {
                try {
                    $cloudinary = app(\App\Services\CloudinaryService::class);
                    $data['avatar'] = $cloudinary->upload($file->getRealPath(), 'avatars', [
                        'transformation' => [
                            'width' => 500,
                            'height' => 500,
                            'crop' => 'fill',
                            'gravity' => 'face',
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]);
                } catch (\Exception $e) {
                    logger()->error('Cloudinary upload failed, falling back to local storage: ' . $e->getMessage());
                    $data['avatar'] = $this->storeLocal($file);
                }
            } else {
                $data['avatar'] = $this->storeLocal($file);
            }
        } elseif ($request->filled('avatar_url')) {
            $data['avatar'] = $request->avatar_url;
        }

        // Clean up internal data
        unset($data['avatar_url'], $data['avatar_file']);

        $user->update($data);

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully!');
    }

    /**
     * Store the uploaded file locally.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    protected function storeLocal($file): string
    {
        if (extension_loaded('gd') || extension_loaded('imagick')) {
            $filename = time() . '.webp';
            $path = 'avatars/' . $filename;

            $manager = new ImageManager(new Driver());
            $image = $manager->decode($file);
            $image->cover(500, 500);
            $encoded = $image->encodeUsingFileExtension('webp', 80);
            
            Storage::disk('public')->put($path, (string) $encoded);
            return '/storage/' . $path;
        }

        // Fallback: simply store the uploaded file if image driver is missing
        $path = $file->store('avatars', 'public');
        return '/storage/' . $path;
    }
}

<?php

use App\Services\CloudinaryService;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cloudinary service can be instantiated when configured', function () {
    config()->set('services.cloudinary', [
        'cloud_name' => 'test_cloud',
        'api_key' => 'test_key',
        'api_secret' => 'test_secret',
    ]);

    $service = new CloudinaryService();
    expect($service)->toBeInstanceOf(CloudinaryService::class);
});

test('profile update handles missing cloudinary config by falling back to local', function () {
    config()->set('services.cloudinary', [
        'url' => null,
        'cloud_name' => null,
        'api_key' => null,
        'api_secret' => null,
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

    $response = $this->put(route('profile.update'), [
        'name' => 'Updated Name',
        'avatar_file' => $file,
    ]);

    $response->assertRedirect();
    $user->refresh();
    
    // Check that it fell back to local storage
    expect($user->avatar)->toStartWith('/storage/avatars/');
    
    // Clean up local file
    $localPath = public_path($user->avatar);
    if (file_exists($localPath)) {
        unlink($localPath);
    }
});

test('profile update uploads to cloudinary when configured', function () {
    config()->set('services.cloudinary', [
        'cloud_name' => 'test_cloud',
        'api_key' => 'test_key',
        'api_secret' => 'test_secret',
    ]);

    $mockUrl = 'https://res.cloudinary.com/test_cloud/image/upload/v12345/avatars/test.jpg';

    // Mock the CloudinaryService
    $this->mock(CloudinaryService::class, function ($mock) use ($mockUrl) {
        $mock->shouldReceive('upload')
            ->once()
            ->andReturn($mockUrl);
    });

    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

    $response = $this->put(route('profile.update'), [
        'name' => 'Updated Name',
        'avatar_file' => $file,
    ]);

    $response->assertRedirect();
    $user->refresh();
    
    // Check that it used the mock Cloudinary URL
    expect($user->avatar)->toBe($mockUrl);
});

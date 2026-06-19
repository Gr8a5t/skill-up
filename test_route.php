<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
if (!$user) { echo "No user found\n"; exit; }

$course = App\Models\Course::first();
if (!$course) { echo "No course found\n"; exit; }

$request = Illuminate\Http\Request::create('/courses/' . $course->slug . '/learn', 'GET');
$request->setUserResolver(function () use ($user) { return $user; });
app()->instance('request', $request);
auth()->login($user);

$response = app()->handle($request);
$content = $response->getContent();

if (strpos($content, 'window.openSkillUpWorkspace') !== false) {
    echo "SUCCESS: Script is in the rendered HTML response!\n";
} else {
    echo "FAIL: Script is MISSING from the rendered HTML response!\n";
}

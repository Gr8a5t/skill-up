<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$course = ['title' => 'Test Course', 'id' => 1];
$view = view('components.code-workspace')->render();
if (strpos($view, 'window.openSkillUpWorkspace') !== false) {
    echo "SCRIPT IS IN THE VIEW OUTPUT!\n";
} else {
    echo "SCRIPT IS MISSING FROM THE VIEW OUTPUT!\n";
}

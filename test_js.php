<?php
$content = file_get_contents(__DIR__ . '/resources/views/dashboard/bar-keeper-dashboard.blade.php');
preg_match_all('/<script>(.*?)<\/script>/s', $content, $matches);

foreach ($matches[1] as $i => $js) {
    // Replace all Blade echo statements with empty strings
    $js = preg_replace('/\{\{.*?\}\}/', '""', $js);
    // Replace all Blade @json directives with empty arrays
    $js = preg_replace('/@json\(.*?\)/', '[]', $js);
    // Replace all other Blade directives with empty space
    $js = preg_replace('/@[a-zA-Z]+\s*(\(.*?\))?/', '', $js);

    file_put_contents(__DIR__ . "/test_{$i}.js", $js);
    exec("node -c " . escapeshellarg(__DIR__ . "/test_{$i}.js") . " 2>&1", $output, $return_var);
    if ($return_var !== 0) {
        echo "Error in script block $i:\n" . implode("\n", $output) . "\n";
        exit(1);
    }
}
echo "All scripts passed syntax check locally!\n";

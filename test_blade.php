<?php
$content = file_get_contents(__DIR__ . '/resources/views/dashboard/bar-keeper-dashboard.blade.php');
if (preg_match('/@section\(\'scripts\'\)(.*?)@endsection/s', $content, $matches)) {
    // Replace blade syntax with mock data so node can parse it
    $js = $matches[1];
    $js = preg_replace('/\{\{.*?\}\}/', '""', $js);
    $js = preg_replace('/@json\(.*?\)/', '[]', $js);
    $js = preg_replace('/@if.*?@endif/s', '', $js);

    file_put_contents(__DIR__ . '/test.js', $js);
    exec('node -c ' . __DIR__ . '/test.js 2>&1', $output, $return_var);
    echo "Node syntax check:\n" . implode("\n", $output);
} else {
    echo "Could not find @section('scripts')";
}

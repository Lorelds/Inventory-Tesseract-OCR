<?php

$directories = [
    __DIR__ . '/app/Http/Controllers',
];

function process_file($filepath) {
    $content = file_get_contents($filepath);
    $original_content = $content;
    
    // Replace with('success', 'String') to with('success', __('String'))
    // Using a regex: with\(\s*'([^']+)'\s*,\s*'([^']+)'\s*\)
    // To only match if the first param is 'success', 'info', 'error', 'warning'
    $content = preg_replace_callback(
        "/with\(\s*'((?:success|error|info|warning))'\s*,\s*'([^']+)'\s*\)/",
        function ($matches) {
            $type = $matches[1];
            $message = $matches[2];
            // Don't wrap if it's already wrapped, though the regex above wouldn't match __('...') because of the inner quotes, but just in case.
            return "with('{$type}', __('{$message}'))";
        },
        $content
    );
    
    if ($content !== $original_content) {
        file_put_contents($filepath, $content);
        echo "Updated $filepath\n";
    }
}

foreach ($directories as $directory) {
    if (is_dir($directory)) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                process_file($file->getPathname());
            }
        }
    }
}

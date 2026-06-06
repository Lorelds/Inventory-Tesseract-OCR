<?php

$models = [
    'Product', 'Store', 'Receipt', 'Debt', 'DebtPayment', 'StockMovement'
];

foreach ($models as $modelName) {
    $path = __DIR__ . "/app/Models/{$modelName}.php";
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Check if Tenantable is already used
        if (strpos($content, 'use App\Traits\Tenantable;') === false) {
            // Insert use statement after namespace or other uses
            // The easiest way is to insert it before "class $modelName"
            $content = preg_replace(
                "/(class\s+{$modelName}\s+extends\s+Model\s*\{)/",
                "use App\\Traits\\Tenantable;\n\n$1\n    use Tenantable;\n",
                $content
            );
            file_put_contents($path, $content);
            echo "Added Tenantable to $modelName\n";
        }
    }
}

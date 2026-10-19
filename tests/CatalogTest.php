<?php

require_once __DIR__ . '/../src/autoload.php';

use CardVault\Services\CatalogService;

echo "=== Running CatalogTest ===\n";

$service = new CatalogService();

// 1. Get full catalog
$catalog = $service->getCatalog();
assert(is_array($catalog), "Catalog must return an array");
assert(count($catalog) >= 3, "Catalog must have at least 3 categories");
echo "[PASS] Catalog contains " . count($catalog) . " categories\n";

// 2. Check structure of category and platform
$firstCategory = $catalog[0];
assert(isset($firstCategory['name']), "Category must have name");
assert(isset($firstCategory['slug']), "Category must have slug");
assert(isset($firstCategory['platforms']) && is_array($firstCategory['platforms']), "Category must have platforms array");

$platforms = $firstCategory['platforms'];
assert(count($platforms) > 0, "First category should have platforms");
$firstPlatform = $platforms[0];
assert(isset($firstPlatform['name']), "Platform must have name");
assert(isset($firstPlatform['region']), "Platform must have region badge");
assert(isset($firstPlatform['denominations']) && is_array($firstPlatform['denominations']), "Platform must have denominations array");

$denominations = $firstPlatform['denominations'];
assert(count($denominations) > 0, "Platform should have denominations");
$firstDenom = $denominations[0];
assert(isset($firstDenom['stock_count']), "Denomination must have stock_count");
assert($firstDenom['stock_count'] > 0, "Denomination should have in-stock vouchers");
echo "[PASS] Catalog structure verified with Region, Denominations, and stock_count: {$firstDenom['stock_count']}\n";

// 3. Filter catalog by Category Slug ('streaming')
$streamingCatalog = $service->getCatalogByCategory('streaming');
assert(count($streamingCatalog) === 1, "Expected 1 category for 'streaming'");
assert($streamingCatalog[0]['slug'] === 'streaming', "Category slug must match 'streaming'");
assert($streamingCatalog[0]['platforms'][0]['name'] === 'Netflix', "Streaming platform must be Netflix");
echo "[PASS] Filter by category ('streaming') returns Netflix exclusively\n";

echo "=== CatalogTest: ALL ASSERTIONS PASSED ===\n";

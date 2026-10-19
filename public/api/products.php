<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../src/autoload.php';

use CardVault\Services\CatalogService;

try {
    $service = new CatalogService();
    $category = $_GET['category'] ?? null;

    if ($category) {
        $catalog = $service->getCatalogByCategory($category);
    } else {
        $catalog = $service->getCatalog();
    }

    echo json_encode([
        'status' => 'success',
        'data' => $catalog,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}

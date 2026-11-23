<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/FriendshipRoutes.php';
require_once __DIR__ . '/rest/routes/PostRoutes.php';
require_once __DIR__ . '/rest/routes/TemplateRoutes.php';
require_once __DIR__ . '/rest/routes/StyleRoutes.php';
require_once __DIR__ . '/rest/routes/CvRoutes.php';
require_once __DIR__ . '/rest/routes/TemplateStyleRoutes.php';
require_once __DIR__ . '/rest/routes/JobRoutes.php';

foreach (['/api/v1', '/api'] as $prefix) {
    registerUserRoutes($prefix);
    registerFriendshipRoutes($prefix);
    registerPostRoutes($prefix);
    registerTemplateRoutes($prefix);
    registerStyleRoutes($prefix);
    registerCvRoutes($prefix);
    registerTemplateStyleRoutes($prefix);
    registerJobRoutes($prefix);
}

\Flight::map('notFound', function () {
    \Flight::json(['error' => 'Endpoint not found.'], 404);
});

Flight::start();
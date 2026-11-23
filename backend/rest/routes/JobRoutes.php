<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/JobSearchService.php';

function registerJobRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/jobs', function () {
        RouteHelper::handle(function () {
            $service = new JobSearchService();
            \Flight::json(['data' => $service->search($_GET)], 200);
        });
    });
}

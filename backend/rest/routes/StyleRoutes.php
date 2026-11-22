<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/StyleService.php';

function registerStyleRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/styles', fn() => RouteHelper::handle(function () {
        $service = new StyleService();
        \Flight::json(['data' => $service->getAll()], 200);
    }));
    \Flight::route('GET ' . $prefix . '/styles/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new StyleService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/styles', function () {
        RouteHelper::handle(function () {
            $service = new StyleService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/styles/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new StyleService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/styles/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new StyleService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/PostService.php';

function registerPostRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/posts', fn() => RouteHelper::handle(function () {
        $service = new PostService();
        \Flight::json(['data' => $service->getAll()], 200);
    }));
    \Flight::route('GET ' . $prefix . '/posts/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new PostService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/posts', function () {
        RouteHelper::handle(function () {
            $service = new PostService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/posts/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new PostService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/posts/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new PostService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

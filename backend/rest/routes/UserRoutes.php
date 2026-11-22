<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/UserService.php';

function registerUserRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/users', function () {
        RouteHelper::handle(function () {
            $service = new UserService();
            \Flight::json(['data' => $service->getAll()], 200);
        });
    });
    \Flight::route('GET ' . $prefix . '/users/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new UserService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/users', function () {
        RouteHelper::handle(function () {
            $service = new UserService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/users/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new UserService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/users/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new UserService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

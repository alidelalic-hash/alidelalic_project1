<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/CvService.php';

function registerCvRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/cvs', fn() => RouteHelper::handle(function () {
        $service = new CvService();
        \Flight::json(['data' => $service->getAll()], 200);
    }));
    \Flight::route('GET ' . $prefix . '/cvs/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new CvService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/cvs', function () {
        RouteHelper::handle(function () {
            $service = new CvService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/cvs/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new CvService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/cvs/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new CvService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

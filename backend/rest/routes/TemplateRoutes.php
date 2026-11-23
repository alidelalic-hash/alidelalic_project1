<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/TemplateService.php';

function registerTemplateRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/templates', fn() => RouteHelper::handle(function () {
        $service = new TemplateService();
        \Flight::json(['data' => $service->getAll()], 200);
    }));
    \Flight::route('GET ' . $prefix . '/templates/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new TemplateService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/templates', function () {
        RouteHelper::handle(function () {
            $service = new TemplateService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/templates/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new TemplateService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/templates/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new TemplateService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

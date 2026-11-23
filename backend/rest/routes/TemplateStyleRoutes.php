<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/TemplateStyleService.php';

function registerTemplateStyleRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/template-styles', function () {
        RouteHelper::handle(function () {
            $service = new TemplateStyleService();
            \Flight::json(['data' => $service->getAll()], 200);
        });
    });
    \Flight::route('GET ' . $prefix . '/template-styles/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new TemplateStyleService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/template-styles', function () {
        RouteHelper::handle(function () {
            $service = new TemplateStyleService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/template-styles/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new TemplateStyleService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/template-styles/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new TemplateStyleService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

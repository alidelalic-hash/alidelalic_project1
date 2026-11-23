<?php

require_once __DIR__ . '/RouteHelper.php';
require_once __DIR__ . '/../services/FriendshipService.php';

function registerFriendshipRoutes($prefix)
{
    \Flight::route('GET ' . $prefix . '/friendships', function () {
        RouteHelper::handle(function () {
            $service = new FriendshipService();
            \Flight::json(['data' => $service->getAll()], 200);
        });
    });
    \Flight::route('GET ' . $prefix . '/friendships/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new FriendshipService();
            $record = $service->getById($id);
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('POST ' . $prefix . '/friendships', function () {
        RouteHelper::handle(function () {
            $service = new FriendshipService();
            \Flight::json(['data' => $service->create(RouteHelper::readJson())], 201);
        });
    });
    \Flight::route('PUT ' . $prefix . '/friendships/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new FriendshipService();
            $record = $service->update($id, RouteHelper::readJson());
            \Flight::json(['data' => $record], $record === null ? 404 : 200);
        });
    });
    \Flight::route('DELETE ' . $prefix . '/friendships/@id', function ($id) {
        RouteHelper::handle(function () use ($id) {
            RouteHelper::validateId($id);
            $service = new FriendshipService();
            $deleted = $service->delete($id);
            \Flight::json(['deleted' => $deleted], $deleted ? 200 : 404);
        });
    });
}

<?php

class RouteHelper
{
    public static function validateId($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            throw new InvalidArgumentException('Invalid record ID.');
        }
    }

    public static function readJson()
    {
        $rawBody = file_get_contents('php://input');
        if ($rawBody === '' || $rawBody === false) {
            return [];
        }

        $data = json_decode($rawBody, true);
        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Request body must be valid JSON.');
        }

        return $data;
    }

    public static function handle(callable $action)
    {
        try {
            $action();
        } catch (InvalidArgumentException $exception) {
            \Flight::json([
                'error' => $exception->getMessage()
            ], 400);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            \Flight::json([
                'error' => 'Internal server error.'
            ], 500);
        }
    }
}

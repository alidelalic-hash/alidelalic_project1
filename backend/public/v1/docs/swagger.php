<?php

header('Content-Type: application/json; charset=utf-8');

$schemas = [
    'User' => [
        'type' => 'object',
        'required' => ['name', 'role', 'username'],
        'properties' => [
            'id' => ['type' => 'integer'],
            'name' => ['type' => 'string'],
            'role' => ['type' => 'string', 'enum' => ['admin', 'moderator', 'member']],
            'username' => ['type' => 'string'],
            'created_at' => ['type' => 'string']
        ]
    ],
    'Friendship' => [
        'type' => 'object',
        'required' => ['user_Id_1', 'user_Id_2', 'status'],
        'properties' => [
            'relation_id' => ['type' => 'integer'],
            'user_Id_1' => ['type' => 'integer'],
            'user_Id_2' => ['type' => 'integer'],
            'status' => ['type' => 'string', 'enum' => ['pending', 'accepted', 'blocked']]
        ]
    ],
    'Post' => [
        'type' => 'object',
        'required' => ['title', 'body', 'user_id', 'status'],
        'properties' => [
            'id' => ['type' => 'integer'],
            'title' => ['type' => 'string'],
            'body' => ['type' => 'string'],
            'user_id' => ['type' => 'integer'],
            'status' => ['type' => 'string', 'enum' => ['draft', 'published', 'archived']],
            'created_at' => ['type' => 'string']
        ]
    ],
    'Template' => [
        'type' => 'object',
        'required' => ['name'],
        'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'description' => ['type' => 'string']]
    ],
    'Style' => [
        'type' => 'object',
        'required' => ['name'],
        'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'description' => ['type' => 'string']]
    ],
    'TemplateStyle' => [
        'type' => 'object',
        'required' => ['temp_id', 'style_id'],
        'properties' => [
            'id' => ['type' => 'integer'],
            'temp_id' => ['type' => 'integer'],
            'style_id' => ['type' => 'integer']
        ]
    ],
    'Error' => [
        'type' => 'object',
        'properties' => ['error' => ['type' => 'string']]
    ],
    'JobSearchResults' => [
        'type' => 'object',
        'properties' => [
            'source' => ['type' => 'string'],
            'attribution_url' => ['type' => 'string', 'format' => 'uri'],
            'jobs' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'slug' => ['type' => 'string'],
                        'title' => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                        'company_name' => ['type' => 'string'],
                        'location' => ['type' => 'string'],
                        'remote' => ['type' => 'boolean'],
                        'job_types' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'url' => ['type' => 'string', 'format' => 'uri'],
                        'created_at' => ['type' => 'integer', 'nullable' => true]
                    ]
                ]
            ],
            'pagination' => [
                'type' => 'object',
                'properties' => [
                    'page' => ['type' => 'integer'],
                    'next' => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                    'previous' => ['type' => 'string', 'format' => 'uri', 'nullable' => true]
                ]
            ]
        ]
    ],
    'Cv' => [
        'type' => 'object',
        'required' => ['name', 'status', 'created_by_user_Id'],
        'properties' => [
            'id' => ['type' => 'integer'],
            'name' => ['type' => 'string'],
            'status' => ['type' => 'string', 'enum' => ['draft', 'completed', 'shared']],
            'created_at' => ['type' => 'string'],
            'created_by_user_Id' => ['type' => 'integer'],
            'temp_id' => ['type' => 'integer', 'nullable' => true]
        ]
    ]
];

$entities = [
    'users' => ['User', 'id'],
    'friendships' => ['Friendship', 'relation_id'],
    'posts' => ['Post', 'id'],
    'templates' => ['Template', 'id'],
    'styles' => ['Style', 'id'],
    'cvs' => ['Cv', 'id'],
    'template-styles' => ['TemplateStyle', 'id']
];

$paths = [];
$tags = [];
foreach ($entities as $name => $entity) {
    [$schema, $parameterName] = $entity;
    $tags[] = ['name' => ucfirst($name)];
    $paths['/api/v1/' . $name] = [
        'get' => ['tags' => [ucfirst($name)], 'summary' => 'List ' . $name, 'responses' => ['200' => ['$ref' => '#/components/responses/SuccessList'], '500' => ['$ref' => '#/components/responses/ServerError']]],
        'post' => ['tags' => [ucfirst($name)], 'summary' => 'Create ' . rtrim($name, 's'), 'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/' . $schema]]]], 'responses' => ['201' => ['$ref' => '#/components/responses/SuccessRecord'], '400' => ['$ref' => '#/components/responses/BadRequest'], '500' => ['$ref' => '#/components/responses/ServerError']]]
    ];
    $paths['/api/v1/' . $name . '/{' . $parameterName . '}'] = [
        'get' => ['tags' => [ucfirst($name)], 'summary' => 'Get ' . rtrim($name, 's'), 'parameters' => [['name' => $parameterName, 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]], 'responses' => ['200' => ['$ref' => '#/components/responses/SuccessRecord'], '400' => ['$ref' => '#/components/responses/BadRequest'], '404' => ['$ref' => '#/components/responses/NotFound'], '500' => ['$ref' => '#/components/responses/ServerError']]],
        'put' => ['tags' => [ucfirst($name)], 'summary' => 'Update ' . rtrim($name, 's'), 'parameters' => [['name' => $parameterName, 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]], 'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/' . $schema]]]], 'responses' => ['200' => ['$ref' => '#/components/responses/SuccessRecord'], '400' => ['$ref' => '#/components/responses/BadRequest'], '404' => ['$ref' => '#/components/responses/NotFound'], '500' => ['$ref' => '#/components/responses/ServerError']]],
        'delete' => ['tags' => [ucfirst($name)], 'summary' => 'Delete ' . rtrim($name, 's'), 'parameters' => [['name' => $parameterName, 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]], 'responses' => ['200' => ['$ref' => '#/components/responses/Deleted'], '400' => ['$ref' => '#/components/responses/BadRequest'], '404' => ['$ref' => '#/components/responses/NotFound'], '500' => ['$ref' => '#/components/responses/ServerError']]]
    ];
}

$tags[] = ['name' => 'Jobs'];
$paths['/api/v1/jobs'] = [
    'get' => [
        'tags' => ['Jobs'],
        'summary' => 'Search Arbeitnow job listings',
        'description' => 'Fetches one Arbeitnow job-board page and filters it by the provided terms. Results include links and source attribution. Search is limited to jobs on the selected page.',
        'parameters' => [
            ['name' => 'what', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'maxLength' => 100]],
            ['name' => 'where', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'maxLength' => 100]],
            ['name' => 'remote', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'boolean']],
            ['name' => 'page', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 1000]],
            ['name' => 'results_per_page', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50]]
        ],
        'responses' => [
            '200' => ['description' => 'Job search results from Arbeitnow', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/JobSearchResults']]]],
            '400' => ['$ref' => '#/components/responses/BadRequest'],
            '500' => ['$ref' => '#/components/responses/ServerError']
        ]
    ]
];

$document = [
    'openapi' => '3.0.3',
    'info' => ['title' => 'CVer API', 'description' => 'CRUD API for CVer database entities.', 'version' => '1.0.0'],
    'servers' => [['url' => '../../../']],
    'tags' => $tags,
    'paths' => $paths,
    'components' => [
        'schemas' => $schemas,
        'responses' => [
            'SuccessList' => ['description' => 'Successful list response', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['data' => ['type' => 'array', 'items' => ['type' => 'object']]]]]]],
            'SuccessRecord' => ['description' => 'Successful record response', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['data' => ['type' => 'object']]]]]],
            'BadRequest' => ['description' => 'Invalid request body or field value', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
            'NotFound' => ['description' => 'Record not found', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
            'Deleted' => ['description' => 'Record deleted', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['deleted' => ['type' => 'boolean']]]]]],
            'ServerError' => ['description' => 'Server or external API error', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]]
        ]
    ]
];

echo json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
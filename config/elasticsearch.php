<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'localhost:9200'),
    'user' => env('ELASTICSEARCH_USER'),
    'password' => env('ELASTICSEARCH_PASSWORD'),
    'cloud_id' => env('ELASTICSEARCH_CLOUD_ID'),
    'api_key' => env('ELASTICSEARCH_API_KEY'),
    'queue' => [
        'timeout' => env('SCOUT_QUEUE_TIMEOUT'),
    ],
    'indices' => [
        'mappings' => [
            env('SCOUT_PREFIX') . 'songs' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'normalized_title' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'data' => [
                        'type' => 'object',
                        'properties' => [

                        ],
                    ],
                    'artist' => [
                        'type' => 'nested',
                        "properties" => [
                            "name" => [
                                "type" => "text",
                                "analyzer" => "standard",
                            ],
                            "normalized_name" => [
                                "type" => "text",
                                "analyzer" => "standard",
                            ],
                        ],
                    ],
                    'album' => [
                        'type' => 'nested',
                        "properties" => [
                            "name" => [
                                "type" => "text",
                                "analyzer" => "standard",
                            ],
                            "normalized_name" => [
                                "type" => "text",
                                "analyzer" => "standard",
                            ],
                        ],
                    ],
                ],
            ],
            env('SCOUT_PREFIX') . 'albums' => [
                'properties' => [
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'normalized_name' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                ],
            ],
            env('SCOUT_PREFIX') . 'artists' => [
                'properties' => [
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'normalized_name' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                    'description' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                    ],
                ],
            ],
        ],
    ]
];

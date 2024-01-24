<?php

namespace App\Infrastructure\ApiResource\Filter;

use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Metadata\FilterInterface;

class RobotOrderFilter implements FilterInterface
{
    # This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'id' => [
                'operations' => ['eq', 'gt', 'gte', 'st', 'lte'],
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
            'name' => [
                'operations' => ['lk'],
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
            'powermove' => [
                'operations' => ['lk'],
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
            'experience' => [
                'operations' => ['eq', 'gt', 'gte', 'st', 'lte'],
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'outOfOrder' => [
                'operations' => ['eq'],
                'type' => Type::BUILTIN_TYPE_BOOL,
            ],
            'avatar' => [
                'operations' => ['lk'],
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
        ];

        foreach ($properties as $property => $config) {
            foreach ($config['operations'] as $operation) {
                $description["orderBy[{$property}]"] = [
                    'type' => $config['type'],
                    'required' => $config['required'] ?? false,
                    'property' => $property,
                    'openapi' => [
                        'allowReserved' => $config['required'] ?? false,
                        'allowEmptyValue' => $config['required'] ?? true,
                    ],
                    'schema' => [ 
                        'type' => 'string', 
                        'enum' => ['asc', 'desc'],
                        'default' => 'asc',
                    ],
                ];
            }
        }

        return $description;
    }
}

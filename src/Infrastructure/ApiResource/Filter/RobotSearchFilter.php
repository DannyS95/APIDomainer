<?php

namespace App\Infrastructure\ApiResource\Filter;

use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Metadata\FilterInterface;

class RobotSearchFilter implements FilterInterface
{
    # This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'id' => [
                'operations' => ['gt', 'gte', 'st', 'lte'],
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
                'operations' => ['gt', 'gte', 'st', 'lte'],
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
                $description["{$property}[{$operation}]"] = [
                    'type' => $config['type'],
                    'required' => $config['required'] ?? false,
                    'openapi' => [
                        'allowReserved' => $config['required'] ?? false,
                        'allowEmptyValue' => $config['required'] ?? true,
                    ],
                ];
            }
        }

        return $description;
    }
}

<?php

namespace App\Infrastructure\ApiResource\Filter;

use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Metadata\FilterInterface;

final class RobotDanceOffSearchFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'id' => [
                'operations' => ['eq', 'gt', 'gte', 'lt', 'lte'],
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'createdAt' => [
                'operations' => ['eq', 'gt', 'gte', 'lt', 'lte'],
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
        ];

        foreach ($properties as $property => $config) {
            foreach ($config['operations'] as $operation) {
                $description["{$property}[$operation]"] = [
                    'type' => $config['type'],
                    'required' => $config['required'] ?? false,
                    'property' => $property,
                    'openapi' => [
                        'allowReserved' => $config['required'] ?? false,
                        'allowEmptyValue' => $config['required'] ?? false,
                    ],
                ];
            }
        }

        return $description;
    }
}

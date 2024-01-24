<?php

namespace App\Infrastructure\ApiResource\Filter;

use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Metadata\FilterInterface;

class RobotDanceOffSearchFilter implements FilterInterface
{
    # This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'id' => [
                'operations' => ['eq', 'gt', 'gte', 'st', 'lte'],
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'robotOne' => [
                'operations' => ['eq', 'gt', 'gte', 'st', 'lte'],
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'robotTwo' => [
                'operations' => ['eq', 'gt', 'gte', 'st', 'lte'],
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'winner' => [
                'operations' => ['eq', 'gt', 'gte', 'st', 'lte'],
                'type' => Type::BUILTIN_TYPE_INT,
            ],
        ];

        foreach ($properties as $property => $config) {
            foreach ($config['operations'] as $operation) {
                $description["{$property}[{$operation}]"] = [
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

<?php

namespace App\Infrastructure\ApiResource\Filter;

use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Metadata\FilterInterface;

class RobotDanceOffOrderFilter implements FilterInterface
{
    # This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'id' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'robotOne' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'robotTwo' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'winner' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
        ];

        foreach ($properties as $property => $config) {
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

        return $description;
    }
}

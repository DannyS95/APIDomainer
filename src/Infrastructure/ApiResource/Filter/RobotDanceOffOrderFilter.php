<?php

namespace App\Infrastructure\ApiResource\Filter;

use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Metadata\FilterInterface;

class RobotDanceOffOrderFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'id' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'createdAt' => [
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
            'teamOneId' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'teamOneName' => [
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
            'teamTwoId' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'teamTwoName' => [
                'type' => Type::BUILTIN_TYPE_STRING,
            ],
            'winningTeamId' => [
                'type' => Type::BUILTIN_TYPE_INT,
            ],
            'winningTeamName' => [
                'type' => Type::BUILTIN_TYPE_STRING,
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

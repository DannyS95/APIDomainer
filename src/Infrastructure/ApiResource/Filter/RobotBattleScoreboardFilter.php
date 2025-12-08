<?php

namespace App\Infrastructure\ApiResource\Filter;

use ApiPlatform\Metadata\FilterInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Documents query parameters for the scoreboard endpoint.
 */
final class RobotBattleScoreboardFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = [
            'year' => [
                'operations' => ['eq'],
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => false,
            ],
            'quarter' => [
                'operations' => ['eq'],
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => false,
            ],
            'page' => [
                'operations' => ['eq'],
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => false,
            ],
            'itemsPerPage' => [
                'operations' => ['eq'],
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => false,
            ],
        ];

        foreach ($properties as $property => $config) {
            foreach ($config['operations'] as $operation) {
                $description["{$property}[{$operation}]"] = [
                    'type' => $config['type'],
                    'required' => $config['required'],
                    'property' => $property,
                    'openapi' => [
                        'allowReserved' => $config['required'],
                        'allowEmptyValue' => $config['required'],
                    ],
                ];
            }
        }

        return $description;
    }
}

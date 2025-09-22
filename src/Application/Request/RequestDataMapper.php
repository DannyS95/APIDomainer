<?php

namespace App\Application\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestDataMapper
{
    private ParameterBag $parameterBag;
    private const FILTER_KEY = 'filter';
    private const SORT_KEY = 'orderBy';
    private const DEFAULT_OPERATION = 'eq';
    private const IGNORED_QUERY_PARAMETERS = [
        self::FILTER_KEY,
        self::SORT_KEY,
        'page',
        'itemsPerPage',
    ];

    public function __construct(private RequestStack $requestStack)
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $this->parameterBag = new ParameterBag(parameters: $request->query->all());
        } else {
            throw new \RuntimeException('Request could not be fetched from the RequestStack.');
        }
    }

    public function getFilters(): array
    {
        $parsedFilters = [];

        foreach ($this->collectRawFilters() as $field => $condition) {
            if (is_array($condition)) {
                foreach ($condition as $operation => $value) {
                    if ($operation === null || $operation === '' || $value === null || $value === '') {
                        continue;
                    }

                    $parsedFilters[$field] = $value;

                    break;
                }
            } elseif ($condition !== null && $condition !== '') {
                $parsedFilters[$field] = $condition;
            }
        }

        return $parsedFilters;
    }

    public function getOperations(): array
    {
        $parsedOperations = [];

        foreach ($this->collectRawFilters() as $field => $condition) {
            if (is_array($condition)) {
                foreach ($condition as $operation => $value) {
                    if ($operation === null || $operation === '' || $value === null || $value === '') {
                        continue;
                    }

                    $parsedOperations[$field] = $operation;

                    break;
                }
            } elseif ($condition !== null && $condition !== '') {
                $parsedOperations[$field] = self::DEFAULT_OPERATION;
            }
        }

        return $parsedOperations;
    }

    public function getSorts(): array
    {
        return $this->parameterBag->get(self::SORT_KEY, []);
    }

    public function getPagination(): array
    {
        return [
            'page' => (int) $this->parameterBag->get('page', 1),
            'itemsPerPage' => (int) $this->parameterBag->get('itemsPerPage', 10),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function collectRawFilters(): array
    {
        $rawFilters = $this->parameterBag->get(self::FILTER_KEY, []);

        if (!is_array($rawFilters)) {
            $rawFilters = [];
        }

        foreach ($this->parameterBag->all() as $key => $value) {
            if (in_array($key, self::IGNORED_QUERY_PARAMETERS, true) || $value === null) {
                continue;
            }

            $rawFilters[$key] = $value;
        }

        return $rawFilters;
    }
}

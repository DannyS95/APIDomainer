<?php

namespace App\Application\Request;

use App\Application\DTO\ApiFiltersDTO;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestDataMapper
{
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
        return $this->getParameterBag()->get(self::SORT_KEY, []);
    }

    public function getPagination(): array
    {
        $parameterBag = $this->getParameterBag();

        return [
            'page' => (int) $parameterBag->get('page', 1),
            'itemsPerPage' => (int) $parameterBag->get('itemsPerPage', 10),
        ];
    }

    /**
     * @param array<string, mixed> $additionalFilters
     * @param array<string, string> $additionalOperations
     */
    public function toApiFiltersDTO(
        array $additionalFilters = [],
        array $additionalOperations = [],
        ?int $itemsPerPageOverride = null
    ): ApiFiltersDTO {
        $filters = array_merge($this->getFilters(), $additionalFilters);
        $operations = array_merge($this->getOperations(), $additionalOperations);
        $pagination = $this->getPagination();

        if ($itemsPerPageOverride !== null) {
            $pagination['itemsPerPage'] = $itemsPerPageOverride;
        }

        return new ApiFiltersDTO(
            filters: $filters,
            operations: $operations,
            sorts: $this->getSorts(),
            page: $pagination['page'],
            itemsPerPage: $pagination['itemsPerPage']
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function collectRawFilters(): array
    {
        $parameterBag = $this->getParameterBag();
        $rawFilters = $parameterBag->get(self::FILTER_KEY, []);

        if (!is_array($rawFilters)) {
            $rawFilters = [];
        }

        foreach ($parameterBag->all() as $key => $value) {
            if (in_array($key, self::IGNORED_QUERY_PARAMETERS, true) || $value === null) {
                continue;
            }

            $rawFilters[$key] = $value;
        }

        return $rawFilters;
    }

    private function getParameterBag(): ParameterBag
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new \RuntimeException('Request could not be fetched from the RequestStack.');
        }

        return new ParameterBag(parameters: $request->query->all());
    }
}

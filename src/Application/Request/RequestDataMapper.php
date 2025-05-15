<?php

namespace App\Application\Request;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestDataMapper
{
    private ParameterBag $parameterBag;
    private const FILTER_KEY = 'filter';
    private const SORT_KEY = 'orderBy';

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
        $rawFilters = $this->parameterBag->get(self::FILTER_KEY, []);
        $parsedFilters = [];

        foreach ($rawFilters as $field => $condition) {
            if (is_array($condition)) {
                $operation = key($condition);
                $value = $condition[$operation];

                if (!empty($value) && !empty($operation)) {
                    $parsedFilters[$field] = $value;
                }
            }
        }

        return $parsedFilters;
    }

    public function getOperations(): array
    {
        $rawFilters = $this->parameterBag->get(self::FILTER_KEY, []);
        $parsedOperations = [];

        foreach ($rawFilters as $field => $condition) {
            if (is_array($condition)) {
                $operation = key($condition);
                if (!empty($operation)) {
                    $parsedOperations[$field] = $operation;
                }
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
}

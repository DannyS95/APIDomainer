<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAction
{
    private ParameterBag $parameterBag;
    private const FILTER_KEY = 'filter';
    private const SORT_KEY = 'orderBy';

    public function __construct(Request $request)
    {
        $this->parameterBag = new ParameterBag($request->query->all());
    }

    /**
     * Retrieve the filters and their operations from the request query parameters.
     * 
     * In API Platform, filters are passed in the URL under the 'filter' key.
     * The format follows:
     *     /robots?filter[experience][gt]=100&filter[name][eq]=Atlas
     * 
     * This method extracts the filter values and their associated operations, 
     * and returns them as an associative array:
     *     [
     *         'experience' => 100,
     *         'name' => 'Atlas'
     *     ]
     *
     * Operations are separated into their own method.
     * If no filters are found, an empty array is returned.
     * 
     * @return array<string, string> Key-value pairs representing field => value.
     */
    public function filters(): array
    {
        $rawFilters = $this->parameterBag->get(self::FILTER_KEY, []);
        $parsedFilters = [];

        foreach ($rawFilters as $field => $condition) {
            if (is_array($condition)) {
                $operation = key($condition);
                $value = $condition[$operation];

                // Only add if there is an operation and a value
                if (!empty($value) && !empty($operation)) {
                    $parsedFilters[$field] = $value;
                }
            }
        }

        return $parsedFilters;
    }

    /**
     * Retrieve the filter operations from the request query parameters.
     * 
     * In API Platform, filter operations are specified within the filter query:
     *     /robots?filter[experience][gt]=100&filter[name][eq]=Atlas
     * 
     * This method extracts the operation types (gt, eq, lt, like) 
     * and returns them as an associative array:
     *     [
     *         'experience' => 'gt',
     *         'name' => 'eq'
     *     ]
     *
     * If no operations are found, an empty array is returned.
     * 
     * @return array<string, string> Key-value pairs representing field => operation.
     */
    public function operations(): array
    {
        $rawFilters = $this->parameterBag->get(self::FILTER_KEY, []);
        $parsedOperations = [];

        foreach ($rawFilters as $field => $condition) {
            if (is_array($condition)) {
                $operation = key($condition);

                // Only add if the operation is not empty
                if (!empty($operation)) {
                    $parsedOperations[$field] = $operation;
                }
            }
        }

        return $parsedOperations;
    }

    /**
     * Retrieve the sorting values from the request query parameters.
     * 
     * In API Platform, sorting criteria are always passed in the URL under the 'orderBy' key.
     * Example:
     *     /robots?orderBy[id]=asc&orderBy[name]=desc
     * 
     * This method extracts those sorting instructions and returns them as an associative array:
     *     [
     *         'id' => 'asc',
     *         'name' => 'desc'
     *     ]
     *
     * If no sorting criteria are found, an empty array is returned.
     * 
     * @return array<string, string> Key-value pairs representing field => direction.
     */
    public function sorts(): array
    {
        return $this->parameterBag->get(self::SORT_KEY, []);
    }

    /**
     * Retrieve pagination parameters from the request query parameters.
     * 
     * In API Platform, pagination is defined by 'page' and 'itemsPerPage'.
     * Example:
     *     /robots?page=2&itemsPerPage=20
     * 
     * This method extracts those parameters and returns them as an associative array:
     *     [
     *         'page' => 2,
     *         'itemsPerPage' => 20
     *     ]
     *
     * Defaults to page 1 and 10 items per page if not specified.
     * 
     * @return array<string, int> Key-value pairs representing pagination settings.
     */
    public function pagination(): array
    {
        return [
            'page' => (int) $this->parameterBag->get('page', 1),
            'itemsPerPage' => (int) $this->parameterBag->get('itemsPerPage', 10),
        ];
    }
}

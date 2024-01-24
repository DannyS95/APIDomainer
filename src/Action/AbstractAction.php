<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAction
{
    private ParameterBag $parameterBag;

    public function __construct(Request $request)
    {
        $this->parameterBag = new ParameterBag($request->query->all());
    }

    /**
     * Get the filter values from ParameterBag instance.
     *
     * @return array<string, string>
     */
    public function filters(): array
    {
        $parameters = $this->parameterBag->all();

        $filters = [];

        \array_walk($parameters, function ($item, $key) use (&$filters) {
            if (is_array($item) === false || $key === 'orderBy') {
                return;
            }

            $filters[$key] = \array_values($item)[0];
        });

        return $filters;
    }

    /**
     * Get the filter operations from ParameterBag instance.
     *
     * @return array<string, string>
     */
    public function operations()
    {
        $parameters = $this->parameterBag->all();

        $operations = [];

        \array_walk($parameters, function ($item, $key) use (&$operations) {
            if (is_array($item) === false || $key === 'orderBy') {
                return;
            }
            $operations[$key] = \key($item);
        });

        return $operations;
    }

    public function sorts()
    {
        return $this->parameterBag->all()['orderBy'];
    }
}

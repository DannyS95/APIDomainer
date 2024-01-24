<?php

namespace App\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

final class RobotCollectionResponder
{
    /**
     * Prepare the response for a collection of Robots.
     *
     * @param array $models
     * @param int $status
     * @return JsonResponse
     */
    public function respond(array $models, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $response = array_map(static function ($model) {
            return [
                'id' => $model->getId(),
                'name' => $model->getName(),
                'type' => $model->getType(),
                'health' => $model->getHealth(),
                'power_level' => $model->getPowerLevel(),
                'status' => $model->getStatus(),
            ];
        }, $models);

        return new JsonResponse($response, $status);
    }
}

<?php

namespace App\Infrastructure\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use App\Action\RobotCollectionAction;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiResource\Filter\RobotOrderFilter;
use App\Infrastructure\Responder\DTO\RobotResponseDTO;
use App\Infrastructure\ApiResource\Filter\RobotSearchFilter;

#[ApiResource(
    routePrefix: '/robots',
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 10,
    operations: [
        new GetCollection(
            uriTemplate: '/',
            name: 'robots',
            controller: RobotCollectionAction::class,
            read: false,
            filters: [ RobotSearchFilter::class, RobotOrderFilter::class ],
            output: RobotResponseDTO::class,
        )
    ]
)]
final class RobotResource
{
}

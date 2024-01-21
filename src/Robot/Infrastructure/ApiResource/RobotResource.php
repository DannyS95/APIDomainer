<?php

namespace App\Robot\Infrastructure\ApiResource;

use App\Infrastructure\Filter\RobotsSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Robot\Action\RobotCollectionAction;

#[ApiResource(
    routePrefix: '/robots',
    paginationEnabled: false,
    operations: [
        new GetCollection(
            uriTemplate: '/',
            name: 'robots',
            controller: RobotCollectionAction::class,
            read: false,
            filters: [
                RobotsSearchFilter::class
            ]
        )
    ]
)]
class RobotResource
{
}
    
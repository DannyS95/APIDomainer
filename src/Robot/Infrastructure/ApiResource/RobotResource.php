<?php

namespace App\Robot\Infrastructure\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Robot\Action\RobotCollectionAction;
use App\Robot\Infrastructure\ApiResource\Filter\RobotsSearchFilter;
 

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
    
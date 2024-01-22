<?php

namespace App\Infrastructure\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Action\RobotCollectionAction;
use App\Infrastructure\ApiResource\Filter\RobotSearchFilter;

#[ApiResource(
    routePrefix: '/robots',
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    operations: [
        new GetCollection(
            uriTemplate: '/',
            name: 'robots',
            controller: RobotCollectionAction::class,
            read: false,
            filters: [ RobotSearchFilter::class, ],
        )
    ]
)]
class RobotResource
{
}

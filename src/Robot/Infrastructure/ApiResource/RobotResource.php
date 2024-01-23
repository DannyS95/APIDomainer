<?php

namespace App\Robot\Infrastructure\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Action\RobotCollectionAction;
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
            filters: [ RobotSearchFilter::class, ],
        )
    ]
)]
final class RobotResource
{
}

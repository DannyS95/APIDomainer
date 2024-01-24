<?php

namespace App\Infrastructure\ApiResource;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use App\Action\RobotCollectionAction;
use ApiPlatform\Metadata\GetCollection;
use App\Action\RobotDanceOffsCollectionAction;
use App\Infrastructure\Response\RobotResponse;
use App\Infrastructure\Request\RobotDanceOffRequest;
use App\Infrastructure\Response\RobotDanceOffResponse;
use App\Infrastructure\ApiResource\Filter\RobotOrderFilter;
use App\Infrastructure\ApiResource\Filter\RobotSearchFilter;
use App\Infrastructure\ApiResource\Filter\RobotDanceOffOrderFilter;
use App\Infrastructure\ApiResource\Filter\RobotDanceOffSearchFilter;

#[ApiResource(
    routePrefix: '/robots',
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 10,
    operations: [
        new GetCollection(
            uriTemplate: '/',
            name: 'Robots',
            controller: RobotCollectionAction::class,
            read: false,
            filters: [ RobotSearchFilter::class, RobotOrderFilter::class ],
            output: RobotResponse::class,
        ),  
        new Post(
            uriTemplate: '/dance-off',
            name: 'Dance-offs',
            input: RobotDanceOffRequest::class,
            read: false,
            output: false,
            messenger: 'input'
        ),
        new GetCollection(
            uriTemplate: '/dance-offs',
            name: 'Robot Dance-offs',
            controller: RobotDanceOffsCollectionAction::class,
            read: false,
            filters: [ RobotDanceOffSearchFilter::class, RobotDanceOffOrderFilter::class ],
            output: RobotDanceOffResponse::class,
        ),
    ]
)]
final class RobotResource
{
}

<?php

namespace App\Infrastructure\ApiResource;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use App\Action\RobotCollectionAction;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\Request\RobotDanceOffRequest;
use App\Infrastructure\Responder\DTO\RobotResponseDTO;
use App\Infrastructure\ApiResource\Filter\RobotOrderFilter;
use App\Infrastructure\ApiResource\Filter\RobotSearchFilter;

#[ApiResource(
    routePrefix: '/robots',
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 10,
    operations: [
        new GetCollection(
            uriTemplate: '/',
            name: 'Robots',
            controller: RobotCollectionAction::class,
            read: false,
            filters: [ RobotSearchFilter::class, RobotOrderFilter::class ],
            output: RobotResponseDTO::class,
        ),
        new Post(
            uriTemplate: '/dance-off',
            name: 'Dance-offs',
            input: RobotDanceOffRequest::class,
            read: false,
            output: false,
            messenger: 'input'
        )
    ]
)]
final class RobotResource
{
}

<?php

namespace App\Infrastructure\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Action\RobotDanceOffHistoryAction;
use App\Action\RobotDanceOffScoreboardAction;
use App\Action\RobotDanceOffTeamsAction;
use App\Infrastructure\Request\RobotBattleReplayRequest;
use App\Infrastructure\Response\RobotDanceOffScoreboardResponse;
use App\Infrastructure\Response\RobotDanceOffResponse;
use App\Infrastructure\Response\RobotDanceOffTeamsResponse;

#[ApiResource(
    routePrefix: '/robot-battles',
    operations: [
        new Post(
            uriTemplate: '/replays',
            name: 'Robot Battle Replay',
            input: RobotBattleReplayRequest::class,
            output: false,
            read: false,
            messenger: 'input'
        ),
        new GetCollection(
            uriTemplate: '/',
            name: 'Robot Dance-Off History Scoreboard',
            controller: RobotDanceOffScoreboardAction::class,
            output: RobotDanceOffScoreboardResponse::class,
            read: false
        ),
        new GetCollection(
            uriTemplate: '/{battleId}/dance-offs',
            name: 'Robot Dance-Off History',
            controller: RobotDanceOffHistoryAction::class,
            output: RobotDanceOffResponse::class,
            read: false
        ),
        new GetCollection(
            uriTemplate: '/{battleId}/teams',
            name: 'Robot Dance-Off Teams',
            controller: RobotDanceOffTeamsAction::class,
            output: RobotDanceOffTeamsResponse::class,
            read: false
        ),
    ]
)]
final class RobotDanceOffHistoryResource
{
}

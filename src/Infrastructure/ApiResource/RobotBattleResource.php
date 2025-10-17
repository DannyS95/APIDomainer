<?php

namespace App\Infrastructure\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Action\RobotBattleHistoryAction;
use App\Action\RobotBattleScoreboardAction;
use App\Action\RobotBattleTeamsAction;
use App\Infrastructure\Request\RobotBattleReplayRequest;
use App\Infrastructure\Response\RobotBattleScoreboardResponse;
use App\Infrastructure\Response\RobotDanceOffResponse;
use App\Infrastructure\Response\RobotBattleTeamsResponse;

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
            name: 'Robot Battle Scoreboard',
            controller: RobotBattleScoreboardAction::class,
            output: RobotBattleScoreboardResponse::class,
            read: false
        ),
        new GetCollection(
            uriTemplate: '/{battleId}/dance-offs',
            name: 'Robot Battle History',
            controller: RobotBattleHistoryAction::class,
            output: RobotDanceOffResponse::class,
            read: false
        ),
        new GetCollection(
            uriTemplate: '/{battleId}/teams',
            name: 'Robot Battle Teams',
            controller: RobotBattleTeamsAction::class,
            output: RobotBattleTeamsResponse::class,
            read: false
        ),
    ]
)]
final class RobotBattleResource
{
}

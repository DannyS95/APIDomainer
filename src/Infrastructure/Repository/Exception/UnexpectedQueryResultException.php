<?php

namespace App\Infrastructure\Repository\Exception;

use RuntimeException;

final class UnexpectedQueryResultException extends RuntimeException
{
    public static function forRobotBattleView(mixed $result): self
    {
        $context = ['type' => \get_debug_type($result)];

        if (is_array($result)) {
            $context['keys'] = array_keys($result);
        }

        $contextPayload = json_encode($context);

        if ($contextPayload === false) {
            $contextPayload = 'unserializable context';
        }

        return new self(sprintf(
            'Expected RobotBattleView instance from query result. Context: %s',
            $contextPayload
        ));
    }
}

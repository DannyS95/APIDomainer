<?php

namespace App\Application\Query;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class QueryBusDispatcher
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private readonly MessageBusInterface $queryBus
    ) {
    }

    public function ask(object $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp instanceof HandledStamp) {
            throw new RuntimeException(sprintf(
                'No handler returned a result for %s.',
                $query::class
            ));
        }

        return $handledStamp->getResult();
    }

    public function askArray(object $query): array
    {
        $result = $this->ask($query);

        if (!is_array($result)) {
            throw new RuntimeException(sprintf(
                'Unexpected response type for %s.',
                $query::class
            ));
        }

        return $result;
    }
}

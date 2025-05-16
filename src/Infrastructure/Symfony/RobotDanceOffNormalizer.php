<?php

namespace App\Infrastructure\Serializer;

use App\Domain\Entity\RobotDanceOff;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RobotDanceOffNormalizer implements NormalizerInterface
{
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId(),
            'createdAt' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'teamOne' => $object->getTeamOne(),
            'teamTwo' => $object->getTeamTwo(),
            'winner' => $object->getWinner()?->getId()
        ];
    }

    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof RobotDanceOff;
    }
}

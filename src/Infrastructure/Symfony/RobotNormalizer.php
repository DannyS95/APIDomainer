<?php

namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Robot;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class RobotNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * Normalize the Robot entity into a JSON-friendly format.
     *
     * @param Robot $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Robot) {
            return [];
        }

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'powermove' => $object->getPowermove(),
            'experience' => $object->getExperience(),
            'out_of_order' => $object->isOutOfOrder(),
            'avatar' => $object->getAvatar(),
            // Optional: Relation IDs only
            'as_robot_one_ids' => $object->getAsRobotOne()->map(fn($r) => $r->getId())->toArray(),
            'as_robot_two_ids' => $object->getAsRobotTwo()->map(fn($r) => $r->getId())->toArray(),
            'as_winner_ids' => $object->getAsWinner()->map(fn($r) => $r->getId())->toArray(),
        ];
    }

    /**
     * Check if the object is supported for normalization.
     *
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof Robot;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Robot::class => true,
        ];
    }
}

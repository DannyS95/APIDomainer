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

        // Map the team participations
        $teams = $object->getTeams()->map(function ($team) {
            return [
                'team_id' => $team->getId(),
                'team_name' => $team->getName()
            ];
        })->toArray();

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'powermove' => $object->getPowermove(),
            'experience' => $object->getExperience(),
            'out_of_order' => $object->isOutOfOrder(),
            'avatar' => $object->getAvatar(),
            'teams' => $teams,
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

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

        // Map the dance-off participations
        $danceOffs = $object->getDanceOffParticipations()->map(function ($participation) {
            return [
                'dance_off_id' => $participation->getDanceOff()->getId(),
                'team' => $participation->getTeam()
            ];
        })->toArray();

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'powermove' => $object->getPowermove(),
            'experience' => $object->getExperience(),
            'out_of_order' => $object->isOutOfOrder(),
            'avatar' => $object->getAvatar(),
            'participations' => $danceOffs,
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

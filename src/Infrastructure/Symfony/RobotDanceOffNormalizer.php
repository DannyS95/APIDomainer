<?php

namespace App\Infrastructure\Serializer;

use App\Domain\Entity\RobotDanceOff;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RobotDanceOffNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        private UrlGeneratorInterface $router,
    ) {
    }

    public function normalize($topic, ?string $format = null, array $context = []): array
    {
        return [
            'id' => $topic->getId(), 
            'robotOne' => $topic->getRobotOne()->getId(),
            'robotTwo' => $topic->getRobotTwo()->getId(),
            'winner' => $topic->getWinner()?->getId(),
        ];
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RobotDanceOff;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            RobotDanceOff::class => true,
        ];
    }
}
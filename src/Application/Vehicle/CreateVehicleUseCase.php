<?php

namespace App\Application\Vehicle;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;

class CreateVehicleUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function saveVehicle($vehicle): void
    {
        try {
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save vehicle');
        }
    }

    public function execute($model, $constructor, $dailyPrice): array
    {
        try {
            $vehicle = new Vehicle($model, $constructor, $dailyPrice);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid vehicle data: ' . $e->getMessage());
        }

        $this->saveVehicle($vehicle);

        return $vehicle->serialize();
    }
}
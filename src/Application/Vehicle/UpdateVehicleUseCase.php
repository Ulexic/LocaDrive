<?php

namespace App\Application\Vehicle;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdateVehicleUseCase
{

    private VehicleRepository $vehicleRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager){
        $this->vehicleRepository = $vehicleRepository;
        $this->entityManager = $entityManager;
    }

    private function getVehicle($id): Vehicle
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        return $vehicle;
    }

    private function saveVehicle($vehicle): void {
        try {
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save vehicle');
        }
    }

    public function execute($id, $model, $constructor, $dailyPrice): array
    {
        $vehicle = $this->getVehicle($id);

        $vehicle->update($model, $constructor, $dailyPrice);

        $this->saveVehicle($vehicle);

        return $vehicle->serialize();
    }
}
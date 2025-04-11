<?php

namespace App\Application\Vehicle;

use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeleteVehicleUseCase
{

    private VehicleRepository $vehicleRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager){
        $this->vehicleRepository = $vehicleRepository;
        $this->entityManager = $entityManager;
    }

    private function getVehicle($id)
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        return $vehicle;
    }

    private function deleteVehicle($vehicle): void
    {
        try {
            $this->entityManager->remove($vehicle);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to delete vehicle');
        }
    }

    public function execute($id): void
    {
        $vehicle = $this->getVehicle($id);

        $this->deleteVehicle($vehicle);

    }
}
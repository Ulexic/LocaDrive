<?php

namespace App\Application\Vehicle;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;

class GetVehicleUseCase
{

    private VehicleRepository $vehicleRepository;

    public function __construct(VehicleRepository $vehicleRepository) {
        $this->vehicleRepository = $vehicleRepository;
    }

    private function getVehicle($id): Vehicle
    {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        return $vehicle;
    }

    public function execute($id): array
    {
        $vehicle = $this->getVehicle($id);

        return $vehicle->serialize();
    }
}
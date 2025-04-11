<?php

namespace App\Application\Vehicle;

use App\Repository\VehicleRepository;

class GetAllVehicleUseCase
{

    private VehicleRepository $vehicleRepository;

    public function __construct(VehicleRepository $vehicleRepository) {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(): array
    {
        $vehicles = $this->vehicleRepository->findAll();

        if (!$vehicles) {
            throw new \InvalidArgumentException('No vehicles found');
        }

        $serializedVehicles = [];
        foreach ($vehicles as $vehicle) {
            $serializedVehicles[] = $vehicle->serialize();
        }

        return $serializedVehicles;
    }
}
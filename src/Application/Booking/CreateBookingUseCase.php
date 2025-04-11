<?php

namespace App\Application\Booking;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\Booking;
use App\Entity\Vehicle;
use App\Repository\ClientRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreateBookingUseCase
{
    private EntityManagerInterface $entityManager;
    private ClientRepository $clientRepository;
    private VehicleRepository $vehicleRepository;


    public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository, VehicleRepository $vehicleRepository){
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    private function getClient($id): Client {
        $client = $this->clientRepository->find($id);

        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }

        return $client;
    }

    private function getVehicle($id): Vehicle {
        $vehicle = $this->vehicleRepository->find($id);

        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        return $vehicle;
    }

    private function updateOrder(Order $order, Booking $booking): void {
        $order->addBooking($booking);
        $order->getPayment()->addPrice($booking->calculatePrice());

        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to update order with booking');
        }
    }

    private function saveBooking(Booking $booking): void {
        try {
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save booking');
        }
    }



    public function execute($clientId, $vehicleId, $startDate, $endDate): array
    {
        $client = $this->getClient($clientId);
        $order = $client->getCurrentOrder();
        $vehicle = $this->getVehicle($vehicleId);

        try {
            $booking = new Booking($vehicle, $startDate, $endDate, null, $order);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Invalid booking data: ' . $e->getMessage());
        }

        $this->saveBooking($booking);

        $this->updateOrder($order, $booking);
        return $booking->serialize();
    }
}
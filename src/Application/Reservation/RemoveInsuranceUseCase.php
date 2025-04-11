<?php

namespace App\Application\Reservation;

use App\Entity\Booking;
use App\Repository\ClientRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class RemoveInsuranceUseCase
{
    private EntityManagerInterface $entityManager;
    private ReservationRepository $reservationRepository;

    private ClientRepository $clientRepository;


    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, ClientRepository $clientRepository){
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
        $this->clientRepository = $clientRepository;
    }

    private function getReservation($id): Booking {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            throw new \InvalidArgumentException('Booking not found');
        }

        return $reservation;
    }

    private function saveReservation(Booking $reservation): void {
        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save reservation');
        }
    }

    public function execute($reservationId, $clientId): void
    {
        $client = $this->clientRepository->find($clientId);
        $client->checkHasReservation($reservationId);

        $reservation = $this->getReservation($reservationId);
        $reservation->getOrder()->checkIsInCart();

        $reservation->removeInsurance();

        $this->saveReservation($reservation);
    }
}
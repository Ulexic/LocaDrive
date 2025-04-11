<?php

namespace App\Application\Reservation;

use App\Entity\Insurance;
use App\Entity\Order;
use App\Entity\Booking;
use App\Repository\ClientRepository;
use App\Repository\InsuranceRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddInsuranceUseCase
{
    private EntityManagerInterface $entityManager;
    private ReservationRepository $reservationRepository;
    private InsuranceRepository $insuranceRepository;

    private ClientRepository $clientRepository;


    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, InsuranceRepository $insuranceRepository, ClientRepository $clientRepository){
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
        $this->insuranceRepository = $insuranceRepository;
        $this->clientRepository = $clientRepository;
    }

    private function getReservation($id): Booking
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            throw new \InvalidArgumentException('Booking not found');
        }
        return $reservation;
    }

    private function getInsurance($id): Insurance
    {
        $insurance = $this->insuranceRepository->find($id);
        if (!$insurance) {
            throw new \InvalidArgumentException('Insurance not found');
        }
        return $insurance;
    }

    private function saveOrder(Order $order): void {
        try {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save order');
        }
    }

    public function execute($reservationId, $insuranceId, $clientId): array
    {
        $client = $this->clientRepository->find($clientId);
        $client->checkHasReservation($reservationId);

        $reservation = $this->getReservation($reservationId);
        $reservation->getOrder()->checkIsInCart();
        $insurance = $this->getInsurance($insuranceId);

        $reservation->addInsurance($insurance);
        $reservation->getOrder()->getPayment()->addPrice($insurance->getPrice());

        $this->saveOrder($reservation->getOrder());

        return $reservation->serialize();
    }
}
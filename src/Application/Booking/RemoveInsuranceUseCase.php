<?php

namespace App\Application\Booking;

use App\Entity\Booking;
use App\Repository\ClientRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

class RemoveInsuranceUseCase
{
    private EntityManagerInterface $entityManager;
    private BookingRepository $bookingRepository;

    private ClientRepository $clientRepository;


    public function __construct(EntityManagerInterface $entityManager, BookingRepository $bookingRepository, ClientRepository $clientRepository){
        $this->entityManager = $entityManager;
        $this->bookingRepository = $bookingRepository;
        $this->clientRepository = $clientRepository;
    }

    private function getBooking($id): Booking {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            throw new \InvalidArgumentException('Booking not found');
        }

        return $booking;
    }

    private function saveBooking(Booking $booking): void {
        try {
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \RuntimeException('Failed to save booking');
        }
    }

    public function execute($bookingId, $clientId): void
    {
        $client = $this->clientRepository->find($clientId);

        $booking = $this->getBooking($bookingId);

        $client->checkHasBooking($booking);

        $booking->getOrder()->checkIsInCart();

        $booking->removeInsurance();

        $this->saveBooking($booking);
    }
}
<?php

namespace App\Application\Booking;

use App\Entity\Insurance;
use App\Entity\Order;
use App\Entity\Booking;
use App\Repository\ClientRepository;
use App\Repository\InsuranceRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddInsuranceUseCase
{
    private EntityManagerInterface $entityManager;
    private BookingRepository $bookingRepository;
    private InsuranceRepository $insuranceRepository;

    private ClientRepository $clientRepository;


    public function __construct(EntityManagerInterface $entityManager, BookingRepository $bookingRepository, InsuranceRepository $insuranceRepository, ClientRepository $clientRepository){
        $this->entityManager = $entityManager;
        $this->bookingRepository = $bookingRepository;
        $this->insuranceRepository = $insuranceRepository;
        $this->clientRepository = $clientRepository;
    }

    private function getBooking($id): Booking
    {
        $booking = $this->bookingRepository->find($id);
        if (!$booking) {
            throw new \InvalidArgumentException('Booking not found');
        }
        return $booking;
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

    private function checkClient($clientId, $booking): void {
        $client = $this->clientRepository->find($clientId);
        if (!$client) {
            throw new \InvalidArgumentException('Client not found');
        }

        $client->checkHasBooking($booking);
    }
    public function execute($bookingId, $insuranceId, $clientId): array
    {

        $booking = $this->getBooking($bookingId);

        $this->checkClient($clientId, $booking);

        $booking->getOrder()->checkIsInCart();
        $insurance = $this->getInsurance($insuranceId);

        $booking->addInsurance($insurance);
        $booking->getOrder()->getPayment()->addPrice($insurance->getPrice());

        $this->saveOrder($booking->getOrder());

        return $booking->serialize();
    }
}
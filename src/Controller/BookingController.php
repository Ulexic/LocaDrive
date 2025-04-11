<?php

namespace App\Controller;

use App\Application\Reservation\AddInsuranceUseCase;
use App\Application\Reservation\CreateBookingUseCase;
use App\Application\Reservation\DeleteBookingUseCase;
use App\Application\Reservation\RemoveInsuranceUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private AddInsuranceUseCase $addInsuranceUseCase;
    private RemoveInsuranceUseCase $removeInsuranceUseCase;

    private CreateBookingUseCase $createReservationUseCase;

    private DeleteBookingUseCase $deleteReservationUseCase;


    public function __construct(AddInsuranceUseCase $addInsuranceUseCase, RemoveInsuranceUseCase $removeInsuranceUseCase, CreateBookingUseCase $createReservationUseCase, DeleteBookingUseCase $deleteReservationUseCase)
    {
        $this->createReservationUseCase = $createReservationUseCase;
        $this->deleteReservationUseCase = $deleteReservationUseCase;
        $this->removeInsuranceUseCase = $removeInsuranceUseCase;
        $this->addInsuranceUseCase = $addInsuranceUseCase;
    }

    #[Route(path: '/client/{clientId}/reservation/{reservationId}/insurance/{insuranceId}', name: 'add_insurance', methods: ['POST'])]
    public function addInsurance(Request $request): Response
    {
        $clientId = $request->get('clientId');
        $insuranceId = $request->get('insuranceId');
        $reservationId = $request->get('reservationId');

        try {
            $reservation = $this->addInsuranceUseCase->execute($reservationId, $insuranceId, $clientId);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'reservation' => $reservation
        ]));
    }

    #[Route(path: '/client/{clientId}/reservation/{insuranceId}/insurance', name: 'delete_insurance', methods: ['DELETE'])]
    public function deleteInsurance(Request $request): Response
    {
        $clientId = $request->get('clientId');
        $insuranceId = $request->get('insuranceId');

        try {
            $this->removeInsuranceUseCase->execute($insuranceId, $clientId);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'message' => "Insurance deleted successfully"
        ]));
    }

    #[Route(path: '/client/{id}/reservation', name: 'create_reservation', methods: ['POST'])]
    public function createReservation(Request $request): Response
    {
        $clientId = $request->get('id');

        $data = json_decode($request->getContent(), true);

        $vehicleId = $data['vehicleId'] ?? null;
        $startDate = $data['startDate'] ?? null;
        $endDate = $data['endDate'] ?? null;

        try {
            $reservation = $this->createReservationUseCase->execute($clientId, $vehicleId, $startDate, $endDate);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'reservation' => $reservation
        ]));
    }

    #[Route(path: '/client/{clientId}/reservation/{reservationId}', name: 'delete_reservation', methods: ['DELETE'])]
    public function deleteReservation(Request $request): Response
    {
        $clientId = $request->get('clientId');
        $reservationId = $request->get('reservationId');

        try {
            $this->deleteReservationUseCase->execute($reservationId, $clientId);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'message' => "Booking deleted successfully"
        ]));
    }

}
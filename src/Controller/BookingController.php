<?php

namespace App\Controller;

use App\Application\Booking\AddInsuranceUseCase;
use App\Application\Booking\CreateBookingUseCase;
use App\Application\Booking\DeleteBookingUseCase;
use App\Application\Booking\RemoveInsuranceUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private AddInsuranceUseCase $addInsuranceUseCase;
    private RemoveInsuranceUseCase $removeInsuranceUseCase;

    private CreateBookingUseCase $createBookingUseCase;

    private DeleteBookingUseCase $deleteBookingUseCase;


    public function __construct(AddInsuranceUseCase $addInsuranceUseCase, RemoveInsuranceUseCase $removeInsuranceUseCase, CreateBookingUseCase $createBookingUseCase, DeleteBookingUseCase $deleteBookingUseCase)
    {
        $this->createBookingUseCase = $createBookingUseCase;
        $this->deleteBookingUseCase = $deleteBookingUseCase;
        $this->removeInsuranceUseCase = $removeInsuranceUseCase;
        $this->addInsuranceUseCase = $addInsuranceUseCase;
    }

    #[Route(path: '/client/{clientId}/booking/{bookingId}/insurance/{insuranceId}', name: 'add_insurance', methods: ['POST'])]
    public function addInsurance(Request $request): Response
    {
        $clientId = $request->get('clientId');
        $insuranceId = $request->get('insuranceId');
        $bookingId = $request->get('bookingId');

        try {
            $booking = $this->addInsuranceUseCase->execute($bookingId, $insuranceId, $clientId);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'booking' => $booking
        ]));
    }

    #[Route(path: '/client/{clientId}/booking/{insuranceId}/insurance', name: 'delete_insurance', methods: ['DELETE'])]
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

    #[Route(path: '/client/{id}/booking', name: 'create_booking', methods: ['POST'])]
    public function createBooking(Request $request): Response
    {
        $clientId = $request->get('id');

        $data = json_decode($request->getContent(), true);

        $vehicleId = $data['vehicleId'] ?? null;
        $startDate = $data['startDate'] ?? null;
        $endDate = $data['endDate'] ?? null;

        try {
            $booking = $this->createBookingUseCase->execute($clientId, $vehicleId, $startDate, $endDate);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'booking' => $booking
        ]));
    }

    #[Route(path: '/client/{clientId}/booking/{bookingId}', name: 'delete_booking', methods: ['DELETE'])]
    public function deleteBooking(Request $request): Response
    {
        $clientId = $request->get('clientId');
        $bookingId = $request->get('bookingId');

        try {
            $this->deleteBookingUseCase->execute($bookingId, $clientId);
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
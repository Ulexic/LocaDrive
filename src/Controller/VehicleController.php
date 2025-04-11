<?php

namespace App\Controller;

use App\Application\Vehicle\CreateVehicleUseCase;
use App\Application\Vehicle\DeleteVehicleUseCase;
use App\Application\Vehicle\GetAllVehicleUseCase;
use App\Application\Vehicle\GetVehicleUseCase;
use App\Application\Vehicle\UpdateVehicleUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    private CreateVehicleUseCase $createVehicleUseCase;
    private DeleteVehicleUseCase $DeleteVehicleUseCase;
    private UpdateVehicleUseCase $UpdateVehicleUseCase;
    private GetAllVehicleUseCase $GetAllVehicleUseCase;
    private GetVehicleUseCase $GetVehicleUseCase;

    public function __construct(CreateVehicleUseCase $createVehicleUseCase, DeleteVehicleUseCase $deleteVehicleUseCase, UpdateVehicleUseCase $updateVehicleUseCase, GetVehicleUseCase $GetVehicleUseCase, GetAllVehicleUseCase $GetAllVehicleUseCase) {
        $this->createVehicleUseCase = $createVehicleUseCase;
        $this->DeleteVehicleUseCase = $deleteVehicleUseCase;
        $this->UpdateVehicleUseCase = $updateVehicleUseCase;
        $this->GetVehicleUseCase = $GetVehicleUseCase;
        $this->GetAllVehicleUseCase = $GetAllVehicleUseCase;
    }

    #[Route(path: '/vehicle/{id}', name: 'get_vehicle', methods: ['GET'])]
    public function getOne(Request $request): Response
    {
        $id = $request->get('id');

        try {
            $vehicle = $this->GetVehicleUseCase->execute($id);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'vehicle' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'message' => json_encode($vehicle)
        ]));
    }

    #[Route(path: '/vehicle', name: 'get_all_vehicle', methods: ['GET'])]
    public function getAll(): Response
    {
        try {
            $vehicles = $this->GetAllVehicleUseCase->execute();
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'vehicle' => json_encode($vehicles)
        ]));
    }

    #[Route(path: '/admin/vehicle', name: 'create_vehicle', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $model = $data['model'] ?? null;
        $constructor = $data['constructor'] ?? null;
        $dailyPrice = $data['dailyPrice'] ?? null;

        try {
            $vehicle = $this->createVehicleUseCase->execute($model, $constructor, $dailyPrice);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'vehicle' => json_encode($vehicle)
        ]));
    }

    #[Route(path: '/admin/vehicle/{id}', name: 'update_vehicle', methods: ['PUT'])]
    public function update(Request $request): Response
    {
        $id = $request->get('id');
        $data = json_decode($request->getContent(), true);
        $model = $data['model'] ?? null;
        $constructor = $data['constructor'] ?? null;
        $dailyPrice = $data['dailyPrice'] ?? null;

        try {
            $vehicle = $this->UpdateVehicleUseCase->execute($id, $model, $constructor, $dailyPrice);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'vehicle' => json_encode($vehicle)
        ]));
    }

    #[Route(path: '/admin/vehicle/{id}', name: 'delete_vehicle', methods: ['DELETE'])]
    public function delete(Request $request): Response
    {
        $id = $request->get('id');

        try {
            $this->DeleteVehicleUseCase->execute($id);
        } catch (\Exception $e) {
            return new Response($this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]), Response::HTTP_BAD_REQUEST);
        }

        return new Response($this->json([
            'status' => 'success',
            'message' => "The vehicle was successfully deleted"
        ]));
    }
}
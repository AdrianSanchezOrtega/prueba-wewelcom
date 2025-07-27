<?php
namespace App\Controller;

use App\Entity\Restaurante;
use App\Repository\RestauranteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route('/api/restaurantes')]
class RestauranteApiController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[Route('/', methods: ['GET'])]
    public function index(RestauranteRepository $repo): Response
    {
        $restaurantes = $repo->findAll();
        $data = array_map(fn($restaurante) => [
            'id' => $restaurante->getId(),
            'nombre' => $restaurante->getNombre(),
            'direccion' => $restaurante->getDireccion(),
            'telefono' => $restaurante->getTelefono(),
        ], $restaurantes);
        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    #[Route('/', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['nombre']) || !isset($data['direccion']) || !isset($data['telefono'])) {
            return $this->json(['error' => 'Datos incompletos o formato incorrecto'], Response::HTTP_BAD_REQUEST);
        }
        $restaurante = new Restaurante();
        $restaurante->setNombre($data['nombre'])
            ->setDireccion($data['direccion'])
            ->setTelefono($data['telefono']);
        $em->persist($restaurante);
        $em->flush();
        return $this->json([
            'id' => $restaurante->getId(),
            'nombre' => $restaurante->getNombre(),
            'direccion' => $restaurante->getDireccion(),
            'telefono' => $restaurante->getTelefono(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show($id, RestauranteRepository $repo): Response
    {
        $restaurante = $repo->find($id);
        if (!$restaurante) {
            return $this->json(['error' => 'Restaurante no encontrado'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $restaurante->getId(),
            'nombre' => $restaurante->getNombre(),
            'direccion' => $restaurante->getDireccion(),
            'telefono' => $restaurante->getTelefono(),
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update($id, Request $request, RestauranteRepository $repo, EntityManagerInterface $em): Response
    {
        $restaurante = $repo->find($id);
        if (!$restaurante) {
            return $this->json(['error' => 'Restaurante no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['nombre']) || !isset($data['direccion']) || !isset($data['telefono'])) {
            return $this->json(['error' => 'Datos incompletos o formato incorrecto'], Response::HTTP_BAD_REQUEST);
        }
        $restaurante->setNombre($data['nombre'])
            ->setDireccion($data['direccion'])
            ->setTelefono($data['telefono']);
        $em->flush();
        return $this->json(['status' => 'Restaurante actualizado'], Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete($id, RestauranteRepository $repo, EntityManagerInterface $em): Response
    {
        $restaurante = $repo->find($id);
        if (!$restaurante) {
            return $this->json(['error' => 'Restaurante no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($restaurante);
        $em->flush();
        return $this->json(['status' => 'Restaurante eliminado'], Response::HTTP_OK);
    }
}

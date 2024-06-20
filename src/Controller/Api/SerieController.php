<?php

namespace App\Controller\Api;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/series', name: 'api_series_')]
class SerieController extends AbstractController
{
    #[Route('', name: 'all', methods: ['GET'])]
    public function retrieveAll(SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository->findAll();

        return $this->json($serie, Response::HTTP_OK, [], ['groups' => 'serie']);

    }

    #[Route('/{id}', name: 'one', methods: ['GET'])]
    public function retrieveOne(SerieRepository $serieRepository, SerializerInterface $serializer, int $id): Response
    {
        $serie = $serieRepository->find($id);
//        $json = json_encode($serie);
        //Méthode une : serialization à la main
//        $json = $serializer->serialize($serie, 'json', ['groups' => 'serie']);
//        return new JsonResponse($json, Response::HTTP_OK);

        //Méthode deux : la même chose en une seule ligne
        return $this->json($serie, Response::HTTP_OK, [], ['groups' => 'serie']);
    }

    #[Route('', name: 'add', methods: ['POST'])]
    public function add(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response
    {
        //Extrait le body de la requête
        $data = $request->getContent();

//        //récupération d'un objet anonyme:
//        $data = json_decode($data);

        //récupération d'un tableau associatif :
//        $data = json_decode($data, true);

        $serie = $serializer->deserialize($data, Serie::class, 'json');

        $errors = $validator->validate($serie);
        if(count($errors) > 0){
            //méthode une :
//            $errorsJson = $serializer->serialize($errors, 'json');
//            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST);
            //méthode 2 :
            return $this->json($errors, Response::HTTP_BAD_REQUEST);

        }

        $serie->setDateCreated(new \DateTime());

        $entityManager->persist($serie);
        $entityManager->flush();

        return $this->json($serie, Response::HTTP_CREATED, [
            "Location" => $this->generateUrl(
                'api_series_one',
                ['id' => $serie->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL)],
            ['groups' => 'serie']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id): Response
    {
        //TODO update une série en JSON
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        //TODO delete une série en JSON
    }
}

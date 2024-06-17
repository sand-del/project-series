<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\String\s;

#[Route('/series', name: 'series_')]
class SerieController extends AbstractController
{
    #[Route('/{page}', name: 'list', requirements: ['page' => '\d+'])]
    public function list(SerieRepository $serieRepository, int $page = 1): Response
    {
        //gestion du cas ou le user essayer d'accéder à la page 0
        if ($page < 1) {
            $page = 1;
        }

        $nbSeriesMax = $serieRepository->count([]);
        $maxPages = ceil($nbSeriesMax / Serie::SERIES_PER_PAGE);

        //gestion du cas ou le user essayer d'accéder à une page suppérieur à la page maxi via l'url
        if($page > $maxPages) {
            $page = $maxPages;
        }

//        $series = $serieRepository->findAll();
//        $series = $serieRepository->findBy([], ["popularity" => "DESC"], 50, 0);
        $series = $serieRepository->findBestSeries($page);
        dump($series);
        return $this->render('series/list.html.twig', [
                'series' => $series,
                'currentPage' => $page,
                'maxPages' => $maxPages,
            ]
        );
    }

    #[Route('/create', name: 'create')]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        //création d'une instance de l'entité
        $serie = new Serie();
        //création du formulaire associé à l'instance de serie
        $serieForm = $this->createForm(SerieType::class, $serie);

        //extrait des informations de la requête http
        $serieForm->handleRequest($request);

        if($serieForm->isSubmitted() && $serieForm->isValid()){
            //permet d'aider l'IDE en "typant" le $file, ce qui nous permet d'avoir accès à l'autocompletion
            /**
             * @var UploadedFile $file
             */
            //récupération du fichier de type UploadedFile
            $file = $serieForm->get('poster')->getData();
            //création de son nom
            $newFilename = str_replace(' ', '', $serie->getName()) . '-' . uniqid() . '.' . $file->guessExtension();
            //sauvegarde dans le bon répertoire en le renommant
            $file->move($this->getParameter('serie_poster_directory'), $newFilename);
            //setter le nouveau nom dans l'objet
            $serie->setPoster($newFilename);
            $entityManager->persist($serie);
            $entityManager->flush();

            $this->addFlash('success', 'Series added successfully!');

            return $this->redirectToRoute('series_detail', ['id' => $serie->getId()]);
        }

        return $this->render('series/create.html.twig', [
            'serieForm' => $serieForm
        ]);
    }

    #[Route('/detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);

        if(!$serie){
            throw $this->createNotFoundException("Oops ! Series not found for id ".$id);
        }

        dump($serie);

        return $this->render('series/detail.html.twig', [
            'serie' => $serie]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(EntityManagerInterface $entityManager, Request $request, SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);

        if(!$serie){
            throw $this->createNotFoundException("Oops ! Series not found for id ".$id);
        }

        $serieForm = $this->createForm(SerieType::class, $serie);
        $serieForm->handleRequest($request);

        if($serieForm->isSubmitted() && $serieForm->isValid()){

            $serie->setDateModified(new \DateTime());

            $entityManager->persist($serie);
            $entityManager->flush();

            $this->addFlash('success', 'Series updated successfully!');
            return $this->redirectToRoute('series_detail', ['id' => $id]);
        }

        return $this->render('series/update.html.twig', [
            'serieForm' => $serieForm
        ]);

    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager, SerieRepository $serieRepository, int $id): Response
    {
        //récupération de l'instance de notre objet
        $serie = $serieRepository->find($id);

        if(!$serie){
            throw $this->createNotFoundException("Oops ! Series not found for id ".$id);
        }

        //possibilité de faire un try / catch pour s'assurer de la suppression
        $entityManager->remove($serie);
        $entityManager->flush();
        $this->addFlash('success', 'Series deleted successfully!');

        return $this->redirectToRoute('series_list');

    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

///**
// * autre possibilité pour les versions inférieurs à PHP8
// * @Route('main', name='app_main')
// */

class MainController extends AbstractController
{
    //équivalent comme les annotations sur java spring boot @Controller
    #[Route('/home', name: 'main_home')]//prefix le nom du controller, suffixe le nom de la méthode
    #[Route('', name: 'main_home_2')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/test', name: 'main_test')]
    public function test(): Response
    {
        $serie = ['title' => 'GOT', 'year' => '2011'];
        $serie2 = ['title' => 'The Boys', 'year' => '2019'];

        $username = '<h1>Sandra</h1>';

        return $this->render('main/test.html.twig', [
            'got' => $serie,
            'boys' => $serie2,
            'username' => $username
        ]);
    }

}

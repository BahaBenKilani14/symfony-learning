<?php

namespace App\Controller;

use App\Entity\Author;

use App\Form\AuthorType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showauthor/{name}', name: 'showauth')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name,
        ]);
    }

    #[Route('/listauthors', name: 'listauthors')]
    public function listAuthors(ManagerRegistry $doctrine): Response
    {
        $authorRepository = $doctrine->getRepository('App\Entity\Author');
        $authors = $authorRepository->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/deleteauth/{id}', name: 'deleteAuth')]
    public function deleteAuth($id, ManagerRegistry $doctrine): Response
    {
        $authorRepository = $doctrine->getRepository('App\Entity\Author');
        $authors = $authorRepository->find($id);
        $em = $doctrine->getManager();
        $em->remove($authors);
        $em->flush();
        return $this->redirectToRoute('listauthors');
    }

    #[Route('/addauthor', name: 'addAuthor')]
    public function addAuthor(Request $request, ManagerRegistry $doctrine): Response
    {
        $author= new Author;
        $em = $doctrine->getManager();
        $form=$this->createForm(AuthorType::class,$author);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('listauthors');
        }
        return $this->render('author/addauth.html.twig',[
            'formauth'=>$form->createView()
        ]);
    }

    #[Route('/updateauthor/{id}', name: 'updateauth')]
    public function updateauth($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $authorRepository = $doctrine->getRepository('App\Entity\Author');
        $author = $authorRepository->find($id);
        $em = $doctrine->getManager();
        $form=$this->createForm(AuthorType::class,$author);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em->flush();
            return $this->redirectToRoute('listauthors');
        }
        return $this->render('author/addauth.html.twig',[
            'formauth'=>$form->createView()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/listbooks', name: 'listbooks')]
    public function listAuthors(ManagerRegistry $doctrine): Response
    {
        $authorRepository = $doctrine->getRepository(Book::class);
        $books = $authorRepository->findAll();
        return $this->render('author/listBooks.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/listbooks/{id}', name: 'listbooksId')]
    public function listBooks($id, ManagerRegistry $doctrine): Response
    {
        $authorRepository = $doctrine->getRepository('App\Entity\Book');
        $books = $authorRepository->find($id);
        if ($id === null || $id === 0) {
            // list all books
            $books = $authorRepository->findAll();
        } else {
            $book = $authorRepository->find($id);
            if (!$book) {
                throw $this->createNotFoundException(sprintf('Book with id %d not found', $id));
            }
            // keep template compatible (it expects an array of books)
            $books = [$book];
        }

        return $this->render('author/listBooks.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/addbook', name: 'addBook')]
    public function addBook(Request $request, ManagerRegistry $doctrine): Response
    {
        $book = new Book;
        $em = $doctrine->getManager();
        $form=$this->createForm(BookType::class,$book);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('listbooks');
        }
        return $this->render('book/addbooks.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}

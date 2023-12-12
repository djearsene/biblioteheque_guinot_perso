<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Document;
use App\Entity\Genre;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/document')]
class DocumentController extends AbstractController
{
    #[Route('/', name: 'app_document_index', methods: ['GET'])]
    public function index(DocumentRepository $documentRepository): Response
    {
        return $this->render(
            'document/index.html.twig',
            [
                'documents' => $documentRepository->findAll(),
            ]
        );
    }

    #[Route('/new', name: 'app_document_new')]
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des images pour un article
            // Je récupère les images transmises depuis le formulaire à travers 1 articles et je vais chercher les données(getData)
            $images = $form->get('images')->getData();

            // On boucle sur les images (lorsque j'ai plusieurs images)
            foreach ($images as $image) {

                // On génère un nouveau nom de fichier pour eviter que 2 fichiers aient le meme nom
                $fichier = md5(uniqid()) . '.' . $image->guessExtension(); // guessExtension recupère l'extension du fichier

                // On passe le fichier dans le dossier uploads
                // Stockage de l'image sur le disque (l'image physique)
                $image->move(
                    $this->getParameter('images_directory'), // n'oublie le parametrage au niveau de Services.yaml
                    $fichier
                );

                // On va alors stocker (le nomde l'image) dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $document->addImages($img);
            }

            $entityManager->persist($document);
            $entityManager->flush();

            return $this->redirectToRoute('app_document_index', ['id' => $document->getId()]);
        }

        return $this->render(
            'document/new.html.twig',
            [
                'formDocument' => $form->createView(),
            ]
        );
    }

    #[Route('/{id}', name: 'app_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render(
            'document/show.html.twig',
            [
                'document' => $document,
            ]
        );
    }

    #[Route('/{id}/edit', name: 'app_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'document/edit.html.twig',
            [
                'document' => $document,
                'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: 'app_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $entityManager->remove($document);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
    }

    // Afficher les documents de la  Genre roman
    #[Route('/document-genre', name: 'documents.genre.roman')]
    public function indexDocumentsgenre_roman(DocumentRepository $documentRepository, Request $request)
    {
        $documents = $documentRepository->findByGenreroman();
        // Appel de la page pour affichage
        return $this->render(
            'document/genre.html.twig',
            [
                // passage du contenu de $location
                'documents' => $documents,
            ]
        );
    }
}

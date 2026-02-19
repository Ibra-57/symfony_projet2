<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Security\Voter\ClientVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/clients')]
#[IsGranted('ROLE_MANAGER')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index')]
    public function index(ClientRepository $clientRepository): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::VIEW, new Client());

        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAllOrderedByName(),
        ]);
    }

    #[Route('/new', name: 'app_client_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::CREATE);

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Le client a été créé avec succès.');

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit')]
    public function edit(Request $request, Client $client, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::EDIT, $client);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le client a été modifié avec succès.');

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_client_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Client $client, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::DELETE, $client);

        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $em->remove($client);
            $em->flush();

            $this->addFlash('success', 'Le client a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_client_index');
    }
}

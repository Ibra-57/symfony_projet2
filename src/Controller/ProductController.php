<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFlow;
use App\Repository\ProductRepository;
use App\Security\Voter\ProductVoter;
use App\Service\CsvExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/products')]
#[IsGranted('ROLE_USER')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $sort = $request->query->get('sort', 'price');
        $direction = $request->query->get('direction', 'DESC');

        $products = $productRepository->findAllSorted($sort, $direction);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    #[Route('/export', name: 'app_product_export')]
    public function export(CsvExportService $csvExportService): Response
    {
        return $csvExportService->exportProducts();
    }

    #[Route('/new', name: 'app_product_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, ProductFlow $flow, EntityManagerInterface $em): Response
    {
        $product = new Product();

        $flow->bind($product);

        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $em->persist($product);
                $em->flush();

                $flow->reset();

                $this->addFlash('success', 'Le produit a été créé avec succès.');

                return $this->redirectToRoute('app_product_index');
            }
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
            'flow' => $flow,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Le produit a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_product_index');
    }
}

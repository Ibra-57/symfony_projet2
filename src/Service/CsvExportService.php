<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    public function exportProducts(): StreamedResponse
    {
        $products = $this->productRepository->findAllOrderedByPriceDesc();

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Nom', 'Type', 'Description', 'Prix', 'Poids', 'Stock', 'Clé de licence'], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->getId(),
                    $product->getName(),
                    $product->getType(),
                    $product->getDescription(),
                    $product->getPrice(),
                    $product->getWeight(),
                    $product->getStock(),
                    $product->getLicenseKey(),
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="produits_export.csv"');

        return $response;
    }
}

<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CsvExportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportServiceTest extends TestCase
{
    private function createProduct(
        int $id,
        string $name,
        string $type,
        string $price,
        ?string $description = null,
        ?string $weight = null,
        ?int $stock = null,
        ?string $licenseKey = null,
    ): Product {
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn($id);
        $product->method('getName')->willReturn($name);
        $product->method('getType')->willReturn($type);
        $product->method('getPrice')->willReturn($price);
        $product->method('getDescription')->willReturn($description);
        $product->method('getWeight')->willReturn($weight);
        $product->method('getStock')->willReturn($stock);
        $product->method('getLicenseKey')->willReturn($licenseKey);
        return $product;
    }

    public function testExportProductsReturnsStreamedResponse(): void
    {
        $repository = $this->createMock(ProductRepository::class);
        $repository->method('findAllOrderedByPriceDesc')->willReturn([]);

        $service = new CsvExportService($repository);

        $response = $service->exportProducts();

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    public function testExportProductsHasCorrectHeaders(): void
    {
        $repository = $this->createMock(ProductRepository::class);
        $repository->method('findAllOrderedByPriceDesc')->willReturn([]);

        $service = new CsvExportService($repository);
        $response = $service->exportProducts();

        $this->assertSame('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame('attachment; filename="produits_export.csv"', $response->headers->get('Content-Disposition'));
    }

    public function testExportProductsOutputsCsvContent(): void
    {
        $physical = $this->createProduct(1, 'Sac Pro', 'physical', '129.99', 'Sac robuste', '1.8', 25);
        $digital = $this->createProduct(2, 'Guide PDF', 'digital', '29.99', 'Guide rando', null, null, 'KEY-123');

        $repository = $this->createMock(ProductRepository::class);
        $repository->method('findAllOrderedByPriceDesc')->willReturn([$physical, $digital]);

        $service = new CsvExportService($repository);
        $response = $service->exportProducts();

        ob_start();
        $response->sendContent();
        $output = ob_get_clean();

        $lines = array_filter(explode("\n", str_replace("\r\n", "\n", $output)));
        $lines = array_values($lines);

        $this->assertCount(3, $lines);

        $header = str_getcsv($lines[0], ';');
        $this->assertSame(['ID', 'Nom', 'Type', 'Description', 'Prix', 'Poids', 'Stock', 'Clé de licence'], $header);

        $row1 = str_getcsv($lines[1], ';');
        $this->assertSame('1', $row1[0]);
        $this->assertSame('Sac Pro', $row1[1]);
        $this->assertSame('physical', $row1[2]);
        $this->assertSame('129.99', $row1[4]);

        $row2 = str_getcsv($lines[2], ';');
        $this->assertSame('2', $row2[0]);
        $this->assertSame('digital', $row2[2]);
        $this->assertSame('KEY-123', $row2[7]);
    }

    public function testExportProductsWithEmptyList(): void
    {
        $repository = $this->createMock(ProductRepository::class);
        $repository->method('findAllOrderedByPriceDesc')->willReturn([]);

        $service = new CsvExportService($repository);
        $response = $service->exportProducts();

        ob_start();
        $response->sendContent();
        $output = ob_get_clean();

        $lines = array_filter(explode("\n", str_replace("\r\n", "\n", $output)));

        $this->assertCount(1, $lines);
        $header = str_getcsv(array_values($lines)[0], ';');
        $this->assertSame('ID', $header[0]);
    }
}

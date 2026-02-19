<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:import-products',
    description: 'Importe des produits depuis un fichier CSV situé dans /public',
)]
class ImportProductsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::REQUIRED, 'Nom du fichier CSV dans /public (ex: produits.csv)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename');
        $filepath = $this->projectDir . '/public/' . $filename;

        if (!file_exists($filepath)) {
            $io->error(sprintf('Le fichier "%s" n\'existe pas dans /public.', $filename));
            return Command::FAILURE;
        }

        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            $io->error('Impossible d\'ouvrir le fichier.');
            return Command::FAILURE;
        }

        $header = fgetcsv($handle, 0, ';');
        if ($header === false) {
            $io->error('Le fichier CSV est vide.');
            fclose($handle);
            return Command::FAILURE;
        }

        $header = array_map('strtolower', array_map('trim', $header));
        $count = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $data = array_combine($header, array_map('trim', $row));

            $product = new Product();
            $product->setName($data['nom'] ?? $data['name'] ?? '');
            $product->setType($data['type'] ?? 'physical');
            $product->setDescription($data['description'] ?? null);
            $product->setPrice($data['prix'] ?? $data['price'] ?? '0');
            $product->setWeight(isset($data['poids']) || isset($data['weight']) ? ($data['poids'] ?? $data['weight']) : null);
            $product->setStock(isset($data['stock']) ? (int) $data['stock'] : null);
            $product->setLicenseKey($data['clé de licence'] ?? $data['licensekey'] ?? $data['license_key'] ?? null);

            $this->em->persist($product);
            $count++;
        }

        fclose($handle);
        $this->em->flush();

        $io->success(sprintf('%d produit(s) importé(s) avec succès.', $count));

        return Command::SUCCESS;
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product1 = new Product();
        $product1->setType('physical');
        $product1->setName('Sac à dos Randonnée Pro 50L');
        $product1->setDescription('Sac robuste et ergonomique pour longues randonnées en montagne. Compartiments multiples et dos ventilé.');
        $product1->setPrice('129.99');
        $product1->setWeight('1.8');
        $product1->setStock(25);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setType('physical');
        $product2->setName('Tente 3 Saisons Ultra-Light');
        $product2->setDescription('Tente ultra-légère 2 personnes, parfaite pour le trekking. Résistante aux intempéries.');
        $product2->setPrice('289.00');
        $product2->setWeight('2.3');
        $product2->setStock(15);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setType('physical');
        $product3->setName('Chaussures de Randonnée Premium');
        $product3->setDescription('Chaussures montantes imperméables avec semelle Vibram. Confort maximal sur terrain difficile.');
        $product3->setPrice('189.99');
        $product3->setWeight('1.2');
        $product3->setStock(40);
        $manager->persist($product3);

        $product4 = new Product();
        $product4->setType('physical');
        $product4->setName('Kit Bivouac Complet Haut de Gamme');
        $product4->setDescription('Kit complet incluant tente 4 saisons, sac de couchage -15°C, matelas isolant et réchaud.');
        $product4->setPrice('749.00');
        $product4->setWeight('5.5');
        $product4->setStock(8);
        $manager->persist($product4);

        $product5 = new Product();
        $product5->setType('digital');
        $product5->setName('Guide PDF Tour du Mont Blanc');
        $product5->setDescription('Guide complet du TMB avec cartes détaillées, étapes, refuges et conseils pratiques. 180 pages.');
        $product5->setPrice('29.99');
        $product5->setLicenseKey('TMB-2024-GUIDE-5X7Y9');
        $manager->persist($product5);

        $product6 = new Product();
        $product6->setType('digital');
        $product6->setName('Carte Interactive GR20 Corse');
        $product6->setDescription('Application mobile avec cartographie offline, tracés GPS et points d\'intérêt du GR20.');
        $product6->setPrice('19.99');
        $product6->setLicenseKey('GR20-MAPS-APP-3K8L2');
        $manager->persist($product6);

        $product7 = new Product();
        $product7->setType('digital');
        $product7->setName('Formation Alpinisme Complète');
        $product7->setDescription('Pack vidéo de formation alpinisme : techniques de grimpe, sécurité, orientation. 25h de contenu.');
        $product7->setPrice('149.00');
        $product7->setLicenseKey('ALPI-FORM-VIDEO-9M4N6');
        $manager->persist($product7);

        $product8 = new Product();
        $product8->setType('digital');
        $product8->setName('Collection Guides Premium - Tous les Sommets');
        $product8->setDescription('Collection complète de 50 guides PDF couvrant les plus beaux sommets d\'Europe. Accès à vie + mises à jour.');
        $product8->setPrice('599.00');
        $product8->setLicenseKey('PREMIUM-COLLECTION-2024-7P8Q1');
        $manager->persist($product8);

        $manager->flush();
    }
}

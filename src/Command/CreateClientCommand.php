<?php

namespace App\Command;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-client',
    description: 'Crée un nouveau client de façon interactive',
)]
class CreateClientCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private ClientRepository $clientRepository,
        private ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Création d\'un nouveau client');

        $firstname = $io->ask('Prénom', null, function (?string $value) {
            if (empty($value)) {
                throw new \RuntimeException('Le prénom ne peut pas être vide.');
            }
            if (!preg_match('/^[\p{L}\s\-\']+$/u', $value)) {
                throw new \RuntimeException('Le prénom ne peut contenir que des lettres, espaces et tirets.');
            }
            return $value;
        });

        $lastname = $io->ask('Nom', null, function (?string $value) {
            if (empty($value)) {
                throw new \RuntimeException('Le nom ne peut pas être vide.');
            }
            if (!preg_match('/^[\p{L}\s\-\']+$/u', $value)) {
                throw new \RuntimeException('Le nom ne peut contenir que des lettres, espaces et tirets.');
            }
            return $value;
        });

        $email = $io->ask('Email', null, function (?string $value) {
            if (empty($value)) {
                throw new \RuntimeException('L\'email ne peut pas être vide.');
            }
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('L\'adresse email n\'est pas valide.');
            }
            return $value;
        });

        $existing = $this->clientRepository->findOneBy(['email' => $email]);
        if ($existing !== null) {
            $io->error(sprintf('Un client avec l\'email "%s" existe déjà.', $email));
            return Command::FAILURE;
        }

        $phoneNumber = $io->ask('Téléphone (optionnel)', null, function (?string $value) {
            if ($value !== null && !preg_match('/^[\d\s\+\-\(\)\.]{7,20}$/', $value)) {
                throw new \RuntimeException('Le numéro de téléphone n\'est pas valide.');
            }
            return $value ?: null;
        });

        $address = $io->ask('Adresse (optionnel)');

        $client = new Client();
        $client->setFirstname($firstname);
        $client->setLastname($lastname);
        $client->setEmail($email);
        $client->setPhoneNumber($phoneNumber);
        $client->setAddress($address ?: null);

        $errors = $this->validator->validate($client);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        $io->section('Récapitulatif');
        $io->table(
            ['Champ', 'Valeur'],
            [
                ['Prénom', $firstname],
                ['Nom', $lastname],
                ['Email', $email],
                ['Téléphone', $phoneNumber ?? '—'],
                ['Adresse', $address ?: '—'],
            ]
        );

        if (!$io->confirm('Confirmer la création de ce client ?', true)) {
            $io->warning('Création annulée.');
            return Command::SUCCESS;
        }

        $this->em->persist($client);
        $this->em->flush();

        $io->success(sprintf('Client "%s %s" créé avec succès (ID: %d).', $firstname, $lastname, $client->getId()));

        return Command::SUCCESS;
    }
}

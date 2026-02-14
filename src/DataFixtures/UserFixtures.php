<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstname('Admin');
        $admin->setLastname('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $managerUser = new User();
        $managerUser->setEmail('manager@example.com');
        $managerUser->setFirstname('Manager');
        $managerUser->setLastname('User');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'password'));
        $manager->persist($managerUser);

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstname('Standard');
        $user->setLastname('User');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('sophie.martin@example.com');
        $user2->setFirstname('Sophie');
        $user2->setLastname('Martin');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password'));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail('pierre.dubois@example.com');
        $user3->setFirstname('Pierre');
        $user3->setLastname('Dubois');
        $user3->setRoles(['ROLE_MANAGER']);
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'password'));
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail('marie.bernard@example.com');
        $user4->setFirstname('Marie');
        $user4->setLastname('Bernard');
        $user4->setRoles(['ROLE_USER']);
        $user4->setPassword($this->passwordHasher->hashPassword($user4, 'password'));
        $manager->persist($user4);

        $user5 = new User();
        $user5->setEmail('lucas.petit@example.com');
        $user5->setFirstname('Lucas');
        $user5->setLastname('Petit');
        $user5->setRoles(['ROLE_USER']);
        $user5->setPassword($this->passwordHasher->hashPassword($user5, 'password'));
        $manager->persist($user5);

        $manager->flush();
    }
}

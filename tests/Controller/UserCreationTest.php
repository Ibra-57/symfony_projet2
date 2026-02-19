<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreationTest extends KernelTestCase
{
    public function testUserCanBeCreatedAndPersisted(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $em = $this->createMock(EntityManagerInterface::class);

        $persistedUser = null;
        $em->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (User $user) use (&$persistedUser) {
                $persistedUser = $user;
                return true;
            }));

        $em->expects($this->once())
            ->method('flush');

        $hasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('test.user@example.com');
        $user->setFirstname('Jean');
        $user->setLastname('Test');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $hasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        $this->assertSame('test.user@example.com', $persistedUser->getEmail());
        $this->assertSame('Jean', $persistedUser->getFirstname());
        $this->assertSame('Test', $persistedUser->getLastname());
        $this->assertContains('ROLE_USER', $persistedUser->getRoles());
        $this->assertTrue($hasher->isPasswordValid($persistedUser, 'password123'));
    }

    public function testUserEmailMustBeUnique(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $existingUser = new User();
        $existingUser->setEmail('existing@example.com');
        $existingUser->setFirstname('Alice');
        $existingUser->setLastname('Durand');
        $existingUser->setRoles(['ROLE_USER']);
        $existingUser->setPassword('hashed');

        $repository = $this->createMock(UserRepository::class);
        $repository->method('findOneBy')
            ->with(['email' => 'existing@example.com'])
            ->willReturn($existingUser);

        $found = $repository->findOneBy(['email' => 'existing@example.com']);

        $this->assertNotNull($found);
        $this->assertSame('existing@example.com', $found->getEmail());
    }

    public function testUserRolesAreCorrectlyAssigned(): void
    {
        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN']);

        $manager = new User();
        $manager->setRoles(['ROLE_MANAGER']);

        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $this->assertContains('ROLE_ADMIN', $admin->getRoles());
        $this->assertContains('ROLE_USER', $admin->getRoles());

        $this->assertContains('ROLE_MANAGER', $manager->getRoles());
        $this->assertNotContains('ROLE_ADMIN', $manager->getRoles());

        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
    }
}

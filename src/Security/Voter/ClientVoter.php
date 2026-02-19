<?php

namespace App\Security\Voter;

use App\Entity\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    public const VIEW = 'CLIENT_VIEW';
    public const CREATE = 'CLIENT_CREATE';
    public const EDIT = 'CLIENT_EDIT';
    public const DELETE = 'CLIENT_DELETE';

    public function __construct(
        private Security $security,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (in_array($attribute, [self::CREATE])) {
            return true;
        }

        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isManager = $this->security->isGranted('ROLE_MANAGER');

        return match ($attribute) {
            self::VIEW, self::CREATE, self::EDIT => $isAdmin || $isManager,
            self::DELETE => $isAdmin,
            default => false,
        };
    }
}

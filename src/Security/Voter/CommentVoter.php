<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    const NEW_COMMENT = 'new_comment';

    const EDIT_COMMENT = 'edit_comment';

    const DELETE_COMMENT = 'delete_comment';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::NEW_COMMENT, self::EDIT_COMMENT, self::DELETE_COMMENT]) && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::NEW_COMMENT:
                return $this->canNew($subject, $user);
            case self::EDIT_COMMENT:
                return $this->canEdit($subject, $user);
            case self::DELETE_COMMENT:
                return $this->canDelete($subject, $user);
        }

        return false;
    }

    public function canNew(User $subject, User $user)
    {
        return $subject === $user;
    }

    public function canEdit(User $subject, User $user)
    {
        return $subject === $user;
    }

    public function canDelete(User $subject, User $user)
    {
        return $subject === $user;
    }
}

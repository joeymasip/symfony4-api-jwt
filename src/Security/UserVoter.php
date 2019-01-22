<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class UserVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // only vote on User objects inside this voter
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        // you know $subject is a User object, thanks to supports
        /** @var User $userSubject */
        $userSubject = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($userSubject, $user);
            case self::EDIT:
                return $this->canEdit($userSubject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $userSubject, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($userSubject, $user)) {
            return true;
        }

        // the User object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return $user === $userSubject;
    }

    private function canEdit(User $userSubject, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $userSubject;
    }
}
<?php

namespace Lw\Infrastructure\Persistence\InMemory\User;

use Lw\Domain\Model\User\User;
use Lw\Domain\Model\User\UserId;
use Lw\Domain\Model\User\UserRepository;

class InMemoryUserRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private $users = array();

    /**
     * {@inheritdoc}
     */
    public function userOfId(UserId $userId)
    {
        if (!isset($this->users[$userId->id()])) {
            return null;
        }

        return $this->users[$userId->id()];
    }

    /**
     * {@inheritdoc}
     */
    public function userOfEmail($email)
    {
        foreach ($this->users as $user) {
            if ($user->email() === $email) {
                return $user;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(User $user)
    {
        $this->users[$user->id()->id()] = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function nextIdentity()
    {
        return new UserId();
    }
}

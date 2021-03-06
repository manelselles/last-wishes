<?php

namespace Lw\Infrastructure\Persistence\InMemory\Wish;

use Lw\Domain\Model\User\UserId;
use Lw\Domain\Model\Wish\Wish;
use Lw\Domain\Model\Wish\WishId;
use Lw\Domain\Model\Wish\WishRepository;

class InMemoryWishRepository implements WishRepository
{
    /**
     * @var Wish[]
     */
    private $wishes = array();

    /**
     * {@inheritdoc}
     */
    public function wishOfId(WishId $wishId)
    {
        if (!isset($this->wishes[$wishId->id()])) {
            return null;
        }

        return $this->wishes[$wishId->id()];
    }

    /**
     * {@inheritdoc}
     */
    public function wishesOfUserId(UserId $userId)
    {
        $wishes = array();
        foreach ($this->wishes as $wish) {
            if ($wish->userId()->equals($userId)) {
                $wishes[] = $wish;
            }
        }

        return $wishes;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Wish $wish)
    {
        $this->wishes[$wish->id()->id()] = $wish;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Wish $wish)
    {
        unset($this->wishes[$wish->id()->id()]);
    }

    /**
     * {@inheritdoc}
     */
    public function nextIdentity()
    {
        return new WishId();
    }
}

<?php

namespace Lw\Application\Service\Wish;

use Lw\Domain\Model\User\User;
use Lw\Domain\Model\Wish\WishEmail;
use Lw\Infrastructure\Persistence\InMemory\User\InMemoryUserRepository;
use Lw\Infrastructure\Persistence\InMemory\Wish\InMemoryWishRepository;

class DeleteWishServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryUserRepository
     */
    private $userRepository;

    /**
     * @var InMemoryWishRepository
     */
    private $wishRepository;

    /**
     * @var DeleteWishService
     */
    private $deleteWishService;

    /**
     * @var User
     */
    private $dummyUser;

    /**
     * @var \Lw\Domain\Model\Wish\Wish
     */
    private $dummyWish;

    public function setUp()
    {
        $this->setupUserRepository();
        $this->setupWishRepository();

        $this->deleteWishService = new DeleteWishService(
            $this->userRepository,
            $this->wishRepository
        );
    }

    private function setupUserRepository()
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->dummyUser = new User($this->userRepository->nextIdentity(), 'irrelevant@email.com', 'irrelevant');
        $this->userRepository->persist($this->dummyUser);
    }

    private function setupWishRepository()
    {
        $this->wishRepository = new InMemoryWishRepository();
        $this->dummyWish = $this->dummyUser->makeWish($this->wishRepository->nextIdentity(), 'irrelevant@email.com', 'content');
        $this->wishRepository->persist($this->dummyWish);
    }

    /**
     * @test
     * @expectedException \Lw\Domain\Model\Wish\WishDoesNotExistException
     */
    public function removingNonExistingWishThrowsException()
    {
        $this->deleteWishService->execute(
            new DeleteWishRequest('non-existent', $this->dummyUser->id()->id())
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function removeWishFromAUserThatDoesNotOwnItShouldThrowException()
    {
        $wish = new WishEmail(
            $this->wishRepository->nextIdentity(),
            $this->userRepository->nextIdentity(),
            'irrelevant@email.com',
            'content'
        );

        $this->wishRepository->persist($wish);

        $this->deleteWishService->execute(
            new DeleteWishRequest($wish->id()->id(), $this->dummyUser->id()->id())
        );
    }

    /**
     * @test
     */
    public function itShouldRemoveWish()
    {
        $this->deleteWishService->execute(
            new DeleteWishRequest($this->dummyWish->id()->id(), $this->dummyUser->id()->id())
        );

        $this->assertNull($this->wishRepository->wishOfId($this->dummyWish->id()));
    }
}

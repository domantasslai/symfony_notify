<?php

namespace App\Repository;

use App\Entity\User;
use App\Exception\WrongInputException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find a user by email or create a new user if not found.
     *
     * This method searches for a user in the repository based on the provided email address. If a user with the given email does not exist, a new user is created using the provided creatableValues array, which should include a 'phone_number'. The new user is then persisted to the database.
     *
     * @param string $email The email address to search for or use when creating a new user.
     * @param array $creatableValues An array of values to create a new user (e.g., ['phone_number' => '1234567890']).
     * @param EntityManagerInterface $entityManager The entity manager used to persist the new user.
     *
     * @return User|null The found user or the newly created user if not found.
     *
     * @throws NonUniqueResultException
     * @throws WrongInputException
     */
    public function findByEmailOrCreate(string $email, array $creatableValues, EntityManagerInterface $entityManager): ?User
    {
        // Attempt to find a user by email.
        $find = $this->createQueryBuilder('u')
            ->where("u.email = :val")
            ->setParameter('val', $email)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();

        if ($find === null) {
            // If no user is found, check if 'phone_number' is provided in $creatableValues array.
            if (!array_key_exists('phone_number', $creatableValues)) throw new WrongInputException('Wrong input! Was expected phone_number.');

            // Create a new user with the provided email, phone_number, and a timestamp for creation.
            $user = (new User())->setEmail($email)
                ->setPhoneNumber($creatableValues['phone_number'])
                ->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Return either the found user or the newly created user.
        return $find ?? $user;
    }
}

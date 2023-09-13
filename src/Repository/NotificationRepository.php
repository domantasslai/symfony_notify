<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Create and persist a new notification entity.
     *
     * This method creates a new notification entity based on the provided data and persists it to the database.
     * The data array should include 'user', 'chanel', 'chanelExecutor', 'message', 'to', and the notification's creation timestamp.
     *
     * @param array $data An array containing data to create the notification entity.
     */
    public function create(array $data): void
    {
        $notification = (new Notification())
            ->setUser($data['user'])
            ->setChanel($data['chanel'])
            ->setChanelExecutor($data['chanelExecutor'])
            ->setMessage($data['message'])
            ->setSendingTo($data['to'])
            ->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }


    /**
     * Count the number of notifications inserted within the last hour for a specific user.
     *
     * This method counts the notifications inserted within the last hour for a given user entity.
     *
     * @param User $user The user for whom notifications are to be counted.
     *
     * @return int The number of notifications inserted within the last hour for the user.
     *
     * @throws NonUniqueResultException If multiple results are found (should not typically occur).
     * @throws NoResultException If no results are found within the specified criteria.
     */
    public function countNotificationsInsertedOneHourAgo(User $user): int
    {
        $oneHourAgo = new \DateTime('-1 hour');

        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where("n.user = :user")
            ->andWhere('n.created_at >= :oneHourAgo')
            ->setParameter('user', $user)
            ->setParameter('oneHourAgo', $oneHourAgo)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

<?php
namespace PHPDish\Bundle\UserBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPDish\Bundle\CoreBundle\Service\PaginatorTrait;
use PHPDish\Bundle\UserBundle\Model\UserInterface;

class UserManager implements UserManagerInterface
{
    use PaginatorTrait;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByName($username)
    {
        return $this->entityManager->getRepository('PHPDishUserBundle:User')
            ->findOneBy(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->getRepository()
            ->findOneBy(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestUsers($limit)
    {
        return $this->getRepository()->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'desc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserFollowing(UserInterface $user, $page, $limit = null)
    {
        $query = $this->getRepository()->createQueryBuilder('u')
            ->innerJoin('u.followers', 'f')
            ->where('f.id = :userId')->setParameter('userId', $user->getId())
            ->getQuery();
        return $this->createPaginator($query, $page, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserFollowers(UserInterface $user, $page, $limit = null)
    {
        $query = $this->getRepository()->createQueryBuilder('u')
            ->innerJoin('u.following', 'f')
            ->where('f.id = :userId')->setParameter('userId', $user->getId())
            ->getQuery();
        return $this->createPaginator($query, $page, $limit);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository('PHPDishUserBundle:User');
    }
}
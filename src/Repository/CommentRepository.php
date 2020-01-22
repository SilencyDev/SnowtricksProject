<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Comment::class);
        $this->security = $security;
    }

    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.validated = 1');
    }

    /**
     * @return Comment[]
     */
    public function findAllInvisible(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.validated = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findAllVisible(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findAllVisibleFromTrick(int $snowtrickId): array
    {
        return $this->findVisibleQuery()
            ->andWhere('c.snowtrick =  :snowtrick')
            ->setParameter('snowtrick', $snowtrickId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findMyComments(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.author = :user')
            ->orderBy('c.id', 'DESC')
            ->setParameter('user', $this->security->getUser()->getUsername())
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

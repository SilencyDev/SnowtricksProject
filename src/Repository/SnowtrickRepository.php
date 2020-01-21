<?php

namespace App\Repository;

use App\Entity\Snowtrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * @method Snowtrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Snowtrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Snowtrick[]    findAll()
 * @method Snowtrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnowtrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Snowtrick::class);
        $this->security = $security;
    }

    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.validated = 1');
    }

    /**
     * @return Snowtrick[]
     */
    public function findAllInvisible(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.validated = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Snowtrick[]
     */
    public function findAllVisible(): array
    {
        return $this->findVisibleQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Snowtrick[]
     */
    public function findMyTricks(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.author = :user')
            ->setParameter('user', $this->security->getUser()->getUsername())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Snowtrick[]
     */
    public function findLatest(): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults('15')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Snowtrick[] Returns an array of Snowtrick objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Snowtrick
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

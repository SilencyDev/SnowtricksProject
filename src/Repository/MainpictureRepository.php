<?php

namespace App\Repository;

use App\Entity\Mainpicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Mainpicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mainpicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mainpicture[]    findAll()
 * @method Mainpicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainpictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mainpicture::class);
    }

    /**
     * @return Mainpicture[]
     */
    public function findOneById($value): ?Mainpicture
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

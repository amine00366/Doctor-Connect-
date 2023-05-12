<?php

namespace App\Repository;

use App\Entity\Postcomment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Postcomment>
 *
 * @method Postcomment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postcomment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postcomment[]    findAll()
 * @method Postcomment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostcommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postcomment::class);
    }

    
    public function getPostComments($post_id)
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'u.username')
            ->join('c.user', 'u')
            ->where('c.post = :id')
            ->setParameter('id', $post_id)
            ->getQuery()
            ->getResult();
    }
public function countBypost(){

        $query = $this->getEntityManager()->createQuery("
            SELECT SUBSTRING(a.posted_at, 1, 10) as postDate, COUNT(a) as count FROM App\Entity\Postcomment a GROUP BY postDate
        ");
        return $query->getResult();
    }

//    /**
//     * @return Postcomment[] Returns an array of Postcomment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Postcomment
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Ordonnance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ordonnance>
 *
 * @method Ordonnance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ordonnance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ordonnance[]    findAll()
 * @method Ordonnance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdonnanceRepository extends ServiceEntityRepository
{
    
    public function save(Ordonnance $entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ordonnance::class);
    }

    public function add(Ordonnance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findmn()
    {
       $qb = $this ->createQueryBuilder('o')
       ->select('m.nom')
       ->leftJoin('o.Doctor','m');
       return $qb -> getQuery()->getResult();
    }
    public function remove(Ordonnance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

       
            $this->getEntityManager()->flush();
        
    }

//    /**
//     * @return Ordonnance[] Returns an array of Ordonnance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ordonnance
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
/**
	 * Retrieve the list of active orders with all their actives packages
	 * @return QueryBuilder
	 */
	private function getOrderQueryBuilder(){
		// Select the orders and their packages
		$queryBuilder = $this->createQueryBuilder('o')
			->addSelect('p');
		
		// Add the package relation
		$queryBuilder->leftJoin('o.packages','p');
		
		// Add WHERE clause
		$queryBuilder->where('o.deleted = 0')
			->andWhere('p.deleted = 0');
		
		//Return the QueryBuilder
		return $queryBuilder;
	}
    
}

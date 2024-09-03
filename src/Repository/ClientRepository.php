<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findAllOrderByName($idUser)
    {

        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftjoin('c.IdUser', 'u')
            ->addSelect('u')
            ->andWhere('u.id = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('c.name',  'ASC')
            ->getQuery()
            ->getResult();
          
    }
    public function findAllByIdClient()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.idProjectForClient', 'p.IdClient')
            ->orderBy('c.name',  'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByIdUser($userId)
    {
        return $this->createQueryBuilder('c')
        ->select('c')
        ->join('c.IdUser', 'u')
        ->where('u.id = :userId')
        ->setParameter('userId', $userId)
        ->orderBy('c.name', 'ASC')
        ->getQuery()
        ->getResult();
    }
    // /**
    //  * @return Client[] Returns an array of Client objects
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
    public function findOneBySomeField($value): ?Client
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

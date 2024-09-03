<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

     /**
      * @return Project[] Returns an array of Project objects
      */
    public function findAllByUser($idUser)
    {

        return $this->createQueryBuilder('p')
            ->andWhere('p.IdUser = :idUser')
            ->setParameter('idUser', $idUser)
            ->getQuery()
            ->getResult();
    }
    public function findAllOrderByDate($idUser){

        return $this->createQueryBuilder('p')
            ->andWhere('p.IdUser = :idUser')
            ->setParameter('idUser', $idUser)
        ->orderBy('p.startDate',  'DESC')
        ->getQuery()
        ->getResult()
        ;
    }
    public function findAllOrderByName($idUser){

        return $this->createQueryBuilder('p')
        ->andWhere('p.IdUser = :idUser')
        ->setParameter('idUser', $idUser)
        ->orderBy('p.name',  'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    public function findAllOrderByEndDate($idUser)
    {

        return $this->createQueryBuilder('p')
            ->andWhere('p.IdUser = :idUser')
            ->setParameter('idUser', $idUser)
            ->andWhere("p.endDate IS NULL",)
            ->orderBy('p.name',  'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findOneBySomeField($name): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findByIdClient($clientId, $idUser)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.IdClient = :val')
            ->setParameter('val', $clientId)
            ->andWhere('c.IdUser = :idUser')
            ->setParameter('idUser',$idUser)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findAllByIdClient($idUser)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.IdUser = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
 /*   public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

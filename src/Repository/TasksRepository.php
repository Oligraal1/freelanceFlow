<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Tasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    public function countTotalHour(Project $project)
    {
        $idtask = $project->getId();

        return $this->createQueryBuilder('t')
            ->andWhere('t.idTask = :idTask')
            ->setParameter('idTask', $idtask)
            ->select('SUM(t.hourWorked) as totalHour')
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function countTotalHourBetweenTwoDates(Project $project, $from, $to)
    {
        $idtask = $project->getId();

        return $this->createQueryBuilder('t')
            ->andWhere('t.idTask = :idTask')
            ->setParameter('idTask', $idtask)
            ->andWhere('t.taskDate BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->select('SUM(t.hourWorked) as totalHour')
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function FiveFirstOrderByDate(Project $project){
        $idtask = $project->getId();
        $offset = 0;
        $limit = 5;
        return $this->createQueryBuilder('t')
            ->andWhere('t.idTask = :idTask')
            ->setParameter('idTask', $idtask)
            ->orderBy('t.taskDate', 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery()
            ->getResult();
    }

    public function findAllOrderByDate(Project $project)
    {
        $idtask = $project->getId();

        return $this->createQueryBuilder('t')
            ->andWhere('t.idTask = :idTask')
            ->setParameter('idTask', $idtask)
            ->orderBy('t.taskDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function FiveFirstOrderByDateAsc(Project $project)
    {
        $idtask = $project->getId();
        $offset = 0;
        $limit = 5;
        return $this->createQueryBuilder('t')
            ->andWhere('t.idTask = :idTask')
            ->setParameter('idTask', $idtask)
            ->orderBy('t.taskDate', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findAllOrderByDateAsc(Project $project)
    {
        $idtask = $project->getId();

        return $this->createQueryBuilder('t')
            ->andWhere('t.idTask = :idTask')
            ->setParameter('idTask', $idtask)
            ->orderBy('t.taskDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Tasks[] Returns an array of Tasks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tasks
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

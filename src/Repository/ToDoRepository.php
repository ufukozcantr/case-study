<?php

namespace App\Repository;

use App\Entity\ToDo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ToDo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ToDo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ToDo[]    findAll()
 * @method ToDo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ToDoRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ToDoRepository constructor.
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, ToDo::class);
    }

    // /**
    //  * @return ToDo[] Returns an array of ToDo objects
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
    public function findOneBySomeField($value): ?ToDo
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param array $data
     * @return ToDo
     */
    public function toDoCreate(array $data)
    {
        $toDo = new ToDo();
        $toDo->setTitle($data['title']);
        $toDo->setLevel($data['level']);
        $toDo->setEstimatedDuration($data['estimatedDuration']);

        $this->entityManager->persist($toDo);
        $this->entityManager->flush();

        return $toDo;
    }

    /**
     * @param $allToDoList
     * @param $allDeveloperList
     * @return array
     */
    public function toDoListAssign($allToDoList, $allDeveloperList): array
    {
        $developers = [];
        $assignCount = [];
        foreach ($allDeveloperList as $dev) {
            $developers[$dev->getId()]['weekDuration'] = $dev->getLevel() * $dev->getEstimatedDuration() * 45;
            $developers[$dev->getId()]['name'] = $dev->getName();
            $assignCount[$dev->getId()] = 0;
            $assignToDo[$dev->getId()] = [];
        }

        $week = 1;
        $assignToDo = [];
        foreach ($allToDoList as $toDo) {
            $devId = $this->findAvailableDev($week, $assignCount, $developers);
            if ($devId){
                $assignToDo[$devId][$week][] = $toDo;
                $assignCount[$devId] += $toDo->getLevel() * $toDo->getEstimatedDuration();
            }else{
                $week++;
                $devId = $this->findAvailableDev($week, $assignCount, $developers);
                $assignToDo[$devId][$week][] = $toDo;
                $assignCount[$devId] += $toDo->getLevel() * $toDo->getEstimatedDuration();
            }
        }

        foreach ($developers as $id => $item) {
            $developers[$id]['week'] = round($assignCount[$id] / $item['weekDuration'], 2);
            $developers[$id]['assignToDo'] = $assignToDo[$id];
        }

        $sortWeek = array_column($developers, 'week');
        array_multisort($sortWeek, SORT_DESC, $developers);

        return $developers;
    }

    /**
     * @param $week
     * @param $assignCount
     * @param $developers
     * @return bool|int|string|null
     */
    private function findAvailableDev($week, $assignCount, $developers)
    {
        $currentDevId = array_key_first($assignCount);
        $assigned = false;
        foreach ($developers as $devId => $dev) {
            if ($dev['weekDuration'] * $week > $assignCount[$devId] || $assignCount[$devId] == 0){
                $currentDevId = $devId;
                $assigned = true;
            }
        }

        if ($assigned)
            return $currentDevId;

        return false;
    }
}

<?php

namespace App\Controller;

use App\Repository\DeveloperRepository;
use App\Repository\ToDoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ToDoController
 * @package App\Controller
 */
class ToDoController extends AbstractController
{
    /**
     * @var ToDoRepository
     */
    private $toDoRepository;
    /**
     * @var DeveloperRepository
     */
    private $developerRepository;

    /**
     * ToDoController constructor.
     * @param ToDoRepository $toDoRepository
     * @param DeveloperRepository $developerRepository
     */
    public function __construct(ToDoRepository $toDoRepository, DeveloperRepository $developerRepository)
    {
        $this->toDoRepository = $toDoRepository;
        $this->developerRepository = $developerRepository;
    }

    /**
     * @Route("/todo/plan", name="developerCreate")
     */
    public function plan()
    {
        $allToDoList = $this->toDoRepository->findAll();
        $allDeveloperList = $this->developerRepository->findAll();

        $developers = $this->toDoRepository->toDoListAssign($allToDoList, $allDeveloperList);

        return $this->render('plan.html.twig', ['message' => 'To Do list assigned to developers', 'developers' => $developers]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Tasks;
use App\Entity\Project;
use App\Form\TasksType;
use App\Repository\ProjectRepository;
use App\Repository\TasksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/tasks")
 */

class TasksController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   
    /**
     * @Route("/{id}", name="tasks_index", methods={"GET"}, options={"expose"=true}, requirements={"id":"\d+"})
     */
    public function index(
        int $id,
        TasksRepository $repo,
        ProjectRepository $projRepo,
        Request $request
    ): Response {

        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();

        // Find the Project by ID
        $project = $projRepo->find($id);

        if (!$project) {
            throw $this->createNotFoundException('No project found for id '.$id);
        }

        $projects = $projRepo->findAllByUser($idUser);
        $tasks5 = $repo->FiveFirstOrderByDate($project);
        $tasks = $repo->findAllOrderByDate($project);
        $tasks5Asc = $repo->FiveFirstOrderByDateAsc($project);
        $tasksAsc = $repo->findAllOrderByDateAsc($project);
        $hour = $repo->countTotalHour($project);

        return $this->render('tasks/index.html.twig', [
            'controller_name' => 'TasksController',
            'tasks' => $tasks,
            'tasks5' => $tasks5,
            'tasksAsc' => $tasksAsc,
            'tasks5Asc' => $tasks5Asc,
            'project' => $project,
            'user' => $user,
            "counthour" => implode(',', $hour),
            'projects' => $projects
        ]);
    }
    /**
     * gère le tableau des tâches en ajax
     * @Route("/tri/{id}", name="tasks_tri")
     * 
     * @return void
     */
    public function tri(int $id, Request $request, ProjectRepository $projectRepo, TasksRepository $repo, ProjectRepository $projRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $project = $projRepo->find($id);
        $tasks = $repo->findAllOrderByDate($project);
        $tasks5 = $repo->FiveFirstOrderByDate($project);
        $tasksAsc = $repo->findAllOrderByDateAsc($project);
        $projects = $projRepo->findAllByUser($idUser);
        $triValue = $request->request->get('triValue');
        
        if ($triValue == 'croissant') {
            $tasks;
        } else {
            $tasksAsc;
        }
        
        return $this->render('tasks/index.html.twig', [
            'controller_name' => 'TasksController',
            'tasks' => $tasks,
            'tasksAsc' => $tasksAsc,
            'project' => $project,
            'user' => $user,
        ]);
    }

    /**
     * gère le tableau des tâches en ajax
     * @Route("/ajax/{id}", name="tasks_ajax")
     * 
     * @return void
     */
    public function ajaxTask(int $id, Request $request, ProjectRepository $projectRepo, TasksRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $project = $projectRepo->findOneBy(['id'=> $id]);
        $tasks = $repo->findAllOrderByDate($project);
        $tasksAsc = $repo->findAllOrderByDateAsc($project);
        $croissant = $request->request->get('croissant');
        $decroissant = $request->request->get('decroissant');
        $triValue =$request->request->get('triValue');
      // dd($triValue);
        foreach ($tasks as $task) {
            $taskAll[]= [
                'id'=> $task->getId(),
                'name'=>$task->getName(),
                'hour'=>$task->getHourWorked(),
                'date'=>$task->getTaskDate()
                ];
            
        }
        foreach ($tasksAsc as $taskAsc) {
            $taskAllAsc[] =[
                'id' => $taskAsc->getId(),
                'name' => $taskAsc->getName(),
                'hour' => $taskAsc->getHourWorked(),
                'date' => $taskAsc->getTaskDate()
                ];
            
        }

        $value = [
            'croissant' => $croissant,
            'decroissant' => $decroissant,
            'triValue' => $triValue,
            'tasks' => $taskAll,
            'tasksAsc' =>$taskAllAsc
        ];
        $response = new JsonResponse();
        $response->setData(['data' => $value]);
       $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{id}/new", name="tasks_new", methods={"GET","POST"})
     */
    public function new(int $id, Request $request, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $project = $repo->findOneBy(['id'=> $id]);
        $idProject = $project->getId();
        $task = new Tasks;
        $task->setIdTask($project);
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($task);

            $this->entityManager->flush();


            return $this->redirectToRoute('tasks_index',  ["id" => $idProject]);
        }

        return $this->render('tasks/new.html.twig', [
            'user' => $user,
            'task' => $task,
            'form' => $form->createView(),
            'projectName' => $project->getName(),
            'projects' => $projects,
            'project' => $project
        ]);
    }
    /**
     * @Route("/show/{id}", name="tasks_show", methods={"GET"})
     */
    public function show(TasksRepository $taskRepo, ProjectRepository $repo, int $id): Response
    {
       /** @var User $user */ 
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);   
      
        $task = $taskRepo->findOneBy(array('id'=> $id));

        return $this->render('tasks/show.html.twig', [
            'task' => $task,
            'user' =>$user,
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tasks_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request, TasksRepository $taskRepo, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $project = new Project;
        $task = $taskRepo->findOneBy(array('id'=> $id));
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $idProject = $task->getIdTask()->getId();
            return $this->redirectToRoute('tasks_index', ["id" => $idProject]);
        }

        return $this->render('tasks/edit.html.twig', [
            'task' => $task,
            'project' => $project,
            'user' =>$user,
            'projects'=>$projects,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/delete", name="tasks_delete", methods={"DELETE"})
     */
    public function delete(int $id, Request $request, TasksRepository $taskRepo): Response
    {
        $task = $taskRepo->findOneBy(array('id'=> $id));
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
}

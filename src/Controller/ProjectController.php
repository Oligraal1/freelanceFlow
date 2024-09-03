<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Project;
use App\Form\ClosureType;
use App\Form\ProjectType;
use App\Form\SearchBarreType;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/", name="project_index", methods={"GET", "POST"})
     */
    public function index(ProjectRepository $repo, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllOrderByName($idUser);
        $project = new Project();
        $form = $this->createForm(SearchBarreType::class, $project);
        $form->handleRequest($request, $repo);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($project->getName() != null) {
                foreach ($projects as $proj) {
                    if (strtolower(substr($proj->getName(), 0, 4)) == strtolower(substr($project->getName(), 0, 4))) {
                        $project = $repo->findOneBySomeField($proj->getName());

                        break;
                    }
                }
                if ($project->getId() != null) {
                    $idProject = $project->getId();
                    $projectID = $project->getIdClient();
                    return $this->redirectToRoute('tasks_index', ["id" => $idProject]);
                } else {
                    $this->addFlash('error', 'Il n\'y a pas de projet à ce nom. Merci de saisir un nom de projet valide.');
                }
            }
        }

        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="project_new", methods={"GET","POST"})
     */
    public function new(int $id, Request $request, ClientRepository $clientRepository, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $client = $clientRepository->find($id);
        $projects = $repo->findAllByUser($idUser);
        $project = new Project();
        $project->setIdClient($client);
        $project->setIdUser($user);
        $client = $client->addIdUser($user);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();
            $idProject = $project->getId();
            return $this->redirectToRoute('tasks_new', ["id" => $idProject]);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'user' => $user,
            'form' => $form->createView(),
            'projects' => $projects
        ]);
    }
    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(int $id, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $project= $repo->find($id);
        return $this->render('project/show.html.twig', [
            'user' => $user,
            'project' => $project,
            'projects' => $projects
        ]);
    }
    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request,  ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $project= $repo->find($id);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'user' => $user,
            'project' => $project,
            'form' => $form->createView(),
            'projects' => $projects
        ]);
    }
    /**
     * @Route("/{id}/closure", name="project_closure", methods={"GET","POST"})
     */
    public function closure(int $id, Request $request, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser); 
        $project= $repo->find($id);
        if ($project->getEndDate() == null) {
            $form = $this->createForm(ClosureType::class, $project);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($project);
                $this->entityManager->flush();
           
                return $this->redirectToRoute('project_index');
            }
        } else {
                $this->entityManager->persist($project->setEndDate(null));
                $this->entityManager->flush();
           
               return $this->redirectToRoute('project_index');
            }
        
        return $this->render('project/closure.html.twig', [
            'user' => $user,
            'project' => $project,
            'form' => $form->createView(),
            'projects' => $projects
        ]);
    }
    /**
     * @Route("/{id}/delete", name="project_delete", methods={"DELETE"})
     */
    public function delete(int $id,Request $request, ProjectRepository $projectRepository): Response
    {
       $project = $projectRepository->find($id);
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($project);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}

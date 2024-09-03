<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\Form\SearchBarreType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(ProjectRepository $repo, Request $request): Response
    {
        //Recupère tous les projets d'un utilisateur
        /** @var User $user */
        $user = $this->getUser();
        if($user){
            $idUser = $user->getId();
            $projects = $repo->findAllOrderByEndDate($idUser);
            $project = new Project();
            //crée la barre de recherche
            $form = $this->createForm(SearchBarreType::class, $project);
            $form->handleRequest($request, $repo);

            //gère la barre de recherche
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
                        return $this->redirectToRoute('tasks_index', ["id" => $idProject]);
                    } else {

                        $this->addFlash('error', 'Il n\'y a pas de projet à ce nom. Merci de saisir un nom de projet valide.');
                    }
                }
            }
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'titlePage' => 'Qu\'est ce que Freelance Time Manager ?',
                'projects' => $projects,
                'form' => $form->createView(),
                'user' => $user,
            ]);
        }
        else {
            $user = new User();
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'titlePage' => 'Qu\'est ce que Freelance Time Manager ?',
                'user' => $user,
            ]);
        }
     }
    /**
     * @Route("home/nav", name="nav")
     */
     public function nav(ProjectRepository $repo, Request $request){
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        return $this->render('home/nav.html.twig', [
            'user' => $user,
            'projects' => $projects
        ]);
     }
}

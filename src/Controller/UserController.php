<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   
    /**
     * @Route("/user", name="user")
     */
    public function index(UserRepository $repo, ProjectRepository $projRepo)
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $projRepo->findAllByUser($idUser);
        $user = $repo -> findOneBy(array('id'=>$idUser));
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'projects' => $projects
        ]);
    }
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     *
     * @return void
     */
    public function edit(Request $request, User $user, ProjectRepository $repo){
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'projects' => $projects,
            'form' => $form->createView(),
        ]);
    }
}

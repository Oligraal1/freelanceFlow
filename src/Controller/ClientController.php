<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/", name="client_index", methods={"GET"})
     */
    public function index(ClientRepository $repoClient, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $clients = $repoClient->findAllOrderByName($idUser);
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'user' => $user,
            'projets' => $repo->findAllByIdClient($idUser),
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/ajax", name="client_ajax", methods={"POST"})
     *
     * @param ClientRepository $repoClient
     * @param Client $client
     * @param ProjectRepository $repo
     * @return Response
     */
    public function ajax(Request $request, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $clientID =  $request->request->get('id');
        $projects = $repo->findByIdClient($clientID, $idUser);
        $projectsForOneClient = [];
            foreach ($projects as $project) {
          $val = ['name' =>  $project->getName(),
                    'id' =>  $project->getId(),
          ];
           $projectsForOneClient[] = array_push($projectsForOneClient, $val);   
        }
     //   Supprime des numéros qui s'insèrent dans le tableau, cela règle le problème mais il faudrait savoir pourquoi.
        $tabNumber = [0, "0", 1, "1", 2, "2", 3, "3", 4, "4", 5, "5", 6, "6", 7, "7", 8, "8", 9, "9"];
        foreach ($tabNumber as $tab) {
            foreach ($projectsForOneClient as $p) {
                if($tab === $p) {
                    unset($projectsForOneClient[$p]);
                }
            }
        }
        $response = new JsonResponse();
        $response->setData(['data' => $projectsForOneClient]);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/new", name="client_new", methods={"GET","POST"})
     */
    public function new(Request $request, ClientRepository $clientRepo, ProjectRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllOrderByDate($idUser);
        $clients = $clientRepo->findAllByIdUser($idUser);
       
        $client = new Client();
        $client = $client->addIdUser($user);
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($client);
            $this->entityManager->flush();
            $idClient = $client->getId();
            
            return $this->redirectToRoute('project_new', ['id' => $idClient]);
        }

        return $this->render('client/new.html.twig', [
            'user' => $user,
            'client' => $client,
            'clients' => $clients,
            'projects' => $projects,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/find_client", name="client_find_client", methods={"GET","POST"})
     */
    public function findClient(Request $request, ClientRepository $clientRepo): Response
    {
       /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $chosenClient = $request->request->get("client");

        if ($chosenClient != null) {

            $existingClient = $clientRepo->findOneByName($chosenClient);
            $idClient = $existingClient->getId();
            return $this->redirectToRoute('project_new', ['id' => $idClient]);
        } else {

            $this->addFlash('error', 'Merci de créer un nouveau client ou de sélectionner un client existant.');
        }
    }
    /**
     * @Route("/{id}", name="client_show", methods={"GET"})
     */
    public function show(int $id, ClientRepository $clientRepo, ProjectRepository $repo): Response
    {
         /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $client = $clientRepo->find($id);
        $clientID = $client->getId();
        $projects = $repo->findByIdClient($clientID, $idUser);
      
        return $this->render('client/show.html.twig', [
            'user' => $user,
            'client' => $client,
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods={"GET","POST"})
     */
    public function edit(int $id,Request $request, ClientRepository $clientRepo, ProjectRepository $repo): Response
    {
         /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $projects = $repo->findAllByUser($idUser);
        $client = $clientRepo->find($id);
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/edit.html.twig', [
            'user' => $user,
            'client' => $client,
            'form' => $form->createView(),
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/{id}", name="client_delete", methods={"DELETE"})
     */
    public function delete(int $id, Request $request, ClientRepository $clientRepo): Response
    {
        $client = $clientRepo->find($id);
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($client);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('client_index');
    }
}

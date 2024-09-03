<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Tasks;
use App\Entity\Client;
use App\Entity\User;
use App\Entity\Project;
use App\Form\TasksType;
use App\Repository\ProjectRepository;
use Spipu\Html2Pdf\Html2Pdf;
use App\Repository\TasksRepository;
use ProxyManager\Factory\RemoteObject\Adapter\JsonRpc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 *  @Route("/facture")
 */

class FactureController extends AbstractController
{
    /**
     * @Route("/{id}", name="facture")
     */
    public function index(int $id, Request $request, TasksRepository $repo, ProjectRepository $ProjRepo)
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $project = $ProjRepo->find($id);
        $projects = $ProjRepo->findAllByUser($idUser);
        $tasks = new Tasks();
        $today = new \DateTime();
        $today = $today->format("d-m-Y");
        $fromStart = $project->getStartDate()->format('d-m-Y');
        $from = $request->request->get("from", $fromStart);
        $to = $request->request->get("to", $today);
        $payDate = $this->calculDatePaiement($today);
        $hour = $repo->countTotalHourBetweenTwoDates($project, $from, $to);
        $hours = $repo->countTotalHour($project);


        return $this->render('facture/index.html.twig', [
            'controller_name' => 'FactureController',
            'user' => $user,
            'project' => $project,
            'projects' => $projects,
            'tasks' => $tasks,
            'hour' => implode(',', $hour),
            'today' => $today,
            'to' => $to,
            'from' => $from,
            'fromStart' => $fromStart,
            'hours' => implode(',', $hours),
            'payDate' => $payDate
        ]);
    }
    /**
     * Simule la facture en ajax
     * @Route("/ajax/{id}", name="facture_ajax")
     * 
     * @return void
     */
    public function ajax(int $id, Request $request, ProjectRepository $projectRepository, TasksRepository $repo): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $today = new \DateTime();
        $today = $today->format('Y-m-d');
        $project = $projectRepository->find($id);
        $fromStart = $project->getStartDate()->format('Y-m-d');

        $from = $request->request->get('from');

        $to = $request->request->get('to');
        if (empty($from) && !empty($to)) {
            $hour = $repo->countTotalHourBetweenTwoDates($project, $fromStart, $to);
        } else if (empty($to) && !empty($from)) {
            $hour = $repo->countTotalHourBetweenTwoDates($project, $from, $today);
        } else if (empty($from) && empty($to)) {
            $hour = $repo->countTotalHourBetweenTwoDates($project, $fromStart, $today);
        } else {
            $hour = $repo->countTotalHourBetweenTwoDates($project, $from, $to);
        }

        // dump($hour);die;
        $hours = $repo->countTotalHour($project);

        $value = [
            'projectId' => $project->getId(),
            'projectName' => $project->getName(),
            'projectPrice' => $project->getPrice(),
            'fromStart' => $fromStart,
            'today' => $today,
            'to' => $to,
            'from' => $from,
            'hour' => implode(',', $hour),
            'hours' => implode(',', $hours),
            'user' => $user,
        ];

        $response = new JsonResponse();
        $response->setData(['data' => $value]);
        //    $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /*---------------------------------------------------------------*/
    /*
    Titre : Détermine rapidement si un jour est férié (fetes mobiles incluses)                                         
                                                                                                                          
    URL   : https://phpsources.net/code_s.php?id=382
    Auteur           : Olravet                                                                                            
    Website auteur   : http://olravet.fr/                                                                                 
    Date édition     : 05 Mai 2008                                                                                        
    Date mise à jour : 17 Aout 2019                                                                                      
    Rapport de la maj:                                                                                                    
    - fonctionnement du code vérifié                                                                                    
*/
    /*---------------------------------------------------------------*/

    public function jour_ferie($timestamp)
    {

        $jour = date("d", $timestamp);
        $mois = date("m", $timestamp);
        $annee = date("Y", $timestamp);

        $EstFerie = 0;
        // dates fériées fixes
        if ($jour == 1 && $mois == 1) $EstFerie = 1; // 1er janvier
        // if ($jour == 13 && $mois == 1) $EstFerie = 1; // test
        if ($jour == 1 && $mois == 5) $EstFerie = 1; // 1er mai
        if ($jour == 8 && $mois == 5) $EstFerie = 1; // 8 mai
        if ($jour == 14 && $mois == 7) $EstFerie = 1; // 14 juillet
        if ($jour == 15 && $mois == 8) $EstFerie = 1; // 15 aout
        if ($jour == 1 && $mois == 11) $EstFerie = 1; // 1 novembre
        if ($jour == 11 && $mois == 11) $EstFerie = 1; // 11 novembre
        if ($jour == 25 && $mois == 12) $EstFerie = 1; // 25 décembre
        // fetes religieuses mobiles
        $pak = easter_date($annee);
        $jp = date("d", $pak);
        $mp = date("m", $pak);

        if ($jp == $jour && $mp == $mois) {
            $EstFerie = 1;
        } // Pâques
        $lpk = mktime(
            date("H", $pak),
            date("i", $pak),
            date("s", $pak),
            date("m", $pak),
            date("d", $pak) + 1,
            date("Y", $pak)
        );
        $jp = date("d", $lpk);
        $mp = date("m", $lpk);

        if ($jp == $jour && $mp == $mois) {
            $EstFerie = 1;
        } // Lundi de Pâques
        $asc = mktime(
            date("H", $pak),
            date("i", $pak),
            date("s", $pak),
            date("m", $pak),
            date("d", $pak) + 39,
            date("Y", $pak)
        );
        $jp = date("d", $asc);
        $mp = date("m", $asc);

        if ($jp == $jour && $mp == $mois) {
            $EstFerie = 1;
        } //ascension
        $pe = mktime(
            date("H", $pak),
            date("i", $pak),
            date("s", $pak),
            date("m", $pak),
            date("d", $pak) + 49,
            date("Y", $pak)
        );
        $jp = date("d", $pe);
        $mp = date("m", $pe);

        if ($jp == $jour && $mp == $mois) {
            $EstFerie = 1;
        } // Pentecôte
        $lp = mktime(
            date("H", $asc),
            date("i", $pak),
            date("s", $pak),
            date("m", $pak),
            date("d", $pak) + 50,
            date("Y", $pak)
        );
        $jp = date("d", $lp);
        $mp = date("m", $lp);

        if ($jp == $jour && $mp == $mois) {
            $EstFerie = 1;
        } // lundi Pentecôte

        // Samedis et dimanches
        $jour_sem = jddayofweek(unixtojd($timestamp), 0);
        if ($jour_sem == 0 || $jour_sem == 6) $EstFerie = 1;
        // ces deux lignes au dessus sont à retirer si vous ne désirez pas faire
        // apparaitre les
        // samedis et dimanches comme fériés.
        return $EstFerie;
    }
    /**
     * Calcule la date de paiement à 28 jours en prenant en compte les jours fériés
     *
     * @param [type] $today
     * @return void
     */
    public function calculDatePaiement($today)
    {
        $today = new \DateTime();
        $today = $today->format("d-m-Y");
        $payDay = intval(substr($today, 0, 2)) + 28;
        $month = intval(substr($today, 3, 2));
        $year = intval(substr($today, 6, 4));
        $months_30 = [4, 6, 9, 11];
        $months_31 = [1, 3, 5, 7, 8, 10, 12];
        $payYear = $year;
        //CALCULE LA DATE DE PAIEMENT EN FONCTION DU MOIS
        if (in_array($month, $months_30)) {
            if ($payDay > 30) {
                $payDay = ($payDay - 30);
                $payMonth = $month + 1;
            } else {
                $payMonth = $month;
            }
        } else if (in_array($month, $months_31)) {
            if ($payDay > 31) {
                $payDay = ($payDay - 31);
                $payMonth = $month + 1;
                if ($payMonth > 12) {
                    $payMonth = 1;
                    $payYear = $year + 1;
                }
            } else {
                $payMonth = $month;
            }
        } else {
            if ($month > 28) {
                $payDay = ($payDay - 28);
                $payMonth = $month + 1;
            } else {
                $payMonth = $month;
            }
        }
        if ($payDay < 10) {
            $payDay = "0" . $payDay;
        }
        if ($payMonth < 10) {
            $payMonth = "0" . $payMonth;
        }

        $payDate = $payDay . "-" . $payMonth . "-" . $payYear;

        //GESTION DES JOURS FERIES
        $payDateFerie = new \DateTime($payDate);

        //Récupération du timestamp pour vérifier s'il s'agit d'un jour férié
        $payDateTimestamp = $payDateFerie->getTimestamp();
        $jourFerie =  $this->jour_ferie($payDateTimestamp);

        if ($jourFerie) {
            $payDay++;
            $payDate = $payDay . "-" . $payMonth . "-" . $payYear;
        }

        return $payDate;
    }

    /**
     * @Route("/pdf/{id}", name="facture_pdf")
     */
    public function pdf(int $id, Request $request, TasksRepository $repo, ProjectRepository $projRepo)
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $project = $projRepo->find($id);
        $projects = $projRepo->findAllByUser($idUser);
        $tasks = new Tasks;
        // Date actuelle formatée
        $today = new \DateTime();
        $todayFormatted = $today->format("d-m-Y");

        // Formater la date de début du projet
        $startDateFormatted = $project->getStartDate()->format("d-m-Y");

        // Récupération des dates du formulaire ou des valeurs par défaut
        $from = $request->request->get("from", $startDateFormatted);
        $to = $request->request->get("to", $todayFormatted);
        $num_facture = $request->request->get("num_facture");
        $prestation = $request->request->get("prestation");
        $payDate = $this->calculDatePaiement($today);
        $hour = $repo->countTotalHourBetweenTwoDates($project, $from, $to);
        $hours = $repo->countTotalHour($project);
        if ($hour != null) {
            $message = "Vous n'avez pas travaillé durant cette période, le total correspond au total des heures travaillées depuis le début du projet.";
        }

        //GENERATION PDF
        $html = $this->renderView('facture/pdf.html.twig', [
            'project' => $project,
            'tasks' => $tasks,
            'hour' => implode(',', $hour),
            'today' => $today,
            'to' => $to,
            'from' => $from,
            'hours' => implode(',', $hours),
            'payDate' => $payDate,
            'num_facture' => $num_facture,
            'prestation' => $prestation,
            'message' => $message,
            'user' => $user,
            'projects' => $projects
        ]);

        $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'fr');
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($html);
        $html2pdf->output($project->getName() . '_' . $num_facture . '.pdf');
    }
    /**
     * @Route("/edit/{id}", name="facture_edit")
     */
    public function edit(int $id, Request $request, TasksRepository $repo, ProjectRepository $projRepo)
    {
        /** @var User $user */
        $user = $this->getUser();
        $idUser = $user->getId();
        $project = $projRepo->find($id);
        $projects = $projRepo->findAllByUser($idUser);
        $tasks = new Tasks();

        // Date actuelle formatée
        $today = new \DateTime();
        $todayFormatted = $today->format("d-m-Y");

        // Formater la date de début du projet
        $startDateFormatted = $project->getStartDate()->format("d-m-Y");

        // Récupération des dates du formulaire ou des valeurs par défaut
        $from = $request->request->get("from", $startDateFormatted);
        $to = $request->request->get("to", $todayFormatted);

        // Calcul des heures
        $hour = $repo->countTotalHourBetweenTwoDates($project, $from, $to);
        $hours = $repo->countTotalHour($project);

        return $this->render('facture/edit.html.twig', [
            'controller_name' => 'FactureController',
            'user' => $user,
            'project' => $project,
            'tasks' => $tasks,
            'hour' => implode(',', $hour),
            'today' => $todayFormatted,
            'to' => $to,
            'from' => $from,
            'hours' => implode(',', $hours),
            'projects' => $projects
        ]);
    }
}

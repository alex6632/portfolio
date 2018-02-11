<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Projet;
use App\Form\ContactType;
use Swift_Mailer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PortfolioController extends Controller
{
    /**
     * @Route("/", name="accueil")
     */
    public function indexAction() {
        return $this->render('Portfolio/index.html.twig');
    }

    /**
     * @Route("/contact/submit", name="contact_form")
     */
    public function contactFormAction(Request $request, Swift_Mailer $mailer) {

        //$contactForm = $this->createForm(ContactType::class);
        $contactForm = $this->createForm(ContactType::class, null, array(
            'action' => $this->generateUrl('contact_form'),
            'method' => 'POST',
        ));

        $contactForm->handleRequest($request);
        $session = $this->get('session');

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {

            $data = $contactForm->getData();

            $message = (new \Swift_Message('Message portfolio'))
                ->setFrom($data['email'])
                ->setTo('a.simonin69@gmail.com')
                ->setBody(
                    $this->renderView(
                        'Portfolio/partials/registration.html.twig',
                        array(
                            'nom' => $data['lastName'],
                            'prenom' => $data['firstName'],
                            'email' => $data['email'],
                            'message' => $data['message'],
                        )
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $session->getFlashBag()->add(
                'success',
                'Merci ! Le message a bien été envoyé !'
            );

            return $this->redirectToRoute('accueil');

        }

        return $this->render('Portfolio/partials/contactForm.html.twig', array(
            'ContactForm'  => $contactForm->createView()
        ));
    }

    /**
     * @Route("/formation", name="formation")
     */
    public function FormationAction() {
        $formationList = $this->getDoctrine()
            ->getRepository(Formation::class)
            ->findAll();

        return $this->render('Portfolio/formation.html.twig', array(
            'FormationList' => $formationList
        ));
    }

    /**
     * @Route("/projets", name="projets")
     */
    public function ProjectsAction() {
        $projectList = $this->getDoctrine()
            ->getRepository(Projet::class)
            ->findAll();

        return $this->render('Portfolio/projets.html.twig', array(
            'ProjectList' => $projectList
        ));
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function ContactAction() {
        return $this->render('Portfolio/contact.html.twig');
    }
}
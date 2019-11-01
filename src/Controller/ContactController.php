<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use App\Entity\Departement;
use App\Form\ContactType;
use App\Repository\DepartementRepository;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends AbstractController
{
  /**
   * @Route("/departements", name="departements_list", methods={"GET"})
   */
  public function getDepartements(DepartementRepository $repo)
  {
    $deps = $repo->findAll();

    if (empty($deps)) {
      return new JsonResponse(['message' => 'Aucun département entreprise trouvé'], Response::HTTP_NOT_FOUND);
    }

    $departements = [];
    foreach ($deps as $dep) {
          $departements[] = [
             'id' => $dep->getId(),
             'nom' => $dep->getNom(),
             'mail' => $dep->getMail(),
          ];
      }

      return new JsonResponse($departements);
  }

  /**
   * @Route("/contacts", name="contact_list", methods={"GET"})
   */
  public function getContacts(ContactRepository $repo)
  {
    $contactsList = $repo->findAll();

    if (empty($contactsList)) {
      return new JsonResponse(['message' => 'Aucun département entreprise trouvé'], Response::HTTP_NOT_FOUND);
    }

    $contacts = [];
    foreach ($contactsList as $contact) {
          $contacts[] = [
             'id' => $contact->getId(),
             'nom' => $contact->getNom(),
             'prenom' => $contact->getPrenom(),
             'mail' => $contact->getMail(),
             'message' => $contact->getMessage(),
             'departements' => $contact->getDepartements()->getNom(),
          ];
      }

      return new JsonResponse($contacts);
  }


  /**
   * @Route("/contact", name="contact_new", methods={"POST"})
   */
  public function newContact(Request $request, \Swift_Mailer $mailer): Response
  {

      $body = $request->getContent();
      $content = json_decode($body,true);

      $contact = new Contact();
      $form = $this->createForm(ContactType::class, $contact);
      $form->submit($content);

      if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($contact);
        $em->flush();

        $this->sendMail($mailer, $contact->getNom(), $contact->getPrenom(), $contact->getMail(),
                        $form->get('departements')->getData()->getMail(), $contact->getMessage());

        return $this->redirectToRoute('contact_list');
      }else{
        return new JsonResponse(['message' => 'Le formulaire n\' est pas valide' ], Response::HTTP_NOT_FOUND);
      }

    }

    private function sendMail( \Swift_Mailer $mailer, string $nom, string $prenom, string $mailFrom, string $mailTo, string $contenu){
      $message = (new \Swift_Message($prenom." ".$nom))
        ->setFrom($mailFrom)
        ->setTo($mailTo)
        ->setBody($contenu) ;
      $mailer->send($message);
    }
}

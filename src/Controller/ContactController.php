<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{

    private $flashy;
    private $mailer;

    public function __construct(MailerInterface  $mailer, FlashyNotifier $flashy)
    {
        $this->flashy = $flashy;
        $this->mailer = $mailer;
    }

    #[Route('/contact', name: 'contact')]
    public function index(Request $request): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $infos = $contactForm->getData();

            $email = new TemplatedEmail();

            $email->to("jean.samedy.dev@gmail.com")
                ->from(new Address('stadiumplatform@gmail.com', 'Stadium Bot'))
                ->subject('Formulaire de contact')
                ->htmlTemplate('contact/contact_email.html.twig')
                ->context([
                    'mail' => $contactForm->get('email')->getData(),
                    'role' => $contactForm->get('role')->getData(),
                    'motif' => $contactForm->get('reason')->getData(),
                    'message' => $contactForm->get('description')->getData(),
                ]);

            $this->mailer->send($email);

            $this->flashy->success('Message envoyé avec succès!');

            return $this->redirectToRoute('home');
        }

        $formContact = $contactForm->createView();

        return $this->render('contact/contact.html.twig', compact('formContact'));
    }
}

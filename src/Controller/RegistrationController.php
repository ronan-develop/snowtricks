<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\User;
use DateTimeImmutable;
use App\Services\MailerService;
use App\Form\RegistrationFormType;
use App\Services\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        MailerService $mailer,
        JWTService $jWTService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
            $user->setSlug(
                strtolower($slugger->slug($user->getUsername()))
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // token
            $header = [
                'alg' => 'HS256',
                'type' => 'JWT'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jWTService->generate($header, $payload, $this->getParameter('secret'));
            dd($token);

            // do anything else you need here, like send an email

            $this->addFlash('success', 'Vous devez confirmer votre compte, relevez vos emails (😉 dans le doute, vérifiez vos SPAMS)');


            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $mailer->sendEmail(
                to: $user->getEmail(),
                subject: "Activation de votre compte Snowtricks",
                body: 'Veuillez cliquer<a href='. $signatureComponents->getSignedUrl() .'> ici </a> pour activer votre compte. Expiration dans '.$signatureComponents->getExpiresAt()
                ->format('d/m/y-H:i:s')
            );

            return $this->render('emails/signup.html.twig', [
                'user' => $user
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name: 'app_verify_email')]
    public function verifyUserEmail(): Response
    {
        return new Response();
    }
}

<?php

namespace App\Controller;

use App\Services\MailerService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Session $session): Response
    {
        if ($this->getUser()) {
            $session->getFlashBag()->add(
                'warning',
                'Vous êtes déjà connecté sous le pseudo ' . $this->getUser()->getUserIdentifier()
            );

            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        if ($error) {
            $session->getFlashBag()->add(
                'warning',
                $error->getMessage()
            );
            foreach ($session->getFlashBag()->get('warning', []) as $message) {
                echo '<div class="flash-warning">' . $message . '</div>';
            }
        }


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/ask-new-password', name: 'app_ask_new')]
    public function askNew(
        Request                 $request,
        UserRepository          $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface  $em,
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy(['email' => $form->getData()['email']]);

            if (!$user) {
                $this->addFlash('warning', 'Adresse Email inconnue');

                return $this->redirectToRoute('app_ask_new');
            }

            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $mailer = (new MailerService())
                ->sendEmail(
                    $user->getEmail(),
                    'Snowtricks : demande de mot de passe',
                    'Bonjour,<br>Votre demande de r&#xE9;initialisation de mot de passe a &#xE9;t&#xE9; effectu&#xE9;e.
                    <br> Veuillez cliquer sur le lien suivant : <a href="' . $url . '"> lien de r&#xE9;initialisation</a>'
                );

            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('success', 'E-mail de réinitialisation du mot de passe envoyé ! 📧');

        return $this->render('security/forgotten_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/reset_password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        Request                $request,
        string                 $token,
        UserRepository         $userRepository,
        EntityManagerInterface $em
    )
    {
        $token = $request->get('token');

        $user = $userRepository->findOneBy(['reset_token' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Token non reconnu');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);
            // Configure different password hashers via the factory
            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'bcrypt'],
                'memory-hard' => ['algorithm' => 'sodium'],
            ]);

            $passwordHasher = $factory->getPasswordHasher('common');

            $hash = $passwordHasher->hash($request->get('password')); // returns a bcrypt hash


            $user->setPassword($hash);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Mot de passe réinitialisé ! 🔑');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/renew-password.html.twig',
            [
                'token' => $token
            ]
        );
    }
}

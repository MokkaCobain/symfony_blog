<?php

namespace App\Controller\Visitor\Registration;


use App\Entity\User;
use App\Service\SendEmailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'visitor.registration.register', methods: ['GET', 'POST'])]
    public function register(
                            Request $request, 
                            UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, 
                            UserRepository $userRepository, 
                            TokenGeneratorInterface $tokenGenerator, 
                            SendEmailService $sendEmailService
                            ): Response
    {
        // Pour sécuriser l'acces à l'url via la barre url
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Générer un token pour l'inscription par email
            $tokenGenerator = $tokenGenerator->generateToken();
            $user->setTokenForEmailVerification($tokenGenerator);

             // Générer la deadline pour la vérification du compte via l'email
            $deadline = (new \DateTimeImmutable('now'))->add(new \DateInterval('P1D'));
            $user->setDeadLineForEmailVerification($deadline);

            // Encoder le password
            $userPasswordHashed = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($userPasswordHashed);

            // Insérer les datas dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Envoyer un email de vérification de compte
            $sendEmailService->send([
                "sender_email"    => 'jean.dupont@gmail.com',
                "sender_name"     => 'Jean Dupont',
                "recipient_email" => $user->getEmail(),
                "subject"         => 'Blog Jean Dupont - Vérification de votre compte',
                "html_template"  => 'email/email.verification.html.twig',
                "context"         => [
                    "user_id"                           => $user->getId(),
                    "token_for_email_verification"      => $user->getTokenForEmailVerification(),
                    "deadline_for_email_verification"   => $user->getDeadlineForEmailVerification()->format('d-m-Y à H:i:s'),
                ]
            ]);

           // Redirection vers la page AVANT vérification du mail
           return $this->redirectToRoute('visitor.registration.waiting.for.email.verification');

        }

        return $this->render('page/visitor/registration/register.html.twig', ["registrationForm" => $form->createView()
        ]);
    }


    #[Route('/register/waiting-for-verify', name: 'visitor.registration.waiting.for.email.verification')]
    public function waitingForEmailVerification()
    {
        return $this->render('page/visitor/registration/waiting.for.email.verification.html.twig');
    }



    #[Route('/register/verify/{id}/{token}', name: 'visitor.registration.email.verificated')]
    public function emailVerification(User $user, $token, UserRepository $userRepository)
    {
        if (! $user) 
        {
            throw new AccessDeniedException();
        }

        if ( $user->getIsVerified() )
        {
            $this->addFlash('succes', 'Votre compte est déjà activé !');
        
            return $this->redirectToRoute('visitor.welcome.index');
        }

        if ( (empty($token)) || ($user->getTokenForEmailVerification() == null)  || ($token !== $user->getTokenForEmailVerification()) )
        {
            throw new AccessDeniedException();
        }

        if ( new \DateTimeImmutable('now') > $user->getDeadlineForEmailVerification() )
        {   
            $deadline = $user->getDeadlineForEmailVerification();
            $userRepository->remove($user, true);

            throw new CustomUserMessageAccountStatusException("Vous avez dépassé le délai de validation (c'était le $deadline). Vous devez vous réinscrire.");
        }

        $user->setIsVerified(true);
        $user->setTokenForEmailVerification('');
        $user->setVerifiedAt(new \DateTimeImmutable('now'));

        $userRepository->add($user, true);

        $this->addFlash('success', 'Votre compte est activé. Vous pouvez vous connecter !');
        return $this->redirectToRoute('visitor.welcome.index');
;    }
}

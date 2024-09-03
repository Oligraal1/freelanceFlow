<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    private $userRepository;
    private $passwordHasher;
    private $urlGenerator;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $request->attributes->get('_route') === self::LOGIN_ROUTE;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('email');
        $password = $request->request->get('password');

        if (!$username || !$password) {
            throw new AuthenticationException('Username and password must not be empty.');
        }

        return new Passport(
            new UserBadge($username, function ($username) {
                $user = $this->userRepository->findOneBy(['email' => $username]);

                if (!$user) {
                    throw new AuthenticationException('User not found.');
                }

                return $user;
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

    public function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

    // public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?RedirectResponse
    // {
    //     $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
    //     if ($targetPath) {
    //         return new RedirectResponse($targetPath);
    //     }

    //     return new RedirectResponse($this->urlGenerator->generate('home'));
    // }

    // public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    // {
    //     return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    // }

    // public function supports(Request $request): ?bool
    // {
    //     return $request->isMethod('POST') && $request->attributes->get('_route') === self::LOGIN_ROUTE;
    // }

    // public function createToken(PassportInterface $passport, string $firewallName): TokenInterface
    // {
    //     return new UsernamePasswordToken(
    //         $passport->getUser(),
    //         $passport->getCredentials(),
    //         $firewallName,
    //         $passport->getUser()->getRoles()
    //     );
    // }

    // private function getTargetPath(SessionInterface $session, string $providerKey): ?string
    // {
    //     return $session->get('_security.'.$providerKey.'.target_path');
    // }


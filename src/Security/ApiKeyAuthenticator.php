<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use App\Security\User;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = $_ENV['API_KEY'] ?? '';
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-API-KEY');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');

        if ($this->apiKey !== $apiKey) {
            throw new CustomUserMessageAuthenticationException('Invalid API Key');
        }

        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API Key provided');
        }

        return new SelfValidatingPassport(
            new UserBadge('api_user', function($userIdentifier) {
                $user = new User();
                $user->setUsername('api_user');
                return $user;
            })
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessageKey()], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Allow the request to continue
    }
}

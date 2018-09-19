<?php

namespace GewisBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\HttpUtils;

class IpAuthenticator extends AbstractGuardAuthenticator
{

    private $em;
    private $httpUtils;
    private $loginPath;

    public function __construct(EntityManager $em, HttpUtils $httpUtils)
    {
        $this->em = $em;
        $this->httpUtils = $httpUtils;
    }

    public function getCredentials(Request $request)
    {
        if (!$token = $request->headers->get('x-real-ip')) {
            // No token?
            $token = null;
        }

        // What you return here will be passed to getUser() as $credentials
        return ['ip_addr' => $token];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $ip = $credentials['ip_addr'];

        if (null === $ip) {
            return;
        }

        return $this->em->getRepository('DOMJudgeBundle:User')->findOneBy(array("ipaddress" => "$ip"));
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // Redirect to login
        return $this->httpUtils->createRedirectResponse($request, '/login');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}

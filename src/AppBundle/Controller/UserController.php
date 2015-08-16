<?php

namespace AppBundle\Controller;

use AppBundle\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="create_user")
     */
    public function indexAction(Request $request)
    {
        /** @var UserService $userService */
        $userService = $this->get('kis.user_service');
        $userService->createUser(
            $request->get('username'),
            $request->get('name'),
            $request->get('passphrase')
        );

        die('done');
    }
}

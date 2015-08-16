<?php

namespace AppBundle\Controller;

use AppBundle\Encryption\InvalidPassPhraseException;
use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Service\DocumentService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/document")
 */
class DocumentController extends Controller
{
    /**
     * @param Request $request
     * @return array
     *
     * @Route("/", name="document_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $message = $request->query->get('message');

        /** @var User $user */
        $user = $this->getUser();

        return [
            'documents' => $user->getDocuments(),
            'message' => $message
        ];
    }

    /**
     * @param Request $request
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/create", name="document_create")
     * @Route("/{id}/update", name="document_update")
     * @Method("POST")
     */
    public function createUpdateAction(Request $request, Document $document = null)
    {
        /** @var UserService $userService */
        $userService = $this->get('kis.user_service');

        $title = $request->request->get('title');
        $plainContent = $request->request->get('plain_content');
        $password = $request->request->get('password');
        $shareWith = $userService->getUsersByEmails($request->request->get('share_with', []));
        $unshareFrom = $userService->getUsersByEmails($request->request->get('unshare_from', []));

        /** @var User $user */
        $user = $this->getUser();

        /** @var DocumentService $documentService */
        $documentService = $this->get('kis.document_service');

        try
        {
            if (is_null($document))
            {
                $documentService->createDocument($title, $plainContent, $user, $password);
            }
            else
            {
                $documentService->updateDocument($document, $title, $plainContent, $user, $password);
            }
        }
        catch (InvalidPassPhraseException $e)
        {
            return $this->redirect($this->generateUrl('document_list', ['message' => 'Invalid password provided!']));
        }



        foreach ($shareWith as $sharedUser)
        {
            $documentService->shareDocument($document, $user, $sharedUser, $password);
        }

        foreach ($unshareFrom as $unsharedUser)
        {
            $documentService->unshareDocument($document, $unsharedUser);
        }


        return $this->redirect($this->generateUrl('document_list'));
    }

    /**
     * @param Request $request
     * @param Document $document
     *
     * @return array
     *
     * @Route("/{id}/edit", name="document_get")
     * @ParamConverter("document")
     * @Method("POST")
     * @Template()
     */
    public function editAction(Request $request, Document $document)
    {
        $password = $request->request->get('password');

        /** @var User $user */
        $user = $this->getUser();

        /** @var DocumentService $documentService */
        $documentService = $this->get('kis.document_service');

        /** @var UserService $userService */
        $userService = $this->get('kis.user_service');

        try
        {
            $plainContent = $documentService->getContent($document, $user, $password);
        }
        catch (InvalidPassPhraseException $e)
        {
            return $this->redirect($this->generateUrl('document_list', ['message' => 'Invalid password provided!']));
        }


        return [
            'id' => $document->getId(),
            'title' => $document->getTitle(),
            'plainContent' => $plainContent,
            'unsharedUsers' => $userService->getUnsharedUsers($document),
            'sharedUsers' => $userService->getSharedUsers($document)
        ];
    }

    /**
     * @Route("/new", name="document_new")
     * @Method("GET")
     * @Template("AppBundle:Document:edit.html.twig")
     */
    public function newAction()
    {
        return [
            'id' => null,
            'title' => '',
            'plainContent' => '',
            'unsharedUsers' => [],
            'sharedUsers' => []
        ];
    }
}

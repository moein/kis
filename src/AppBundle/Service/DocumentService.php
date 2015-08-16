<?php

namespace AppBundle\Service;

use AppBundle\Encryption\KeyGen;
use AppBundle\Entity\Document;
use AppBundle\Entity\Share;
use AppBundle\Entity\User;
use AppBundle\Repository\DocumentRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DocumentService
{
    protected $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function createDocument($title, $plainContent, User $creator, $passPhrase)
    {
        $key = \Crypto::CreateNewRandomKey();
        $encryptedKey = KeyGen::encrypt($key, $creator->getPublicKey());
        $document = new Document($creator, $encryptedKey);
        $this->documentRepository->persist($document->getShares()[0]);
        $this->updateDocument($document, $title, $plainContent, $creator, $passPhrase);

        return $document;
    }

    public function updateDocument(Document $document, $title, $plainContent, User $updater, $passPhrase)
    {
        $share = $document->getShareOf($updater);
        if ($share === null)
        {
            throw new AccessDeniedException('The user does not have access to this document');
        }

        $encryptionKey = $this->getEncryptionKey($share, $passPhrase);

        $encryptedContent = base64_encode(\Crypto::Encrypt($plainContent, $encryptionKey));

        $document->setTitle($title);
        $document->setEncryptedContent($encryptedContent);

        $this->documentRepository->save($document);
    }

    public function shareDocument(Document $document, User $requester, User $shareWith, $passPhrase)
    {
        $requesterShare = $document->getShareOf($requester);
        if ($requesterShare === null)
        {
            throw new AccessDeniedException('The user does not have access to this document');
        }

        $encryptionKey = $this->getEncryptionKey($requesterShare, $passPhrase);
        $encryptedKey = KeyGen::encrypt($encryptionKey, $shareWith->getPublicKey());

        $share = $document->shareWith($shareWith, $encryptedKey);

        $this->documentRepository->save($share);
    }

    public function unshareDocument(Document $document, User $unshareFrom)
    {
        $share = $document->getShareOf($unshareFrom);

        $this->documentRepository->delete($share);

        $document->removeShareOf($unshareFrom);
    }

    public function getContentById($documentId, User $requester, $passPhrase)
    {
        /** @var Document $document */
        $document = $this->documentRepository->find($documentId);

        return $this->getContent($document, $requester, $passPhrase);
    }

    public function getContent(Document $document, User $requester, $passPhrase)
    {
        $share = $document->getShareOf($requester);
        $encryptionKey = $this->getEncryptionKey($share, $passPhrase);

        return \Crypto::Decrypt(base64_decode($document->getEncryptedContent()), $encryptionKey);
    }

    protected function getEncryptionKey(Share $share, $passPhrase)
    {
        return KeyGen::decrypt($share->getEncryptedKey(), $share->getUser()->getPrivateKey(), $passPhrase);
    }
}
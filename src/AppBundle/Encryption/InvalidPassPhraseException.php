<?php

namespace AppBundle\Encryption;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InvalidPassPhraseException extends AccessDeniedException
{

}
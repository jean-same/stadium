<?php

namespace App\EventsListener\Doctrine;

use App\Entity\Account;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordEncoderListener
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist( Account $account)
    {
       /* $hash = $this->passwordHasher->hashPassword($account, $account->getPassword() );
        
        $account->setPassword($hash); */
    }

    public function preUpdate(Account $account)
    {
        $hash = $this->passwordHasher->hashPassword($account, $account->getPassword());
        $account->setPassword($hash);
    }
}

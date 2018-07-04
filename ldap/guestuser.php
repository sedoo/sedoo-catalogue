<?php

require_once "ldap/constants.php";
require_once "ldap/entry.php";

class guestuser
{

    public $cn;
    public $sn;
    public $mail;

    public function __construct($mail)
    {
        $this->mail = $mail;
        $this->cn = "guest";
        $this->sn = "guest";
    }

    public function testGroups($groups)
    {
        return false;
    }

  /*
   * Teste si l'utilisateur est membre d'un des groupes du tableau $groups.
   * @return false
   */
    public function isMemberOf($groups)
    {
        return false;
    }

    public function isRoot()
    {
        return false;
    }

    public function isAdmin()
    {
        return false;
    }

    public function isProjectAdmin()
    {
        return false;
    }
}

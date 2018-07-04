<?php

require_once "ldap/constants.php";
require_once "ldap/entry.php";

class user extends entry
{

    public $cn;
    public $sn;

    public function __construct($dn = null, $attrs = null)
    {
        if (isset($dn)) {
            parent::__construct($dn);
        }

        if (isset($attrs)) {
            $this->initUser($attrs);
        }
    }

    public function initUser($attrs)
    {
        $this->cn = $attrs["cn"][0];

        if ($attrs["sn"]) {
            $this->lastname = $attrs["sn"][0];
        }
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

  /*public function isAdmin(){
  return false;
  }*/

    public function isProjectAdmin()
    {
        return false;
    }
}

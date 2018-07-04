<?php

require_once "ldap/entry.php";

class projet extends entry
{

    public $cn;
    public $description;

    public function __construct($dn = null, $attrs = null)
    {
        if (isset($dn)) {
            parent::__construct($dn);
        }

        if (isset($attrs)) {
            $this->cn = $attrs["cn"][0];
            if ($attrs["description"]) {
                $this->description = $attrs["description"][0];
            }
        }
    }
}

<?php

class entry
{

    public $dn;

    function __construct($dn)
    {
        $this->dn = $dn;
    }

    function toString()
    {
        return "DN: $this->dn\n";
    }
}

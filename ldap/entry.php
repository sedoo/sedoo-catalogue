<?php

class entry
{

    public $dn;

    public function __construct($dn)
    {
        $this->dn = $dn;
    }

    public function toString()
    {
        return "DN: $this->dn\n";
    }
}

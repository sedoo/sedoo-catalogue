<?php

class contact
{

    public $nom;
    public $mail;
    public $organisme;
    public $type;

    public function contact($xmlElt)
    {
        $this->nom = (string)$xmlElt->contact_name;
        $this->mail = (string)$xmlElt->contact_mail;
        $this->organisme = (string)$xmlElt->contact_organism;
        $this->type = (string)$xmlElt->contact_type;
    }
    
    public function isPI()
    {
        return $this->type == 'PI or Lead scientist';
    }
    
    public function toString()
    {
        return "&nbsp;&nbsp;-&nbsp;$this->type: $this->nom ($this->organisme), $this->mail";
    }
}

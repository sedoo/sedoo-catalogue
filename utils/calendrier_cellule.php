<?php

class calendrier_cellule
{

    const BLANC = "#ffffff";
    const VERT = "#32CD32";
    const ROUGE = "#ff9090";

    public $jour;
    public $color;

    public function calendrier_cellule($jour = null, $color = self::BLANC)
    {
        $this->color = $color;
        $this->jour = $jour;
    }

    public function setAvailable()
    {
        $this->color = self::VERT;
    }

    public function setMissing()
    {
        $this->color = self::ROUGE;
    }
}

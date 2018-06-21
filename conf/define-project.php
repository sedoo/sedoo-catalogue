<?php
/**
 * on récupère le nom du projet à partir de l'url
 */
$project_name = strtolower(explode('.', $_SERVER['SERVER_NAME'])[0]);
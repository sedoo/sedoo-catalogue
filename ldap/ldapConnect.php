<?php
require_once "conf/conf.php";
require_once "ldap/portalUser.php";
require_once "ldap/projectUser.php";
require_once "ldap/user.php";
require_once "ldap/projet.php";
require_once "ldap/groupe.php";
require_once "ldap/groupeFtp.php";
require_once "ldap/constants.php";

class ldapConnect
{

    public $ldaphost = LDAP_HOST;
    public $ldapport = LDAP_PORT;
    public $ldapdn = LDAP_DN;
    public $ldappasswd = LDAP_PASSWD;
    public $ldapconn;

  /*
   * Connexion en utilisant LDAP_DN et LDAP_PASSWD (voir constants.php)
   */
    public function openAdm()
    {
        $this->open($this->ldapdn, $this->ldappasswd);
    }

  /*
   * Ouvre une connexion. @return TRUE en cas de succès et FALSE en cas d'échec. Exception en cas d'erreur (sauf erreur 49 Invalid credentials)
   */
    public function open($dn, $passwd)
    {
      // Connexion LDAP
        $this->ldapconn = ldap_connect($this->ldaphost, $this->ldapport);
        if (!isset($this->ldapconn) || empty($this->ldapconn)) {
            throw new Exception("Impossible de se connecter au serveur LDAP");
        } else {
          // Connexion au serveur LDAP
            $ldapbind = ldap_bind($this->ldapconn, $dn, $passwd);

          // Vérification de l'authentification
            if ($ldapbind) {
                return true;
            } else {
                $errorCode = ldap_errno($this->ldapconn);
                if ($errorCode == 49) {
                  // Invalid credentials
                    return false;
                } else {
                    echo "Error code: $errorCode<br>";
                    $this->logErreur("Echec de la connexion à l'annuaire");
                    throw new Exception("Impossible de se connecter au serveur LDAP");
                }
            }
        }
    }

    public function close()
    {
        if ($this->ldapconn) {
            ldap_close($this->ldapconn);
        }
    }

  /*
   * Ajoute une entrée dans l'annuaire.
   */
    public function addEntry($dn, $attrs)
    {
        if (!isset($this->ldapconn) || empty($this->ldapconn)) {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
        if (ldap_add($this->ldapconn, $dn, $attrs)) {
            return true;
        } else {
            $this->logErreur("Echec lors de l'ajout de l'entrée $dn");
            return false;
        }
    }

    public function exists($dn)
    {
        if (!isset($this->ldapconn) || empty($this->ldapconn)) {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
        $result = ldap_read($this->ldapconn, $dn, "objectClass=*");
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function getEntry($dn, $retClass = null)
    {
        if (!isset($retClass)) {
            $retClass = strtolower(MainProject) . 'User';
        }

        if (!isset($this->ldapconn) || empty($this->ldapconn)) {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
        $result = ldap_read($this->ldapconn, $dn, "objectClass=*");
        if ($result) {
            $entry = ldap_first_entry($this->ldapconn, $result);
            $attrs = ldap_get_attributes($this->ldapconn, $entry);
            $dn = ldap_get_dn($this->ldapconn, $entry);
            $user = new portalUser($dn, $attrs);

            return $user;
        }
        return null;
    }

    public function login($dn, $password, $objClass = REGISTERED_USER_CLASS, $retClass = null)
    {
        if (!isset($retClass) && empty($retClass)) {
            $retClass = strtolower(MainProject) . 'User';
        }

        if ($this->open($dn, $password)) {
            $result = ldap_read($this->ldapconn, $dn, "objectClass=" . $objClass);
            if ($result) {
                $entry = ldap_first_entry($this->ldapconn, $result);
                $attrs = ldap_get_attributes($this->ldapconn, $entry);
                $dn = ldap_get_dn($this->ldapconn, $entry);
                $user = new portalUser($dn, $attrs);
                return $user;
            } else {
                $this->logErreur("Impossible de lire l'entrée $dn");
            }
        }
    }

  /*
   * Authentification via l'annuaire. @return entrée correspondante
   */
    public function loginAdmin($adminMail, $password)
    {
        $dn = $this->getUserDn($adminMail);
        return $this->login($dn, $password);
    }

  /**
   * Recherche dans l'annuaire
   * @param user utilisateur à utiliser pour se connecter à l'annuaire
   * @param base branche de l'annuaire à interroger
   * @param filter filtre ldap
   * @param classname type des objets php à renvoyer
   */
    public function listEntries($base, $filter, $className, $sort = null)
    {
        global $project_name;
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            $result = ldap_search($this->ldapconn, $base, $filter);
            if ($result) {
                if (isset($sort)) {
                    ldap_sort($this->ldapconn, $result, $sort);
                }
                $cpt = 1;
                $entry = ldap_first_entry($this->ldapconn, $result);

                while ($entry) {
                    $attrs = ldap_get_attributes($this->ldapconn, $entry);
                    $dn = ldap_get_dn($this->ldapconn, $entry);
                    if (strstr($className, 'User')) {
                        $class = 'portalUser';
                    } elseif ($className == strtolower($project_name) . 'User') {
                        $class = 'projectUser';
                    } else {
                        $class = $className;
                    }

                    $liste[$cpt++] = new $class($dn, $attrs);
                    $entry = ldap_next_entry($this->ldapconn, $entry);
                }
                if (isset($liste) && !empty($liste)) {
                    return $liste;
                }
            } else {
                $this->logErreur("Echec lors de la recherche dans l'annuaire");
            }
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

  // TODO modifier attribut

  // TODO modifier mot de passe

  /*
   * Ajoute des attributs.
   */
    public function addAttributes($dn, $nvAttrs)
    {
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            if (ldap_mod_add($this->ldapconn, $dn, $nvAttrs)) {
                return true;
            } else {
                $this->logErreur("Echec de la modification de l'entrée " . $dn);
                return false;
            }
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

  /*
   * Remplace la valeur d'un attribut.
   */
    public function modifyAttribute($dn, $attr, $value)
    {
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            $nvAttrs[$attr] = $value;
            if (ldap_mod_replace($this->ldapconn, $dn, $nvAttrs)) {
                return true;
            } else {
                $this->logErreur("Echec de la modification de l'entrée " . $dn);
                return false;
            }
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

    public function modifyAttributes($dn, $attr)
    {
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            unset($attr['objectClass']);
            unset($attr['altMail']);
            while ($current = $attr) {
                echo key($current);
                if (ldap_mod_replace($this->ldapconn, $dn, $current)) {
                    next($attr);
                    return true;
                } else {
                    $this->logErreur("Echec de la modification de l'entrée " . $dn . " -----> attribut : " . key($attr));
                    return false;
                }
            }
            reset($attr);
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

    public function addAttribute($dn, $attr, $value)
    {
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            $nvAttrs[$attr] = $value;
            if (ldap_mod_add($this->ldapconn, $dn, $nvAttrs)) {
                return true;
            } else {
                $this->logErreur("Echec de la modification de l'entrée " . $dn);
                return false;
            }
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

  /*
   * Supprime des attributs.
   */
    public function deleteAttributes($dn, $attrs)
    {
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            if (ldap_mod_del($this->ldapconn, $dn, $attrs)) {
                return true;
            } else {
                $this->logErreur("Echec de la modification de l'entrée " . $dn);
                return false;
            }
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

    public function deleteEntry($dn)
    {
        if (!isset($this->ldapconn) || empty($this->ldapconn)) {
            throw new Exception("ERREUR: Connection à l'annuaire non ouverte");
        }

        if (ldap_delete($this->ldapconn, $dn)) {
            return true;
        } else {
            $this->logErreur("Echec de la suppression de l'entrée $dn");
            return false;
        }
    }

    public function renameEntry($dn, $newrdn)
    {
        if (!isset($this->ldapconn) || empty($this->ldapconn)) {
            throw new Exception("ERREUR: Connection à l'annuaire non ouverte");
        }

        if (ldap_rename($this->ldapconn, $dn, $newrdn, null, true)) {
            return true;
        } else {
            $this->logErreur("Echec du renommage de l'entrée $dn (nv rdn : $newrdn)");
            return false;
        }
    }

  /*
   * Supprime un attribut.
   */
    public function deleteAttribute($dn, $attr, $value)
    {
        if (isset($this->ldapconn) && !empty($this->ldapconn)) {
            $nvAttrs[$attr] = $value;
            if (ldap_mod_del($this->ldapconn, $dn, $nvAttrs)) {
                return true;
            } else {
                $this->logErreur("Echec de la modification de l'entrée " . $dn);
                return false;
            }
        } else {
            throw new Exception("Connexion à l'annuaire non ouverte");
        }
    }

    public function rejectUser($admin, $user, $project)
    {
        if ($this->open($admin->dn, $admin->userPassword)) {
            $nvAttrs[strtolower($project) . "Status"] = STATUS_REJECTED;
            if (ldap_mod_add($this->ldapconn, $user->dn, $nvAttrs)) {
                return true;
            } else {
                $this->logErreur("Echec de la modification de l'entrée " . $user->dn);
                return false;
            }
        } else {
            $this->logErreur("Echec lors de la connexion à l'annuaire");
            return false;
        }
    }

    public function logErreur($msg)
    {
        echo "ERREUR: $msg. Cause: " . ldap_error($this->ldapconn) . '<br>';
    }

    public function getUserDn($mail)
    {
        return 'mail=' . $mail . ',' . PEOPLE_BASE;
    }
}

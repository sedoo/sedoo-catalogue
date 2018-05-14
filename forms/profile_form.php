<?php
require_once "forms/login_form.php";
require_once "countries.php";
require_once "bd/journal.php";
require_once "conf/conf.php";

class profile_form extends login_form {

  function createForm() {
    if (isset($_SESSION['loggedUser'])) {
      $this->user = unserialize($_SESSION['loggedUser']);
    }
    if ($this->isLogged()) {
      $this->initGroups();
    } else {
      $this->createLoginForm('Mail');
    }
	}
	
  function changeMail() {
    $ldapConn = new ldapConnect();
    $newmail = $this->exportValue('new_mail');
    try {
      $ldapConn->openAdm();
      $newrdn = "mail=$newmail";
      if ($ldapConn->renameEntry($this->user->dn, $newrdn)) {
        $this->user->mail = $newmail;
        $this->user->dn = $this->user->getUserDn();
        $_SESSION['loggedUser'] = serialize($this->user);
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->mailAdmin('ERREUR', "Erreur lors du changement de mot de passe.", $e, $user);
      return false;
    }
	}
	
  function changePassword($project) {
    global $project_name;
    $ldapConn = new ldapConnect();
    $newpassword = $this->exportValue('password_1');
    $hashMd5 = md5($newpassword);
    $md5Ldap = ldap_md5($newpassword);
    try {
      $ldapConn->openAdm();
      if ($ldapConn->modifyAttribute($this->user->dn, "userPassword", $md5Ldap)) {
        $this->user->userPassword = $newpassword;
        if ($newpassword) {
          // Envoi d'un mail au responsable du projet
          $infos = "L'utilisateur " . $this->user->mail . " a changé son mot de passe.\n\nmd5: $hashMd5 \nmd5 (ldap): " . $md5Ldap . "\nmdp: $newpassword";
          if ($this->user->isMemberOf(array(
            strtolower($project_name) . 'Participant',
          )) && TEST_MODE === false) {
            sendMailSimple(Portal_Contact_Email, "[$project_name] New Password", $infos, ROOT_EMAIL);
          }
          sendMailSimple(Portal_Contact_Email, "[$project] New Password", $infos, ROOT_EMAIL);
        }
        $_SESSION['loggedUser'] = serialize($this->user);
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->mailAdmin('ERREUR', "Erreur lors du changement de mot de passe.", $e, $user);
      return false;
    }
	}
	
  function createChangeEmailForm() {
    $this->registerRule('not_in_directory', 'function', 'not_in_directory');
    $this->addElement('text', 'new_mail', 'New Mail');
    $this->applyFilter('new_mail', 'trim');
    $this->addRule('new_mail', 'New Mail is required', 'required');
    $this->addRule('new_mail', 'New Mail is incorrect', 'email');
    $this->addRule('new_mail', 'A user is already registered with this email address', 'not_in_directory');
    $this->addElement('submit', 'bouton_change_mail', 'Ok');
	}
	
  function createChangePasswordForm() {
    $this->addElement('password', 'password_1', 'New Password');
    $this->applyFilter('password_1', 'trim');
    $this->addRule('password_1', 'New Password is required', 'required');
    $this->addRule('password_1', 'Password must be at least 4 characters in length', 'minlength', 4);
    $this->addElement('password', 'password_2', 'New Password (confirm)');
    $this->applyFilter('password_2', 'trim');
    $this->addRule('password_2', 'New Password (confirm) is required', 'required');
    $this->addRule(array(
      'password_1',
      'password_2',
    ), "Entered passwords are not the same", 'compare', 'eq');
    $this->addElement('submit', 'bouton_change_password', 'Ok');
	}
	
  function displayProfile() {
    global $project_name, $MainProjects;
    echo '<table>';
    if (isset($this->user->affiliation) && !empty($this->user->affiliation)) {
      echo '<tr><th colspan="4" align="center"><strong>Institution</strong></th></tr>';
      echo "<tr><td><strong>Affiliation</strong></td><td colspan='3'>" . $this->user->affiliation . "</td></tr>";
      echo "<tr><td><strong>Address</strong></td><td colspan='3'>" . $this->user->street . "</td></tr>";
      echo "<tr><td><strong>Zip Code</strong></td><td>" . $this->user->zipCode . "</td><td><strong>City</strong></td><td>" . $this->user->city . "</td></tr>";
      echo "<tr><td><strong>Country</strong></td><td colspan='3'>" . countries::getDisplayName($this->user->country) . "</td></tr>";
    }
    echo '<tr><th colspan="4" align="center"><strong>Contact</strong></th></tr>';
    if (isset($this->user->phoneNumber) && !empty($this->user->phoneNumber)) {
      echo "<tr><td><strong>Telephone</strong></td><td colspan='3'>" . $this->user->phoneNumber . "</td></tr>";
    }
    echo "<tr><td><strong>Mail</strong></td><td colspan='3'>" . $this->user->mail . "</td></tr>";
    if (isset($this->user->memberOf) && !empty($this->user->memberOf)) {
      echo '<tr><th colspan="4" align="center"><strong>Group(s)</strong></th></tr>';
      echo "<td colspan='4'>";
      foreach ($this->user->memberOf as $group) {
        echo $this->groups[$group] . '<br>';
      }
      echo "</td>";
    }
    if (isset($this->user->abstract) && !empty($this->user->abstract)) {
      echo '<tr><th colspan="4" align="center"><strong>Planned Work</strong></th></tr>';
      echo "<tr><td><strong>Description</strong></td><td colspan='3'>" . $this->user->abstract . "</td></tr>";
      if ($MainProjects != NULL) {
        foreach ($MainProjects as $project) {
          if (isset($this->user->attrs[strtolower($project) . 'Abstract']) && !empty($this->user->attrs[strtolower($project) . 'Abstract'])) {
            if (is_array($this->user->attrs[strtolower($project) . 'Abstract'])) {
              echo "<tr><td><strong>Work in $project</strong></td><td colspan='3'>" . $this->user->attrs[strtolower($project) . 'Abstract'][0] . "</td></tr>";
            } else {
              echo "<tr><td><strong>Work in $project</strong></td><td colspan='3'>" . $this->user->attrs[strtolower($project) . 'Abstract'] . "</td></tr>";
            }

          }
        }
      }
      if (isset($this->user->associatedProject) && !empty($this->user->associatedProject)) {
        echo "<tr><td><strong>Member of</strong></td><td colspan='3'>" . $this->user->associatedProject . "</td></tr>";
      }
    }
    if (isset($this->user->supervisor_name) && !empty($this->user->supervisor_name)) {
      echo '<tr><th colspan="4" align="center"><strong>Supervisor</strong></th></tr>';
      echo "<tr><td><strong>name</strong></td><td>" . $this->user->supervisor_name . "</td><td><strong>Affiliation</strong></td><td>" . $this->user->supervisor_affiliation . "</td></tr>";
    }
    echo "</table>";
	}
	
  function displayChangePasswordForm() {
    // Affichage des erreurs
    if (!empty($this->_errors)) {
      foreach ($this->_errors as $error) {
        echo '<span class="danger">' . $error . '</span><br>';
      }
    }
    $reqUri = $_SERVER['REQUEST_URI'];
    echo '<form action="' . $reqUri . '" method="post" name="frmpasswd" id="frmpasswd" >';
    echo '<table>';
    echo '<tr><td><span class="info">' . $this->getElement('password_1')->getLabel() . '</span></td><td>' . $this->getElement('password_1')->toHTML() . '</td></tr>';
    echo '<tr><td><span class="info">' . $this->getElement('password_2')->getLabel() . '</span></td><td>' . $this->getElement('password_2')->toHTML() . '</td></tr>';
    echo '<tr><td colspan="2" align="center">' . $this->getElement('bouton_change_password')->toHTML() . '</td></tr></table>';
    echo '</table>';
    echo '</form>';
	}
	
  function displayChangeEmailForm() {
    // Affichage des erreurs
    if (!empty($this->_errors)) {
      foreach ($this->_errors as $error) {
        echo '<span class="danger">' . $error . '</span><br>';
      }
    }
    $reqUri = $_SERVER['REQUEST_URI'];
    echo '<form action="' . $reqUri . '" method="post" name="frmmail" id="frmmail" >';
    echo '<table>';
    echo '<tr><td><span class="info">Current Mail</span></td><td>' . $this->user->mail . '</td></tr>';
    echo '<tr><td><span class="info">' . $this->getElement('new_mail')->getLabel() . '</span></td><td>' . $this->getElement('new_mail')->toHTML() . '</td></tr>';
    echo '<tr><td colspan="2" align="center">' . $this->getElement('bouton_change_mail')->toHTML() . '</td></tr></table>';
    echo '</table>';
    echo '</form>';
	}

}

?>

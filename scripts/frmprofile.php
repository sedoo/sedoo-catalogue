<?php
require_once "forms/profile_form.php";
require_once "forms/journal_form.php";
require_once 'forms/user_form_multi_projects.php';
require_once "ldap/validator.php";
require_once "ldap/projectUser.php";

/*
 * Teste si element n'est pas vide qu'un 2e champ est rempli.
 * element: element sur lequel s'applique la regle value
 * value: valeur saisie
 * args: array(0 => formulaire, 1 => champ texte à vérifier)
 */
function valid_xor($element, $value, $args) {
  $arg_value = $args[0]->exportValue($args[1]);
  if (empty($value) xor empty($arg_value)) {
    return false;
  } else {
    return true;
  }
}

$form = new profile_form();
$form_user = new user_form_new();
$form->createForm();
$form_user->createForm();

if ($form->isLogged()) {
  if (isset($_REQUEST['pageId']) || !empty($_REQUEST['pageId'])) {
    $pageId = $_REQUEST['pageId'];
  }
  if (!isset($pageId) || empty($pageId)) {
    $pageId = 1;
  }
  if ($pageId == 1) {
    echo "<div class='aligncenter'>";
    $form->user = unserialize($_SESSION['loggedUser']);
    echo "<h1>" . $form->user->cn . "</h1>";
    $form->displayProfile();
    $form_user->displayModifyButton();
    echo "</div>";
    unset($_SESSION['init']);
  } else if ($pageId == 2) {
    $form->createChangePasswordForm();
    echo "<h1>Change Password</h1>";
    if (isset($_POST["bouton_change_password"])) {
      if ($form->validate()) {
        if ($form->changePassword($project_name)) {
          echo "<span class='success'><strong>The password has been updated successfully.</strong></span><br>";
        } else {
          echo "<span class='danger'><strong>Sorry, the system was unable to change your password.</strong></span><br>";
          $form->displayChangePasswordForm();
        }
      } else {
        $form->displayChangePasswordForm();
      }
    } else {
      $form->displayChangePasswordForm();
    }
    unset($_SESSION['init']);
  } else if ($pageId == 4) {
    include 'frm_duplicate_dataset.php';
  } else if ($pageId == 5) {
    if (array_key_exists('type', $_REQUEST)) {
      $typeJournal = $_REQUEST['type'];
    } else {
      $typeJournal = 0;
    }
    $jform = new journal_form();
    $jform->createForm(true);
    if (isset($_POST['add'])) {
      $jform->addAbo();
    } else {
      foreach (array_keys($_POST) as $key) {
        if (strpos($key, 'del_') === 0) {
          $id = substr($key, 4);
          $jform->deleteAbo($id);
        }
      }
    }
    $jform->displayList($typeJournal);
    unset($_SESSION['init']);
  } else if ($pageId == 6) {
    include 'frmsuscribe.php';
    unset($_SESSION['init']);
  } else if ($pageId == 7) {
    include 'frmdbrequests.php';
    unset($_SESSION['init']);
  } else if ($pageId == 9) {
    if (!isset($_SESSION['init']) && empty($_SESSION['init'])) {
      $_SESSION['init'] = 0;
    }
    echo '<form action="' . $reqUri . '" method="post" name="frmuser" id="frmuser" >';
    echo '<SCRIPT LANGUAGE="Javascript" SRC="/js/functions.js"> </SCRIPT>';
    echo "<script type='text/javascript'>
				$(function (){UseDialogForm();})
			</script>";
    $form_user->addProjectAbstract();
    if (isset($_SESSION['init']) && $_SESSION['init'] == 0) {
      $form_user->initUser(true);
    }
    echo "<div class='aligncenter'>";
    $form_user->disableElement('mail');
    $form_user->displayFormRegister(true);
    echo "</div>";
    $_SESSION['init'] = 1;
    $form_user->addValidationRules();
    if (isset($_POST['bouton_update'])) {
      if ($form_user->validate()) {
        if ($form_user->updateUser()) {
          $form_user->updateUserProfile();
          unset($_SESSION['init']);
          header("Location: ?p&pageId=1");
        }
      } else {
        if (!empty($form_user->_errors)) {
          foreach ($form_user->_errors as $error) {
            echo '<span class="danger">' . $error . '</span><br>';
          }
        }
      }
    }
  } else if ($pageId == 10) {
    include 'frmStatsPi.php';
    unset($_SESSION['init']);
  } else if ($pageId == 11) {
    $form->user = unserialize($_SESSION['loggedUser']);
    $form_user->initUser();
    echo "<h1>" . MainProject . " data access registration</h1><br/>";
    if ($form->user->isMemberOf(array(
      strtolower(MainProject),
      strtolower(MainProject) . 'Adm',
    ))) {
      echo "<span class='success'><strong>You are already registered to access " . MainProject . " data.</strong></span><br>";
    } else {
      if (isset($_POST['bouton_save'])) {
        if ($form_user->validate() && $form_user->validateChart(true)) {
          $form_user->savePortalDataPolicyForm();
          $form_user->requestPortalDataAccess();
          $milieu = ob_get_clean();
          echo "<h1>" . MainProject . " data access registration</h1><br/>";
          echo "<span class='success'><strong>The request has been registered, please wait for the administrator confirmation (you will receive a mail).</strong></span><br>";
        } else {
          if (!empty($form_user->_errors)) {
            foreach ($form_user->_errors as $error) {
              echo '<span class="danger">' . $error . '</span><br>';
            }
          }
          echo '<form  method="post" name="frmportaldatapolicy" id="frmportaldatapolicy" >';
          echo '<SCRIPT LANGUAGE="Javascript" SRC="/js/functions.js"> </SCRIPT>';
          $form_user->displayPortalDataPolicy();
          echo '</form>';
        }
      } else {
        if (!empty($form_user->_errors)) {
          foreach ($form_user->_errors as $error) {
            echo '<span class="danger">' . $error . '</span><br>';
          }
        }
        echo '<form  method="post" name="frmportaldatapolicy" id="frmportaldatapolicy" >';
        echo '<SCRIPT LANGUAGE="Javascript" SRC="/js/functions.js"> </SCRIPT>';
        $form_user->displayPortalDataPolicy();
        echo '</form>';
      }
    }
  } else if ($pageId > 14) {
    $form_user->project = null;
    $form_user->project[0] = $MainProjects[($pageId - 15)];
    if ($pageId > (14 + count($MainProjects))) {
      $form_user->project[0] = $OtherProjects[($pageId - (15 + count($MainProjects)))];
    }

    $form->user = unserialize($_SESSION['loggedUser']);
    $form_user->initUser();
    echo "<h1>" . $form_user->project[0] . " data access registration</h1><br/>";

    if ($form->user->isMemberOf(array(
      strtolower($form_user->project[0]) . 'Core',
      strtolower($form_user->project[0]) . 'Asso',
    ))) {
      echo "<span class='success'><strong>You are already registered to access " . $form_user->project[0] . "data.</strong></span><br>";
    } else if ((array_key_exists(strtolower($form_user->project[0]) . 'ApplicationDate', $form->user->attrs[strtolower($form_user->project[0])]) && $form->user->attrs[strtolower($form_user->project[0]) . 'Status'][0] == 'pending') || (array_key_exists(strtolower($form_user->project[0]) . 'ApplicationDate', $form->user->attrs) && $form->user->attrs[strtolower($form_user->project[0]) . 'Status'][0] == 'pending')) {
      echo "<span class='warning'><strong>You have already submitted a request, please wait for the administrator confirmation (you will receive a mail).</strong></span><br>";
    } else if ((array_key_exists(strtolower($form_user->project[0]) . 'ApplicationDate', $form->user->attrs[strtolower($form_user->project[0])]) && $form->user->attrs[strtolower($form_user->project[0]) . 'Status'][0] == 'rejected') || (array_key_exists(strtolower($form_user->project[0]) . 'ApplicationDate', $form->user->attrs) && $form->user->attrs[strtolower($form_user->project[0]) . 'Status'][0] == 'rejected')) {
      echo "<span class='danger'><strong>We have received your request for an access to the " . $form_user->project[0] . " database. Considering the details you provided, your request has been rejected.\n If you think that your request should have been agreed or if you would like to collaborate with some " . $form_user->project[0] . " scientists, please contact
" . constant(strtolower($form_user->project[0]) . _AdminGroup_Email) . " .</strong></span><br>";
    } else {
      $form_user->addProjectDataPolicy();
      if (isset($_POST['bouton_save'])) {
        if ($form_user->validate() && $form_user->validateChart()) {
          $form_user->saveDataPolicyForm();
          $form_user->addProjectUser();
          $milieu = ob_get_clean();
          echo "<h1>" . $form_user->project[0] . " data access registration</h1><br/>";
          echo "<span class='success'><strong>The request has been registered, please wait for the administrator confirmation (you will receive a mail).</strong></span><br>";
        } else {
          if (!empty($form_user->_errors)) {
            foreach ($form_user->_errors as $error) {
              echo '<span class="danger">' . $error . '</span><br>';
            }
          }
          echo '<form  method="post" name="frm' . strtolower($form_user->project[0]) . 'datapolicy" id="frm' . strtolower($form_user->project[0]) . 'datapolicy" >';
          echo '<SCRIPT LANGUAGE="Javascript" SRC="/js/functions.js"> </SCRIPT>';
          $form_user->displayProjectDataPolicy();
          echo '</form>';
        }
      } else {
        if (!empty($form_user->_errors)) {
          foreach ($form_user->_errors as $error) {
            echo '<span class="danger">' . $error . '</span><br>';
          }
        }
        echo '<form  method="post" name="frm' . strtolower($form_user->project[0]) . 'datapolicy" id="frm' . strtolower($form_user->project[0]) . 'datapolicy" >';
        echo '<SCRIPT LANGUAGE="Javascript" SRC="/js/functions.js"> </SCRIPT>';
        $form_user->displayProjectDataPolicy();
        echo '</form>';
      }
    }
    unset($_SESSION['init']);
  }
} else if ($form->isCat()) {
  echo "<span class='danger'><strong>You cannot modify the account " . $form->user->cn . "</strong></span><br>";
} else {
  $form->displayLGForm("", true);
}

?>

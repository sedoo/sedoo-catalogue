<?php
require_once "forms/user_form_multi_projects.php";

/*
 * Teste si element n'est pas vide qu'un 2e champ est rempli. element: element sur lequel s'applique la regle value: valeur saisie args: array(0 => formulaire, 1 => champ texte à vérifier)
 */
function valid_xor($element, $value, $args)
{
    $arg_value = $args[0]->exportValue($args[1]);
    if (empty($value) xor empty($arg_value)) {
        return false;
    } else {
        return true;
    }
}

$formReg = new user_form_new();
$formReg->project[0] = $project_name;
$formReg->createForm();

if (isset($_SESSION['loggedUser']) && !empty($_SESSION['loggedUser'])) {
    $user = unserialize($_SESSION['loggedUser']);
}
if (isset($_SESSION['loggedUser']) && !empty($_SESSION['loggedUser'])) {
    if ($project_name == strtolower(MainProject)) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . '/Your-Account/?p&pageId=11');
    } elseif (in_array($project_name, $MainProjects)) {
        $Project_pageId = 11;
        while ($project = current($MainProjects)) {
            if ($project == $project_name) {
                $Project_pageId = key($MainProjects) + 15;
            }
            next($MainProjects);
        }
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/Your-Account/?p&pageId=' . $Project_pageId);
    }
} else {
    if (isset($user) && !empty($user)) {
        $formReg->getElement('mail')->setValue($user->mail);
        $formReg->check();
    } elseif (isset($_POST['bouton_check'])) {
        if ($formReg->validate()) {
            $formReg->check();
        }
    } elseif (isset($_POST['bouton_save'])) {
      // if (in_array($project_name, $MainProjects)) {
      //   $formReg->saveForm ( true );
      //   if ($formReg->validate () && $formReg->validateChart ()) {
      //     if ($formReg->addUser ( true )) {
      //       $formReg->addProjectUser ( true );
      //       echo "<span class='success'><strong>\n\nYour portal account was created.\n</strong><br><strong>Your access privileges will be temporarily limited to public datasets until your identity is verified and approved by the administrator.\n</strong>" . "<br><strong>Once your registration to access $project_name data will be approved, you will receive a confirmation mail.\n</strong></span><br>";
      //       return;
      //     }
      //   }
      // } else {
        $formReg->saveForm();
        if ($formReg->validate() && $formReg->validateChart(true)) {
            if ($formReg->addUser(true)) {
                echo "<span class='success'><strong>\n\nYour portal account was created.\n</strong><br><strong>Your access privileges will be temporarily limited to public datasets until your identity is verified and approved by the administrator.\n</strong>" . "<br><strong>Once your registration to access $project_name data will be approved, you will receive a confirmation mail.\n</strong></span><br>";
                return;
            }
        }
      // }
    } elseif (isset($_POST['bouton_update'])) {
        if (in_array($project_name, $MainProjects)) {
            $formReg->saveForm(true);
            if ($formReg->validate() && $formReg->validateChart()) {
                if ($formReg->updateUser()) {
                    echo "<span class='success'><strong>The request has been registered.</strong></span><br>";
                    return;
                }
            }
        } else {
            $formReg->saveForm();
            if ($formReg->validate() && $formReg->validateChart(true)) {
                if ($formReg->updateUser()) {
                    echo "<span class='success'><strong>The request has been registered.</strong></span><br>";
                    return;
                }
            }
        }
    } elseif (isset($_POST['bouton_login_reg'])) {
        if ($formReg->validate()) {
            $formReg->doLogin();
        }
    } elseif (isset($_POST['bouton_forgot'])) {
        if ($formReg->doForgot()) {
            echo "<span class='success'><strong>A new password has been generated and sent to you by email.</strong></span><br>";
        }
    }
    global $project_name;
  // if (in_array($project_name, $MainProjects)) {
  //   $formReg->displayForm ( true );
  // }
  // else
    $formReg->displayForm(false);
}

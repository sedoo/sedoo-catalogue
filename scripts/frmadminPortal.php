<?php
require_once "forms/admin_form.php";

echo '<script src="/js/admin.js"></script>';

$form = new admin_form();
$form->createForm();

if ($form->isPortalAdmin()) {
  if (isset($_REQUEST['pageId']) && !empty($_REQUEST['pageId'])) {
    $pageId = $_REQUEST['pageId'];
  }

  if (!isset($pageId) || empty($pageId)) {
    $pageId = 1;
  }
  if (isset($_REQUEST['update']) && !empty($_REQUEST['update'])) {
    $reqId = $_REQUEST['update'];
  }

  // Contenus
  if ($pageId == 1) {
    echo "<h1>Registered Users</h1><br>";
    if (isset($reqId) && !empty($reqId)) {
      if (isset($_POST["bouton_update_$reqId"])) {
        // Suppression user enregistré
        if ($form->updateUser($reqId)) {
        } else {
          echo "<font size=\"3\" color='red'><strong>An error occurred during the update.</strong></font><br>";
        }
      } else if (isset($_POST["bouton_unregister_$reqId"])) {
        if ($form->unregisterUser($reqId)) {
        } else {
          echo "<font size=\"3\" color='red'><strong>An error occurred during the update.</strong></font><br>";
        }
      }
    }
    if (isset($_REQUEST['first']) && !empty($_REQUEST['first'])) {
      $first = $_REQUEST['first'];
    }

    if (!isset($first) || empty($first)) {
      $first = 1;
    }
    if (isset($_POST["bouton_search"])) {
      $first = $form->searchUser();
    }

    $form->displayRegisteredUsersList($first, 20);
  } else if ($pageId == 2) {
    echo "<h1>Registration Requests</h1>";
    if (isset($reqId) && !empty($reqId)) {
      if (isset($_POST["bouton_register_$reqId"])) {
        if ($form->registerUser($reqId)) {
        } else {
          echo "<font size=\"3\" color='red'><strong>Registration failure.</strong></font><br>";
        }
      } else if (isset($_POST["bouton_reject_$reqId"])) {
        if ($form->rejectUser($reqId)) {
        } else {
          echo "<font size=\"3\" color='red'><strong>An error occured.</strong></font><br>";
        }
      }
    }
    $form->displayPendingRequestsList();
  } else if ($pageId == 3) {
    echo "<h1>Rejected Registration Requests</h1>";
    if (isset($reqId) && !empty($reqId)) {
      if (isset($_POST["bouton_restore_$reqId"])) {
        if ($form->restoreUser($reqId)) {
        } else {
          echo "<font size=\"3\" color='red'><strong>Restoration failure.</strong></font><br>";
        }
      } else if (isset($_POST["bouton_delete_$reqId"])) {
        if ($form->deleteUser($reqId)) {
        } else {
          echo "<font size=\"3\" color='red'><strong>An eror occurred</strong></font><br>";
        }
      }
    }
    $form->displayRejectedRequestsList();
  } else if (($pageId == 5)) {
    include 'frmjournal.php';
  } else if (($pageId == 6)) {
    include 'frmurl.php';
  } else if (($pageId == 7)) {
    include 'frmstats.php';
  } else if (($pageId == 11)) {
    include 'frmroles.php';
  } else if (($pageId == 13)) {
    include 'frmquality.php';
  } else if (($pageId == 14)) {
    include 'frmdoi.php';
  } else if ($pageId == 16) {
    echo "<h1>Registered Users in all " . MainProject . " projects</h1><br>";
    $form->displayRegisteredUsersListByProject($MainProjects);
  } else if (($pageId == 19)) {
    include 'utils/elastic/frmElastic.php';
  }
} else if ($form->isLogged()) {
  echo "<h1>Admin Corner</h1>";
  echo "<font size=\"3\" color='red'><strong>You cannot view this part of the site.</strong></font><br>";
} else {
  $form->displayLGForm("", true);
}

?>

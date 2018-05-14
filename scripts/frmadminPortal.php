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
          echo "<span class='danger'><strong>An error occurred during the update.</strong></span><br>";
        }
      } else if (isset($_POST["bouton_unregister_$reqId"])) {
        if ($form->unregisterUser($reqId)) {
        } else {
          echo "<span class='danger'><strong>An error occurred during the update.</strong></span><br>";
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
          echo "<span class='danger'><strong>Registration failure.</strong></span><br>";
        }
      } else if (isset($_POST["bouton_reject_$reqId"])) {
        if ($form->rejectUser($reqId)) {
        } else {
          echo "<span class='danger'><strong>An error occured.</strong></span><br>";
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
          echo "<span class='danger'><strong>Restoration failure.</strong></span><br>";
        }
      } else if (isset($_POST["bouton_delete_$reqId"])) {
        if ($form->deleteUser($reqId)) {
        } else {
          echo "<span class='danger'><strong>An eror occurred</strong></span><br>";
        }
      }
    }
    $form->displayRejectedRequestsList();
  } else if ($pageId == 4) {
    echo "<h1$project_name participants";
    if (constant('PORTAL_WebSite') != '') {
      echo "(" . constant('PORTAL_WebSite') . " users)";
    }
    echo "</h1>";
    $form->displayParticipantsList();    
  } else if (($pageId == 5)) {
    include 'frmjournal.php';
  } else if (($pageId == 6)) {
    include 'frmurl.php';
  } else if (($pageId == 7)) {
    include 'frmstats.php';
  } else if (($pageId == 8)) {
    include 'frmgroups.php';    
  } else if (($pageId == 9)) {
    include 'frmInsDats.php';
  } else if (($pageId == 10)) {
    include 'frmInsParams.php';    
  } else if (($pageId == 11)) {
    include 'frmroles.php';
  } else if (($pageId == 12)) {
    include 'frmdbrequests.php';    
  } else if (($pageId == 13)) {
    include 'frmquality.php';
  } else if (($pageId == 14)) {
    include 'frmdoi.php';
  } else if (($pageId == 15)) {
    include 'database-content-admin.php';    
  } else if ($pageId == 16) {
    include 'archive-dataset.php';
  } else if (($pageId == 17)) {
    include 'ContactProjectUsers.php';    
  } else if (($pageId == 19)) {
    include 'utils/elastic/frmElastic.php';
  }
} else if ($form->isLogged()) {
  echo "<h1>Admin Corner</h1>";
  echo "<span class='danger'><strong>You cannot view this part of the site.</strong></span><br>";
} else {
  $form->displayLGForm("", true);
}

?>

<?php
require_once "conf/conf.php";
require_once "forms/admin_form.php";

echo '<SCRIPT LANGUAGE="Javascript" SRC="/js/admin.js"> </SCRIPT>';

$form = new admin_form();
$form->createForm();

if ($form->isAdmin()) {
    if (isset($_REQUEST['pageId']) || !empty($_REQUEST['pageId'])) {
        $pageId = $_REQUEST['pageId'];
    }
    if (!isset($pageId) || empty($pageId)) {
        $pageId = 1;
    }
    if (isset($_REQUEST['update']) || !empty($_REQUEST['update'])) {
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
            } elseif (isset($_POST["bouton_unregister_$reqId"])) {
                if ($form->unregisterUser($reqId)) {
                } else {
                    echo "<span class='danger'><strong>An error occurred during the update.</strong></span><br>";
                }
            }
        }
        if (isset($_REQUEST['first']) || !empty($_REQUEST['first'])) {
            $first = $_REQUEST['first'];
        }
        if (!isset($first) || empty($first)) {
            $first = 1;
        }
        if (isset($_POST["bouton_search"])) {
            $first = $form->searchUser();
        }
        $form->displayRegisteredUsersList($first);
    } elseif ($pageId == 2) {
        echo "<h1>Registration Requests</h1>";
        if (isset($reqId) && !empty($reqId)) {
            if (isset($_POST["bouton_register_$reqId"])) {
                if ($form->registerUser($reqId)) {
                } else {
                    echo "<span class='danger'><strong>Registration failure.</strong></span><br>";
                }
            } elseif (isset($_POST["bouton_reject_$reqId"])) {
                if ($form->rejectUser($reqId)) {
                } else {
                    echo "<span class='danger'><strong>An error occured.</strong></span><br>";
                }
            }
        }
        $form->displayPendingRequestsList();
    } elseif ($pageId == 3) {
        echo "<h1>Rejected Registration Requests</h1>";
        if (isset($reqId) && !empty($reqId)) {
            if (isset($_POST["bouton_restore_$reqId"])) {
                if ($form->restoreUser($reqId)) {
                } else {
                    echo "<span class='danger'><strong>Restoration failure.</strong></span><br>";
                }
            } elseif (isset($_POST["bouton_delete_$reqId"])) {
                if ($form->deleteUser($reqId)) {
                } else {
                    echo "<span class='danger'><strong>An eror occurred</strong></span><br>";
                }
            }
        }
        $form->displayRejectedRequestsList();
    } elseif ($pageId == 4) {
        echo "<h1$project_name participants";
        if (constant(strtolower($project_name) . 'WebSite') != '') {
            echo "(" . constant(strtolower($project_name) . 'WebSite') . " users)";
        }

        echo "</h1>";
        $form->displayParticipantsList();
    } elseif (($pageId == 5)) {
      // echo "<h1>Journal</h1>";
        include 'frmjournal.php';
    } elseif (($pageId == 6)) {
        include 'frmurl.php';
    } elseif (($pageId == 7)) {
        include 'frmstats.php';
    } elseif (($pageId == 8)) {
        include 'frmgroups.php';
    } elseif (($pageId == 9)) {
        include 'frmInsDats.php';
    } elseif (($pageId == 10)) {
        include 'frmInsParams.php';
    } elseif (($pageId == 11)) {
        include 'frmroles.php';
    } elseif (($pageId == 12)) {
        include 'frmdbrequests.php';
    } elseif (($pageId == 13)) {
        include 'frmquality.php';
    } elseif (($pageId == 14)) {
        include 'frmdoi.php';
    } elseif (($pageId == 15)) {
        include 'database-content-admin.php';
    } elseif (($pageId == 16)) {
        include 'archive-dataset.php';
    } elseif (($pageId == 17)) {
        include 'ContactProjectUsers.php';
    }
} elseif ($form->isLogged()) {
    echo "<h1>Admin Corner</h1>";
    echo "<span class='danger'><strong>You cannot view this part of the site.</strong></span><br>";
} else {
    $form->displayLGForm("", true);
}

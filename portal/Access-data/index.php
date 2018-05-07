<?php
  if (!isset($_SESSION)) {
    session_start();
  }
  require_once 'conf/define-project.php'; 
  $project_url = "/";
  $titreMilieu = "Access data";
  ob_start();
?>
<div class="column1-unit">
  <br><br>
  <div class="">
    <p>The <?= $project_name; ?> Database offers you full public access to its metadata catalogue through the <a href="/portal/Browse-Catalogue">"Browse catalogue"</a> section.<br class="autobr">
    However, access to data is restricted to <?= $project_name; ?> registered users, as described in the <a href="/portal/Data-Policy"><?= $project_name; ?> data policy section</a>.</p>

    <p>If you are not a registered user of the <?= $project_name; ?> Database, you can ask for access by filling the <a href="/portal/Data-Access-Registration">on-line registration form</a>.</p>

    <p>If you are a registered user, you can click on the dataset title to access metadata, or on the blue flag <span><img src="/img/dataOk.gif" alt="" style="" height="16" width="15"></span> next to it to access data.</p>
  </div>
</div>

<?php
  $milieu = ob_get_clean();
  include "template.php";
?>

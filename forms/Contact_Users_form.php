<?php

require_once "conf/conf.php";
require_once "HTML/QuickForm.php";
require_once "ldap/ldapConnect.php";
require_once "mail.php";

class Contact_Users_form extends HTML_QuickForm
{

    public function createForm()
    {
        $this->addElement('textarea', 'EditionArea', 'Message', array('cols' => 50, 'rows' => 8));
        $this->addRule('EditionArea', 'You have to write your message first', 'required');
        $this->addElement('text', 'Subject', 'Subject', array('cols' => 50, 'rows' => 8));
        $this->addRule('Subject', 'The subject is required', 'required');
        $this->addElement('submit', 'bouton_send', 'Send', array('style' => 'text-align:center;'));
    }

    public function display()
    {
      //Affichage des erreurs
        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                echo '<span class="danger">' . $error . '</span><br>';
            }
        }
        $reqUri = $_SERVER['REQUEST_URI'];

        echo '<form action="' . $reqUri . '" method="post" name="frmContactUsers" id="frmContactUsers" >';
        echo '<table><tr><td colspan="3" align="center"><span class="info">Mandatory fields are in blue</span></td></tr>';
        echo '<tr><td><span class="info">' . $this->getElement('Subject')->getLabel() . '</span></td><td colspan="2">' . $this->getElement('Subject')->toHTML() . '</td></tr>';
        echo '<tr><td><span class="info">' . $this->getElement('EditionArea')->getLabel() . '</span></td><td colspan="2">' . $this->getElement('EditionArea')->toHTML() . '</td></tr>';
        echo '<tr><td colspan="3" align="center">' . $this->getElement('bouton_send')->toHTML() . '</td></tr>';
        echo '</table></form>';
    }
    
    public function getProjectUsers($project)
    {
        $ldap = new ldapConnect();
        $ldap->openAdm();
        $projectUsers = $ldap->listEntries(PEOPLE_BASE, '(&(objectClass=' . strtolower($project) . 'User)(objectClass=registeredUser)(' . strtolower($project) . 'Status=' . STATUS_ACCEPTED . '))', strtolower($project) . 'User', 'sn');
        $ldap->close();
        return $projectUsers;
    }

    public function sendMessageToAllUsers($project)
    {
        $projectUsers = $this->getProjectUsers($project);
        $Subject = $this->exportValue('Subject');
        $Message = $this->exportValue('EditionArea');
        foreach ($projectUsers as $user) {
            sendMailSimple($user->mail, $Subject, $Message, ROOT_EMAIL, true);
        }
    }
}

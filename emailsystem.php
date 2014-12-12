<?php

require_once 'emailsystem.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function emailsystem_civicrm_config(&$config) {
  _emailsystem_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function emailsystem_civicrm_xmlMenu(&$files) {
  _emailsystem_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function emailsystem_civicrm_install() {
  return _emailsystem_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function emailsystem_civicrm_uninstall() {
  return _emailsystem_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function emailsystem_civicrm_enable() {
  return _emailsystem_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function emailsystem_civicrm_disable() {
  return _emailsystem_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function emailsystem_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _emailsystem_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function emailsystem_civicrm_managed(&$entities) {
  return _emailsystem_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function emailsystem_civicrm_caseTypes(&$caseTypes) {
  _emailsystem_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function emailsystem_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _emailsystem_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_alterScheduleReminderQuery
 *
 */
function emailsystem_civicrm_alterScheduleReminderQuery(&$queryParams, $scheduleReminder) {
  if (in_array($scheduleReminder->name, CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames())) {
    $queryParams['dateClause'] = str_replace('participant_register_date', 'e.register_date', $queryParams['dateClause'], $count);
    if ($count) {
      $queryParams['dateClause'] .= CRM_Emailsystem_BAO_Emailsystem::getAdditionalWhereClause($scheduleReminder->name);
    }
  }
}

/**
 * Implementation of hook_civicrm_buildForm to add options for start_action_date
 *
 */
function emailsystem_civicrm_buildForm($formName, &$form) {
  if ('CRM_Admin_Form_ScheduleReminders' == $formName && $form->getVar('_id')) {
    $values = $form->getVar('_values');
    if (!in_array($values['name'], CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames())) {
      return NULL;
    }
    
    $elements = & $form->getElement('start_action_date');
    $elements->_options = array_merge($elements->_options, array(
      array(
        'text' => 'Registration Date',
        'attr' => array('value' => 'event_registration_start_date')
      ),
      array(
        'text' => 'Enrolled date',
        'attr' => array('value' => 'participant_register_date')
      ),
    ));    
  }
}

/**
 * Implementation of hook_civicrm_alterMailParams to alter CC and toEmail
 *
 */
function emailsystem_civicrm_alterMailParams(&$params) {
    /* switch (CRM_Utils_Array::value('groupName', $params)) { */
    /*   case 'Scheduled Reminder Sender': */
    /*     CRM_Emailsystem_BAO_Emailsystem::addCCToAdmin($params); */
    /*     break; */
        
    /*   case 'msg_tpl_workflow_event': */
    /*     if ($params['valueName'] == 'event_offline_receipt') { */
    /*     } */
    /*     break; */
    /* } */
    
    if ('Scheduled Reminder Sender' == CRM_Utils_Array::value('groupName', $params)) {
      CRM_Emailsystem_BAO_Emailsystem::addCCToAdmin($params);
    }
    
}
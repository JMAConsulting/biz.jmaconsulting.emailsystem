<?php

require_once 'emailsystem.civix.php';
require_once 'civicrm_constants.php';

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
  emailsystem_civicrm_actionschedule($entities);
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
  if (array_key_exists($scheduleReminder->name, CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames())) {
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
    if (!array_key_exists($values['name'], CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames())) {
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
  if ('Scheduled Reminder Sender' == CRM_Utils_Array::value('groupName', $params)) {
    CRM_Emailsystem_BAO_Emailsystem::addCCToAdmin($params);
  }
}

/**
 * Implementation of hook_civicrm_tokenValues
 *
 */
function emailsystem_civicrm_tokenValues(&$values, $cids) {
  
  if (array_key_exists('event.event_id', $values)) {
    $values['event.start_end_date'] = CRM_Emailsystem_BAO_Emailsystem::getDateFormatted(
      $values['event.event_id'], 
      $values['event.start_date'], 
      $values['event.end_date']
    );
    CRM_Core_Smarty::singleton()->assign('eventStartDate', $values['event.start_date']);
  }
}

/**
 * Implementation of hook_civicrm_tokens
 *
 */
function emailsystem_civicrm_tokens(&$tokens) {
  $tokens['event'] = array(
    'event.start_end_date' => 'Event Start-End Date',
  );
}

/**
 * function to Manage Schedule reminder's
 *
 */
function emailsystem_civicrm_actionschedule(&$entities) { 
  $eventTypes = CRM_Core_PseudoConstant::get('CRM_Event_DAO_Event', 'event_type_id', array('labelColumn' => 'value'));
  $eventTypes = implode('', $eventTypes);

  $participantRole = CRM_Core_PseudoConstant::get('CRM_Event_DAO_Participant', 'role_id', array('labelColumn' => 'name'));
  $participantRole = array_search('Instructor', $participantRole);
  foreach(CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames() as $name => $value) { 
    $entities[] = array(
      'module' => 'biz.jmaconsulting.emailsystem',
      'name' => $name,
      'entity' => 'action_schedule',
      'params' => array_merge(array(
        'version' => 3,
        'title' => ts($value[0]),
        'name' => $name,
        'entity_value' => $eventTypes,
        'recipient' => '1',
        'limit_to' => '1',
        'recipient_listing' => (in_array($name, array('custom_schedule_reminder_12', 'custom_schedule_reminder_14'))) ? $participantRole : NULL,
        'is_repeat' => '0',
        'is_active' => '1',
        'record_activity' => '1',
        'mapping_id' => '2',
        'mode' => 'Email',
      ), CRM_Emailsystem_BAO_Emailsystem::getReminderParameters($name))
    );
  }
}

/**
 * Implementation of hook_civicrm_post
 *
 */
function emailsystem_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'GroupContact' && $op == 'create' && $objectId == IFMGA_GROUP_ID) {
    foreach ($objectRef as $contactID) {
      $sendParams = array(
        'messageTemplateID' => IFMGA_MESSAGE_TEMPLATE, 
        'contactId' => $contactID,
        'toEmail' => CRM_Contact_BAO_Contact::getPrimaryEmail($contactID),
        'tplParams' => array(),
      );
      CRM_Emailsystem_BAO_Emailsystem::sendMail($sendParams, TRUE);
    }
  } 
  
  if ($objectName == 'Participant' && $op == 'edit') {
    if (in_array($objectRef->status_id, array(PARTICIPANT_STATUS_UNDER_REVIEW, PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT))) {
    
      if (!CRM_Core_Smarty::singleton()->get_template_vars('statusChange')) {
        return FALSE;
      }
      
      $messageTemplateId = NULL;
      if ($objectRef->status_id == PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT) {
        $eventId = $objectRef->event_id;
        if (!$eventId) {
          $eventId = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Participant', $objectId, 'event_id');
        }
        $eventStartDate = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event', $eventId, 'start_date');
        $eventStartDate = strtotime($eventStartDate);
        $tenWeeks = strtotime('+10 weeks');
        if ($tenWeeks < $eventStartDate) {
          $messageTemplateId = EPP_GREATER_MSG_TPL;
        }
        elseif ($tenWeeks > $eventStartDate) {
          $messageTemplateId = EPP_LESS_MSG_TPL;        
        }
        else {
          return FALSE;
        }
      }
      else {
        $messageTemplateId = UNDER_REVIEW_MSG_TPL;
      }
    
      $contactID = $objectRef->contact_id;
      if (!$contactID) {
        $contactID = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Participant', $objectId, 'contact_id');
      }
      $sendParams = array(
        'messageTemplateID' => $messageTemplateId, 
        'contactId' => $contactID,
        'toEmail' => CRM_Contact_BAO_Contact::getPrimaryEmail($contactID),
        'tplParams' => array(),
      );
      CRM_Emailsystem_BAO_Emailsystem::sendMail($sendParams, TRUE);
    
      if ($objectRef->status_id == PARTICIPANT_STATUS_UNDER_REVIEW) {
        $sendParams = array(
          'messageTemplateID' => UNDER_REVIEW_MSG_TPL_ADMIN, 
          'contactId' => $contactID,
          'toEmail' => CRM_Emailsystem_BAO_Emailsystem::getAdminEmails(1),
          'tplParams' => array(),
        );
        CRM_Emailsystem_BAO_Emailsystem::sendMail($sendParams);
      }
      CRM_Core_Smarty::singleton()->assign('statusChange', FALSE);
      CRM_Core_Smarty::singleton()->assign('participantObject', $objectRef);
    }
  }
}

/**
 * Implementation of hook_civicrm_pre
 *
 */
function emailsystem_civicrm_pre($op, $objectName, $id, &$params) {
  if ($objectName == 'Participant' && $op == 'edit' && 
      in_array(CRM_Utils_Array::value('status_id', $params), array(PARTICIPANT_STATUS_UNDER_REVIEW, PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT))) {
    $originalStatusId = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Participant', $id, 'status_id');
    if ($originalStatusId != $params['status_id']) {
      CRM_Core_Smarty::singleton()->assign('statusChange', TRUE);
    }
  }
}

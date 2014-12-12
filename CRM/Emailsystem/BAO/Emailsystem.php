<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CiviCRM_Hook
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id: $
 *
 */

class CRM_Emailsystem_BAO_Emailsystem extends CRM_Core_DAO {
  
  /**
   * function to get 14 schedule reminder names
   *
   * @access public
   * return array 
   */
  static function getScheduleReminderNames() {
    
    return array(
     'sfdfsdfs',
     'b',
     'c',
     'd',
     'e',
     'f',
     'g',
     'h',
     'i',
     'j',
     'k',
     'l',
     'm',
     'n',
    );
  }
  
  /**
   * function to build where clause 
   *
   * @param string $scheduleReminderName name of schedule reminder
   *
   * @access public
   * return string 
   */
  static function getAdditionalWhereClause($scheduleReminderName) {
    $additionalWhereClause = '';
    switch ($scheduleReminderName) {
      case 'a':
      case 'b':
      case 'c':
        // greater than 10 week
        $additionalWhereClause = ' r.start_date';
        break;
        
      case 'd':
      case 'e':
        // less than 10 week
        $additionalWhereClause = ' r.start_date ';
        break;
    }
    
    return $additionalWhereClause;
  }
  
  /**
   * function to build admin emails
   *
   * @param array $params array of params
   *
   * @param string $context
   *
   * @access public 
   */
  static function addCCToAdmin(&$params) {
    $scheduleReminderName = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_ActionSchedule', $params['entity_id']);
    switch ($scheduleReminderName) {
      // add cc to email
      case 'a':
      case 'b':
      case 'c':
      case 'd':
      case 'e':
        $params['cc'] = self::getAdminEmails();
        break;
        
      // replace toEmail with admin
      case 'f':
      case 'g':
      case 'h':
        $params['toName'] = '';
        $params['toEmail'] = self::getAdminEmails();
        break;

      // replace toEmail with instructor
      case 'i':
      case 'j':
        $params['toName'] = '';
        $params['toEmail'] = self::getAdminEmails('instructor');
        break;
    }
  }
  
  /**
   * function to get admin or instructor emails
   *
   * @param string $context
   *
   * @access public 
   * @return string 
   */
  static function getAdminEmails($context = 'admin') {
    $email = '';
    if ($context == 'admin') {
      // get all admin emails
      $email = 'pradeep.nayak@jmaconsulting.biz, pradpnayak@gmail.com';
    }
    else {
      // get all instructor emails
      $email = '';
    }
    
    return $email;
  }
}

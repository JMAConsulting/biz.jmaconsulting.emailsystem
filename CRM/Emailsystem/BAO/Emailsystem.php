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
   * @package   CiviCRM_Hook
   * @copyright CiviCRM LLC (c) 2004-2013
   * $Id: $
   *
   */
  class CRM_Emailsystem_BAO_Emailsystem extends CRM_Core_DAO {

    /**
     * reminderParams
     * @var array
     * @static
     */
    private static $reminderParams;

    /**
     * holds event start date and end date
     * @var array
     * @static
     */
    private static $eventStartEndDate;

    /**
     * function to get 14 schedule reminder names
     *
     * @access public
     * return array
     */
    static function getScheduleReminderNames() {
      $a = 1;
      if ( empty( self::$reminderParams ) ) {
        $participantStatus = CRM_Event_PseudoConstant::participantStatus();
        $statusArray = array(
          'pending'  => 'Registered',
          'enrolled' => 'enrolled',
        );
        foreach ( $statusArray as $key => $value ) {
          $$key = array_search( $value, $participantStatus );
        }

        // must rename in the DB when adding a new?? The last param is the Message Template
        self::$reminderParams = array(
          'custom_schedule_reminder_1'  => array(
            'Application Reminder',
            array(
              '10',
              'day',
              'after',
              'event_registration_start_date',
              $pending,
              66,
            ),
          ),
          'custom_schedule_reminder_2'  => array(
            'Application Now Due',
            array(
              '14',
              'day',
              'after',
              'event_registration_start_date',
              $pending,
              67,
            ),
          ),
          'custom_schedule_reminder_3'  => array(
            'Incomplete Application Notification',
            array(
              '16',
              'day',
              'after',
              'event_registration_start_date',
              $pending,
              68,
            ),
          ),
          'custom_schedule_reminder_4'  => array(
            'Reminder - Deposit Due',
            array(
              '14',
              'day',
              'after',
              'participant_register_date',
              $enrolled,
              71,
            ),
          ),
          'custom_schedule_reminder_15' => array(
            'Reminder_Deposit_Due_Conditional_',
            array(
              '14',
              'day',
              'after',
              'participant_register_date',
              $enrolled,
              96,
            ),
          ),
          'custom_schedule_reminder_5'  => array(
            'Reminder - Deposit Past Due',
            array(
              '21',
              'day',
              'after',
              'participant_register_date',
              $enrolled,
              72,
            ),
          ),
          'custom_schedule_reminder_6'  => array(
            'Student has not paid Deposit (Admin Notification > 10 weeks)',
            array(
              '28',
              'day',
              'after',
              'participant_register_date',
              $enrolled,
              81,
            ),
          ),
          'custom_schedule_reminder_7'  => array(
            'Reminder - Program Balance Past Due',
            array(
              '7',
              'day',
              'after',
              'participant_register_date',
              $enrolled,
              74,
            ),
          ),
          'custom_schedule_reminder_8'  => array(
            'Student has not paid Deposit (Admin Notification < 10 weeks)',
            array(
              '14',
              'day',
              'after',
              'participant_register_date',
              $enrolled,
              82,
            ),
          ),
          'custom_schedule_reminder_9'  => array(
            'Reminder - Program Payment due Shortly',
            array(
              '12',
              'week',
              'after',
              'event_start_date',
              $enrolled,
              76,
            ),
          ),
          'custom_schedule_reminder_10' => array(
            'Reminder - Program Payment Due',
            array(
              '10',
              'week',
              'after',
              'event_start_date',
              $enrolled,
              77,
            ),
          ),
          'custom_schedule_reminder_11' => array(
            'Student has not paid Deposit-Admin Notification 7 days past du',
            array(
              '9',
              'week',
              'after',
              'event_start_date',
              $enrolled,
              83,
            ),
          ),
          'custom_schedule_reminder_12' => array(
            'Course Roster and Health Statements',
            array(
              '2',
              'week',
              'after',
              'event_start_date',
              '',
              78,
            ),
          ),
          'custom_schedule_reminder_13' => array(
            'AMGA Program Evaluation',
            array(
              '1',
              'day',
              'before',
              'event_end_date',
              $enrolled,
              79,
            ),
          ),
          'custom_schedule_reminder_14' => array(
            'Student Evaluation Reminder for Instructors',
            array(
              '1',
              'day',
              'before',
              'event_end_date',
              1,
              84,
            ),
          ),
        );
      }

      return self::$reminderParams;
    }

    /**
     * function to build where clause
     *
     * @param string $scheduleReminderName name of schedule reminder
     *
     * @access public
     * return string
     */
    static function getAdditionalWhereClause( $scheduleReminderName ) {
      $additionalWhereClause = '';
      switch ( $scheduleReminderName ) {
        case 'custom_schedule_reminder_4':
        case 'custom_schedule_reminder_5':
        case 'custom_schedule_reminder_6':
        case 'custom_schedule_reminder_15':
          // greater than 10 week
          $additionalWhereClause = ' AND r.start_date > ' . strtotime( '+10 weeks' );
          break;

        case 'custom_schedule_reminder_7':
        case 'custom_schedule_reminder_8':
          // less than 10 week
          $additionalWhereClause = ' AND r.start_date < ' . strtotime( '+10 weeks' );
          break;
      }

      return $additionalWhereClause;
    }

    /**
     * function to build admin emails
     *
     * @param array  $params array of params
     *
     * @param string $context
     *
     * @access public
     */
    static function addCCToAdmin( &$params ) {
      $scheduleReminderName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_ActionSchedule', $params['entity_id'] );
      switch ( $scheduleReminderName ) {
        // add cc to email
        case 'custom_schedule_reminder_2':
        case 'custom_schedule_reminder_3':
        case 'custom_schedule_reminder_5':
        case 'custom_schedule_reminder_7':
        case 'custom_schedule_reminder_10':
          $params['cc'] = self::getAdminEmails( $params['entity_id'] );
          break;

        // replace toEmail with admin
        case 'custom_schedule_reminder_6':
        case 'custom_schedule_reminder_8':
        case 'custom_schedule_reminder_11':
          $params['toName'] = '';
          $params['toEmail'] = self::getAdminEmails( $params['entity_id'] );
          break;

        // replace toEmail with instructor
        case 'custom_schedule_reminder_12':
        case 'custom_schedule_reminder_14':
          // TODO: add code to attach CSV of participant and
          break;
      }
    }

    /**
     * function to get admin or instructor emails
     *
     * @param string
     *
     * @access public
     * @return string
     */
    static function getAdminEmails( $reminderId = NULL ) {
      $email = '';
      // get admin email id
      if ( $reminderId ) {
        $email = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_ActionSchedule', $reminderId, 'from_email' );
      }

      if ( empty( $email ) ) {
        $domainValues = CRM_Core_BAO_Domain::getNameAndEmail();
        $email = $domainValues[1];
      }

      return $email;
    }

    /**
     * This function builds the schedule reminder paramters for install
     *
     * @param string $scheduleReminderName
     *
     * @access public
     */
    static function getReminderParameters( $scheduleReminderName ) {
      $params = array();

      $params = array(
        'start_action_offset',
        'start_action_unit',
        'start_action_condition',
        'start_action_date',
        'entity_status',
        'msg_template_id',
      );
      $scheduleReminders = CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames();
      $params = array_combine( $params, $scheduleReminders[ $scheduleReminderName ][1] );
      if ( !empty( $params['msg_template_id'] ) ) {
        $messageTemplates = new CRM_Core_DAO_MessageTemplate();
        $messageTemplates->id = $params['msg_template_id'];
        if ( $messageTemplates->find( true ) ) {
          $params += array(
            'body_html' => $messageTemplates->msg_html,
            'body_text' => $messageTemplates->msg_text,
            'subject'   => $messageTemplates->msg_subject,
          );
        }
      }

      return $params;
    }

    /**
     * This function formarts the date for token value
     * July 17 2014 - July 20 2014  --  July 17-20, 2014
     * July 28 2014 - August 2 2014 --  July 28 - August 2, 2014
     * December 30 2014 - January 3 2015 --  December 30, 2014 - January 3, 2015
     *
     *
     * @param string  the
     * @param object
     * @param object
     *
     * @access public
     */
    static function getDateFormatted( $eventId, $startDate, $endDate ) {

      if ( empty( self::$eventStartEndDate[ $eventId ] ) ) {
        $start_date_modified = date( 'F d Y', strtotime( $startDate ) );
        $end_date_modified = date( 'F d Y', strtotime( $endDate ) );
        $start_date_array = explode( ' ', $start_date_modified );
        $end_date_array = explode( ' ', $end_date_modified );

        if ( $start_date_array[0] . $start_date_array[2] === $end_date_array[0] . $end_date_array[2] ) {
          $toShowDate = "$start_date_array[0] $start_date_array[1]-$end_date_array[1], $start_date_array[2]";
        } elseif ( $start_date_array[2] === $end_date_array[2] ) {
          $toShowDate = "$start_date_array[0] $start_date_array[1] - $end_date_array[0] $end_date_array[1], $start_date_array[2]";
        } else {
          $toShowDate = "$start_date_array[0] $start_date_array[1],  $start_date_array[2] - $end_date_array[0] $end_date_array[1], $end_date_array[2]";
        }
        self::$eventStartEndDate[ $eventId ] = $toShowDate;
      }

      return self::$eventStartEndDate[ $eventId ];
    }

    /**
     * This function sends mail when there is change in db values for entity
     * Participant, Contribution and GroupContact
     *
     * @param array  $sendParams
     * @param string $cc
     *
     * @access public
     */
    static function sendMail( $sendParams, $cc = false ) {
      if ( empty( $sendParams['messageTemplateID'] ) ) {
        return false;
      }

      $messageID = $sendParams['messageTemplateID'];

      $adminEmails = array(
        'registration' => array(
          //        'email' => 'jane@amga.com',
          //        'name' => 'Jane Anderson'
          'email' => 'jesse@amga.com',
          'name'  => 'Jesse Littleton',
        ),
        'membership'   => array(
          'email' => 'peter@amga.com',
          'name'  => 'Peter Schultz',
        ),
      );

      $adminMap = array(
        65 => 'registration',
        69 => 'registration',
        70 => 'registration',
        73 => 'registration',
        80 => 'registration',
        90 => 'registration',
        91 => 'registration',
        94 => 'registration',
        92 => 'registration',
        93 => 'registration',
        96 => 'registration',
      );

      $override = array_key_exists( $messageID, $adminMap );

      if ( $cc ) {
        $sendParams['cc'] = $override ? $adminEmails[ $adminMap[ $messageID ] ]['email'] : self::getAdminEmails();
      }

      if ( $override ) {
        $sendParams['from'] = "{$adminEmails[$adminMap[$messageID]]['name']} <{$adminEmails[$adminMap[$messageID]]['email']}>";
      } else {
        $domainValues = CRM_Core_BAO_Domain::getNameAndEmail();
        $sendParams['from'] = "$domainValues[0] <$domainValues[1]>";
      }

      CRM_Core_BAO_MessageTemplate::sendTemplate( $sendParams );
    }

    /**
     * This function gets the participant tokens
     *
     * @param integer $participantId Participant Id
     *
     * @access public
     * @returns array of values
     */
    static function getParticipantTokens( $participantId ) {
      $eventId = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $participantId, 'event_id' );

      $sql = "SELECT
  civicrm_event.title as title,
  civicrm_event.start_date,
  civicrm_event.end_date,
  civicrm_address.street_address as street_address,
  civicrm_address.city as city,
  civicrm_address.postal_code as postal_code,
  civicrm_state_province.abbreviation as state
FROM civicrm_event
LEFT JOIN civicrm_loc_block ON civicrm_event.loc_block_id = civicrm_loc_block.id
LEFT JOIN civicrm_address ON civicrm_loc_block.address_id = civicrm_address.id
LEFT JOIN civicrm_state_province ON civicrm_address.state_province_id = civicrm_state_province.id
WHERE civicrm_event.id = %1";
      $queryParams = array(
        1 => array(
          $eventId,
          'Integer',
        ),
      );
      $results = array();
      $dao = CRM_Core_DAO::executeQuery( $sql, $queryParams );
      if ( $dao->fetch() ) {
        $loc['street_address'] = $dao->street_address;
        $loc['city'] = $dao->city;
        $loc['state_province'] = $dao->state;
        $loc['postal_code'] = $dao->postal_code;
        $results = array(
          'event_id'       => $eventId,
          'start_date'     => $dao->start_date,
          'end_date'       => $dao->end_date,
          'title'          => $dao->title,
          'event_location' => CRM_Utils_Address::format( $loc ),
        );
      }

      // get customfields for Participant
      $params = array(
        'entity_id'    => $participantId,
        'entity_table' => 'Participant',
      );
      $result = civicrm_api3( 'custom_value', 'get', $params );
      $results[ 'custom_' . ENROLLMENT_DATE_FIELD_ID ] = CRM_Utils_Array::value( 'latest', CRM_Utils_Array::value( ENROLLMENT_DATE_FIELD_ID, $result['values'] ) );

      return $results;
    }
  }

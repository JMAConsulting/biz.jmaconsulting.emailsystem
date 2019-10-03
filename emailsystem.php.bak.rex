<?php

  require_once 'emailsystem.civix.php';
  require_once 'civicrm_constants.php';

  /**
   * Implementation of hook_civicrm_config
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
   */
  function emailsystem_civicrm_config( &$config ) {
    _emailsystem_civix_civicrm_config( $config );
  }

  /**
   * Implementation of hook_civicrm_xmlMenu
   *
   * @param $files array(string)
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
   */
  function emailsystem_civicrm_xmlMenu( &$files ) {
    _emailsystem_civix_civicrm_xmlMenu( $files );
  }

  /**
   * Implementation of hook_civicrm_install
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
   */
  function emailsystem_civicrm_install() {
    CRM_Core_DAO::executeQuery( "ALTER TABLE `civicrm_action_schedule` CHANGE `entity_value` `entity_value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'Entity value'" );

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
   * @param $op    string, the type of operation being performed; 'check' or 'enqueue'
   * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
   *
   * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
   *                for 'enqueue', returns void
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
   */
  function emailsystem_civicrm_upgrade( $op, CRM_Queue_Queue $queue = NULL ) {
    return _emailsystem_civix_civicrm_upgrade( $op, $queue );
  }

  /**
   * Implementation of hook_civicrm_managed
   *
   * Generate a list of entities to create/deactivate/delete when this module
   * is installed, disabled, uninstalled.
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
   */
  function emailsystem_civicrm_managed( &$entities ) {
    emailsystem_civicrm_actionschedule( $entities );

    return _emailsystem_civix_civicrm_managed( $entities );
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
  function emailsystem_civicrm_caseTypes( &$caseTypes ) {
    _emailsystem_civix_civicrm_caseTypes( $caseTypes );
  }

  /**
   * Implementation of hook_civicrm_alterSettingsFolders
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
   */
  function emailsystem_civicrm_alterSettingsFolders( &$metaDataFolders = NULL ) {
    _emailsystem_civix_civicrm_alterSettingsFolders( $metaDataFolders );
  }

  /**
   * Implementation of hook_civicrm_alterScheduleReminderQuery
   *
   */
  function emailsystem_civicrm_alterScheduleReminderQuery( &$queryParams, $scheduleReminder ) {
    if ( array_key_exists( $scheduleReminder->name, CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames() ) ) {
      $queryParams['dateClause'] = str_replace( 'participant_register_date', 'e.register_date', $queryParams['dateClause'], $count );
      if ( $count ) {
        $queryParams['dateClause'] .= CRM_Emailsystem_BAO_Emailsystem::getAdditionalWhereClause( $scheduleReminder->name );
      }
    }
  }

  /**
   * Implementation of hook_civicrm_buildForm to add options for start_action_date
   *
   */
  function emailsystem_civicrm_buildForm( $formName, &$form ) {
    if ( 'CRM_Admin_Form_ScheduleReminders' == $formName && $form->getVar( '_id' ) ) {
      $values = $form->getVar( '_values' );
      if ( !array_key_exists( $values['name'], CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames() ) ) {
        return NULL;
      }

      $elements = &$form->getElement( 'start_action_date' );
      $elements->_options = array_merge( $elements->_options, array(
        array(
          'text' => 'Registration Date',
          'attr' => array('value' => 'event_registration_start_date'),
        ),
        array(
          'text' => 'Enrolled date',
          'attr' => array('value' => 'participant_register_date'),
        ),
      ) );
    }
  }

  /**
   * Implementation of hook_civicrm_alterMailParams to alter CC and toEmail
   *
   */
  function emailsystem_civicrm_alterMailParams( &$params ) {
    if ( 'Scheduled Reminder Sender' == CRM_Utils_Array::value( 'groupName', $params ) ) {
      CRM_Emailsystem_BAO_Emailsystem::addCCToAdmin( $params );
    }
  }

  /**
   * Implementation of hook_civicrm_tokenValues
   *
   */
  function emailsystem_civicrm_tokenValues( &$values, $cids ) {
    if ( array_key_exists( 'event.event_id', $values ) ) {
      $values['event.start_end_date'] = CRM_Emailsystem_BAO_Emailsystem::getDateFormatted( $values['event.event_id'], $values['event.start_date'], $values['event.end_date'] );
      CRM_Core_Smarty::singleton()->assign( 'eventStartDate', $values['event.start_date'] );
    }

    $contactId = CRM_Utils_Array::value( 'contact_id', $cids );
    if ( !$contactId ) {
      $contactId = reset( $cids );
    }

    $participantId = CRM_Core_Smarty::singleton()->get_template_vars( 'participantIdToken' );
    if ( $participantId ) {

      $results = CRM_Emailsystem_BAO_Emailsystem::getParticipantTokens( $participantId );

      $value['event.start_end_date'] = CRM_Emailsystem_BAO_Emailsystem::getDateFormatted( $results['event_id'], $results['start_date'], $results['end_date'] );

      $value['custom.participant_id'] = $participantId;
      $value['custom.title'] = $results['title'];
      $value['custom.event_location'] = $results['event_location'];
      $value['custom.enrollment_date'] = CRM_Utils_Date::customFormat( $results[ 'custom_' . ENROLLMENT_DATE_FIELD_ID ], '%B %E, %Y' );
      $value['custom.enrollment_date_2_weeks'] = CRM_Utils_Date::customFormat( date( 'Y-m-d', strtotime( "+2 weeks", strtotime( $results[ 'custom_' . ENROLLMENT_DATE_FIELD_ID ] ) ) ), '%B %E, %Y' );
      $value['custom.tuition'] = civicrm_api3( 'Event', 'getvalue', array(
        'id'     => $results['event_id'],
        'return' => 'custom_' . TUITION_FIELD_ID,
      ) );

      $date = date( 'F', $results['start_date'] );
      if ( in_array( $date, array(
        'December',
        'January',
        'February',
      ) ) ) {
        $dateRange = 'October 15';
      } elseif ( in_array( $date, array(
        'March',
        'April',
        'May',
      ) ) ) {
        $dateRange = 'November 15';
      } else {
        $dateRange = 'January 15';
      }
      $value['custom.enrollment_deadline'] = $dateRange;

      $values[ $contactId ] += $value;
      CRM_Core_Smarty::singleton()->assign( 'eventStartDate', $results['start_date'] );
      CRM_Core_Smarty::singleton()->assign( 'enrollmentDate', $results[ 'custom_' . ENROLLMENT_DATE_FIELD_ID ] );
    }

    $membershipId = CRM_Core_Smarty::singleton()->get_template_vars( 'membershipIdToken' );
    if ( $membershipId ) {
      $membership_end = civicrm_api3( 'Membership', 'getvalue', array(
        'id'     => $membershipId,
        'return' => 'end_date',
      ) );
      $values[ $contactId ]['custom.membership_end_date'] = date( 'F j, Y', strtotime( $membership_end ) );
    }

    $payment_amount = CRM_Core_Smarty::singleton()->get_template_vars( 'paymentAmount' );
    if ( $payment_amount ) {
      $values[ $contactId ]['custom.payment_amount'] = '$' . number_format( $payment_amount, 2 );
    }

  }

  /**
   * Implementation of hook_civicrm_tokens
   *
   */
  function emailsystem_civicrm_tokens( &$tokens ) {
    $tokens['event'] = array(
      'event.start_end_date' => ts( 'Event Start-End Date' ),
    );
    $tokens['custom'] = array(
      'custom.title'                   => ts( 'Event Title' ),
      'custom.participant_id'          => ts( 'Participant Id' ),
      'custom.event_location'          => ts( 'Event Location' ),
      'custom.enrollment_date_2_weeks' => ts( '2 weeks From Enrollment Date' ),
      'custom.enrollment_date'         => ts( 'Enrollment Date' ),
      'custom.enrollment_deadline'     => ts( 'Enrollment Deadline' ),
      'custom.tuition'                 => ts( 'Full Tuition' ),
      'custom.membership_end_date'     => ts( 'Membership End Date' ),
      'custom.payment_amount'          => ts( 'Payment Amount' ),
    );
  }

  /**
   * function to Manage Schedule reminder's
   *
   */
  function emailsystem_civicrm_actionschedule( &$entities ) {
    $eventTypes = CRM_Core_PseudoConstant::get( 'CRM_Event_DAO_Event', 'event_type_id', array('labelColumn' => 'value') );
    $eventTypes = implode( '', $eventTypes );

    $participantRole = CRM_Core_PseudoConstant::get( 'CRM_Event_DAO_Participant', 'role_id', array('labelColumn' => 'name') );
    $participantRole = array_search( 'Instructor', $participantRole );
    foreach ( CRM_Emailsystem_BAO_Emailsystem::getScheduleReminderNames() as $name => $value ) {
      $entities[] = array(
        'module' => 'biz.jmaconsulting.emailsystem',
        'name'   => $name,
        'entity' => 'action_schedule',
        'update' => 'never',
        'params' => array_merge( array(
          'version'           => 3,
          'title'             => ts( $value[0] ),
          'name'              => $name,
          'entity_value'      => $eventTypes,
          'recipient'         => '1',
          'limit_to'          => '1',
          'recipient_listing' => ( in_array( $name, array(
            'custom_schedule_reminder_12',
            'custom_schedule_reminder_14',
          ) ) ) ? $participantRole : NULL,
          'is_repeat'         => '0',
          'is_active'         => '1',
          'record_activity'   => '1',
          'mapping_id'        => '2',
          'mode'              => 'Email',
        ), CRM_Emailsystem_BAO_Emailsystem::getReminderParameters( $name ) ),
      );
    }
  }

  /**
   * Implementation of hook_civicrm_post
   *
   */
  function emailsystem_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
    if ( $objectName == 'GroupContact' && $op == 'create' && $objectId == IFMGA_GROUP_ID ) {
      foreach ( $objectRef as $contactID ) {
        $sendParams = array(
          'messageTemplateID' => IFMGA_MESSAGE_TEMPLATE,
          'contactId'         => $contactID,
          'toEmail'           => CRM_Contact_BAO_Contact::getPrimaryEmail( $contactID ),
          'tplParams'         => array(),
        );
        CRM_Emailsystem_BAO_Emailsystem::sendMail( $sendParams, true );
      }
    }

    if ( $objectName == 'Participant' && $op == 'edit' ) {
      if ( in_array( $objectRef->status_id, array(
          PARTICIPANT_STATUS_UNDER_REVIEW,
          PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT,
          PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT2,
          PARTICIPANT_STATUS_PASS,
          ON_WAITLIST_STATUS_ID,
        ) ) ) {
        if ( !CRM_Core_Smarty::singleton()->get_template_vars( 'statusChange' ) ) {
          return false;
        }

        $messageTemplateId = NULL;
        CRM_Core_Smarty::singleton()->assign( 'participantIdToken', $objectId );
        CRM_Core_Smarty::singleton()->assign( 'statusChange', false );
        $contactID = $objectRef->contact_id;
        if ( !$contactID ) {
          $contactID = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $objectId, 'contact_id' );
        }

        if ( $objectRef->status_id == PARTICIPANT_STATUS_PASS ) {
          // Get all events contact is a participant of
          $partParams = array(
            'contact_id'            => $contactID,
            'participant_status_id' => PARTICIPANT_STATUS_PASS,
          );
          $parts = civicrm_api3( 'Participant', 'get', $partParams );
          $sendFlag = 0;
          foreach ( $parts['values'] as $key => $value ) {
            if ( in_array( $value['event_type'], array(
              'Rock Guide Exam',
              'Alpine Guide Exam',
              'Ski Guide Exam',
            ) ) ) {
              $sendFlag++;
            }
          }
          if ( $sendFlag >= 3 ) { // Should have a pass status in atleast 3 events of the above types
            $sendParams = array(
              'messageTemplateID' => PASS_MESSAGE_TEMPLATE,
              // Change this to the corresponding message template later
              'contactId'         => $contactID,
              'toEmail'           => CRM_Emailsystem_BAO_Emailsystem::getAdminEmails(),
              'tplParams'         => array(),
            );
            CRM_Emailsystem_BAO_Emailsystem::sendMail( $sendParams );
          }

          return true;
        }

        if ( in_array( $objectRef->status_id, array(
          PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT,
          PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT2,
        ) ) ) {
          $eventId = $objectRef->event_id;
          if ( !$eventId ) {
            $eventId = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $objectId, 'event_id' );
          }
          $eventStartDate = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $eventId, 'start_date' );
          $eventStartDate = strtotime( $eventStartDate );
          $tenWeeks = strtotime( '+10 weeks' );
          if ( $objectRef->status_id == PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT && $tenWeeks < $eventStartDate ) {
            $messageTemplateId = EPP_GREATER_MSG_TPL;
          } elseif ( $objectRef->status_id == PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT2 || $tenWeeks > $eventStartDate ) {
            $messageTemplateId = EPP_LESS_MSG_TPL;
          } else {
            return false;
          }
        } elseif ( $objectRef->status_id == ON_WAITLIST_STATUS_ID ) {
          $messageTemplateId = ENROLLED_ON_WAITLIST_MSG_TPL;
        } else {
          $messageTemplateId = UNDER_REVIEW_MSG_TPL;
        }

        $sendParams = array(
          'messageTemplateID' => $messageTemplateId,
          'contactId'         => $contactID,
          'toEmail'           => CRM_Contact_BAO_Contact::getPrimaryEmail( $contactID ),
          'tplParams'         => array(),
        );
        CRM_Emailsystem_BAO_Emailsystem::sendMail( $sendParams, true );


        // In addition to the UNDER_REVIEW_MSG_TPL above (on which Jane is cc'd), this was sending a second email to
        // Jane each time a user uploaded a resume.  She did not want this.
        //      if ($objectRef->status_id == PARTICIPANT_STATUS_UNDER_REVIEW) {
        //        $sendParams = array(
        //          'messageTemplateID' => UNDER_REVIEW_MSG_TPL_ADMIN,
        //          'contactId' => $contactID,
        //          'toEmail' => 'jane@amga.com',
        //          'tplParams' => array(),
        //        );
        //        CRM_Emailsystem_BAO_Emailsystem::sendMail($sendParams);
        //      }
        CRM_Core_Smarty::singleton()->assign( 'participantIdToken', '' );
      }
    }
  }

  /**
   * Implementation of hook_civicrm_pre
   *
   */
  function emailsystem_civicrm_pre( $op, $objectName, $id, &$params ) {
    if ( $objectName == 'Participant' && $op == 'edit' && in_array( CRM_Utils_Array::value( 'status_id', $params ), array(
          PARTICIPANT_STATUS_UNDER_REVIEW,
          PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT,
          PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT2,
          PARTICIPANT_STATUS_PASS,
          ON_WAITLIST_STATUS_ID,
        ) )
    ) {
      $originalStatusId = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $id, 'status_id' );
      $enrolled_statuses = array(
        PARTICIPANT_STATUS_ENROLLED,
        PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT,
        PARTICIPANT_STATUS_ENROLLED_PENDING_PAYMENT2,
      );
      if ( $originalStatusId != $params['status_id'] && !( // Ensure status change isn't between the two enrolled statuses, no need for redundant emails
          in_array( $params['status_id'], $enrolled_statuses ) && in_array( $originalStatusId, $enrolled_statuses ) )
      ) {
        CRM_Core_Smarty::singleton()->assign( 'statusChange', true );
      }
    }
  }

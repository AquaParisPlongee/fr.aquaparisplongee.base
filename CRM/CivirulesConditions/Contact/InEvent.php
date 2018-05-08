<?php
/**
 * Class for CiviRules Event participation
 *
 * @author Thomas Blein (Aqua Paris Plongee) <webmaster@aquaparisplongee.fr>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Contact_InEvent extends CRM_Civirules_Condition {

  private $conditionParams = array();

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->conditionParams = array();
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * This method returns true or false when a condition is valid or not
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   * @abstract
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $isConditionValid = false;
    $contact_id = $triggerData->getContactId();
    switch($this->conditionParams['operator']) {
      case 'in one of':
        $isConditionValid = $this->contactIsParticipantOfOneEvent($contact_id, $this->conditionParams['event_ids']);
        break;
      case 'in all of':
        $isConditionValid = $this->contactIsParticipantOfAllEvents($contact_id, $this->conditionParams['event_ids']);
        break;
      case 'not in':
        $isConditionValid = $this->contactIsNotParticipantOfEvent($contact_id, $this->conditionParams['event_ids']);
        break;
    }
    return $isConditionValid;
  }

  protected function contactIsNotParticipantOfEvent($contact_id, $event_ids) {
    $isValid = true;
    foreach($event_ids as $eid) {
      if ($this->isContactInEvent($contact_id, $eid)) {
        $isValid = false;
        break;
      }
    }
    return $isValid;
  }

  protected function contactIsParticipantOfOneEvent($contact_id, $event_ids) {
    $isValid = false;
    foreach($event_ids as $eid) {
      if ($this->isContactInEvent($contact_id, $eid)) {
        $isValid = true;
        break;
      }
    }
    return $isValid;
  }

  protected function contactIsParticipantOfAllEvents($contact_id, $event_ids) {
    $isValid = 0;
    foreach($event_ids as $eid) {
      if ($this->isContactInEvent($contact_id, $eid)) {
        $isValid++;
      }
    }
    if (count($event_ids) == $isValid && count($event_ids) > 0) {
      return true;
    }
    return false;
  }

  protected function isContactInEvent($contact_id, $eid) {
      $result = civicrm_api3('Participant', 'getcount', array(
          'contact_id' => $contact_id,
          'event_id' => $eid,
      ));
      if ($result == 0)
          return FALSE;
      else
          return TRUE;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contact_inevent/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $operators = CRM_CivirulesConditions_Contact_InEvent::getOperatorOptions();
    $operator = $this->conditionParams['operator'];
    $operatorLabel = ts('unknown');
    if (isset($operators[$operator])) {
      $operatorLabel = $operators[$operator];
    }

    $events = '';
    foreach($this->conditionParams['event_ids'] as $eid) {
      if (strlen($events)) {
        $events .= ', ';
      }
      $events .= civicrm_api3('Event', 'getvalue', array('return' => 'title', 'id' => $eid));
    }

    return $operatorLabel.' events ('.$events.')';
  }

  /**
   * Method to get operators
   *
   * @return array
   * @access protected
   */
  public static function getOperatorOptions() {
    return array(
      'in one of' => ts('In one of selected'),
      'in all of' => ts('In all selected'),
      'not in' => ts('Not in selected'),
    );
  }

}

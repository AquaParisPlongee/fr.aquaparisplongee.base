<?php
/**
 * Class for CiviRules Condition Event participant Form
 *
 * @author Thomas Blein (Aqua Paris Plongee) <webmaster@aquaparisplongee.fr>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contact_InEvent extends CRM_CivirulesConditions_Form_Form {

  /**
   * Method to get events
   *
   * @return array
   * @access protected
   */
    protected function getEvents() {
        $result = civicrm_api3('Event', 'get', array(
            'return' => array("id", "title"),
            'is_active' => 1,
        ));
        $values = array();
        foreach ($result['values'] as $event){
            $values[$event['id']] = $event['title'];
        }
        return $values;
  }

  /**
   * Method to get operators
   *
   * @return array
   * @access protected
   */
  protected function getOperators() {
    return CRM_CivirulesConditions_Contact_InEvent::getOperatorOptions();
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $event = $this->add('select', 'event_ids', ts('Events'), $this->getEvents(), true);
    $event->setMultiple(TRUE);
    $this->add('select', 'operator', ts('Operator'), $this->getOperators(), true);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleCondition->condition_params);
    if (!empty($data['event_ids'])) {
      $defaultValues['event_ids'] = $data['event_ids'];
    }
    if (!empty($data['operator'])) {
      $defaultValues['operator'] = $data['operator'];
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    $data['event_ids'] = $this->_submitValues['event_ids'];
    $data['operator'] = $this->_submitValues['operator'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}

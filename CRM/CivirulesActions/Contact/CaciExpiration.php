<?php
/**
 * Class for CiviRules Set Thank You Date for Contribution Action
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */
class CRM_CivirulesActions_Contact_CaciExpiration extends CRM_CivirulesActions_Generic_Api{

  /**
   * Method to set the api entity
   *
   * @return string
   * @access protected
   */
  protected function getApiEntity() {
    return 'Contact';
  }

  /**
   * Method to set the api action
   *
   * @return string
   * @access protected
   */
  protected function getApiAction() {
    return 'Setcaciexpiration';
  }

  /**
   * Returns an array with parameters used for processing an action
   *
   * @param array $params
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return array $params
   * @access protected
   */
  protected function alterApiParameters($params, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contact = $triggerData->getEntityData("Contact");
    $params['contact_id'] = $contact['id'];

    return $params;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a action
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleActionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }
}

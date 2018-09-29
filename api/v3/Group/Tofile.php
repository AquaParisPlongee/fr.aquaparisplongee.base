<?php
use CRM_Base_ExtensionUtil as E;

/**
 * Group.Tofile API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_group_tofile_spec(&$spec) {
  $spec['group_id'] = array(
        'title' => 'Group ID',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
  );
  $spec['list_name'] = array(
        'title' => 'List name',
        'type' => CRM_Utils_Type::T_STRING,
        'api.required' => 1,
  );
}

/**
 * Group.Tofile API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_group_tofile($params) {
  // $myfile = file_put_contents(__DIR__ . '/logs.txt', print_r($params, true).PHP_EOL , FILE_APPEND | LOCK_EX);
  require __DIR__ . '/credential.php';
  $version = CRM_Core_BAO_Domain::version();
  if (!preg_match('/[0-9]+/i', $params['group_id'])) {
    throw new API_Exception('Parameter group_id must be a unique id');
  }
  $group_id = $params['group_id'];

  if (!isset($params['list_name'])) {
    throw new API_Exception('You have to provide list_name and list_domain');
  }
  $list_name = $params['list_name'];

  // $myfile = file_put_contents(__DIR__ . '/logs.txt', 'CiviCRM group content'.PHP_EOL , FILE_APPEND | LOCK_EX);
  $group_contacts = civicrm_api3('GroupContact', 'get', array(
                                 'sequential' => 1,
                                 'group_id' => $group_id,
                                 'status' => "Added",
                                 'options' => array( 'limit' => 200,),
                                ));
  $group_email_list = array();
  if ($group_contacts['count'] > 0) {
      foreach ($group_contacts['values'] as $contact_in_group){
          $contact_params = array(array('contact_id', '=', $contact_in_group['contact_id'], 0, 0));
          list($contact, $_) = CRM_Contact_BAO_Query::apiQuery($contact_params);
          $contact = reset($contact);
          array_push($group_email_list, $contact['email']);
      }
  }

  $myfile = file_put_contents($mailinglistdir . '/' . $list_name,
                              implode(PHP_EOL, $group_email_list).PHP_EOL);

  $returnValues[$group_id] = array(
    'status_msg' => "Succesfully extract mail from group $group_id to '$list_name'",
  );


  return civicrm_api3_create_success($returnValues, $params, 'Group', 'Tofile');
}

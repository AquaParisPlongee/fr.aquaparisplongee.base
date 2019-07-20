<?php
use CRM_Base_ExtensionUtil as E;

/**
 * Contact.Removepublicphone API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_contact_removepublicphone_spec(&$spec) {
    $spec['contact_id'] = array(
        'title' => 'Contact ID',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
    );
}

/**
 * Contact.Removepublicphone API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_removepublicphone($params) {
    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Starting remove public phone".PHP_EOL, FILE_APPEND | LOCK_EX);
    if (!preg_match('/[0-9]+/i', $params['contact_id'])) {
        throw new API_Exception('Parameter contact_id must be a unique id');
    }
    $contact_id = $params['contact_id'];
    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Contact_id $contact_id".PHP_EOL, FILE_APPEND | LOCK_EX);
    $publique_phones = civicrm_api3('Phone', 'get', [
      'contact_id' => $contact_id,
      'location_type_id' => "Publique",
      'return' => ["id"]
    ]);
    if ($publique_phones["count"] > 0){
        //$myfile = file_put_contents(__DIR__ . '/logs.txt', print_r($publique_phones["values"], true).PHP_EOL , FILE_APPEND | LOCK_EX);
        foreach($publique_phones["values"] as $key => $value){
            $result = civicrm_api3('Phone', 'delete', ['id' => $key]);
            $myfile = file_put_contents(__DIR__ . '/logs.txt', "Delete phone_id $key".PHP_EOL , FILE_APPEND | LOCK_EX);
        }
        $returnValues[$contact_id] = array(
            'status_msg' => "Succesfully set primary phone as public for $contact_id",
        );
    }else{
        $returnValues[$contact_id] = array(
	    'status_msg' => "No public phone set for $contact_id",
        );
        $myfile = file_put_contents(__DIR__ . '/logs.txt', "No public phone set for $contact_id".PHP_EOL , FILE_APPEND | LOCK_EX);
    }

    return civicrm_api3_create_success($returnValues, $params, 'Contact', 'Removepublicphone');
}

<?php
use CRM_Base_ExtensionUtil as E;

/**
 * Contact.Removepublicemail API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_contact_removepublicemail_spec(&$spec) {
    $spec['contact_id'] = array(
        'title' => 'Contact ID',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
    );
}

/**
 * Contact.Removepublicemail API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_removepublicemail($params) {
    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Starting remove public email".PHP_EOL, FILE_APPEND | LOCK_EX);
    if (!preg_match('/[0-9]+/i', $params['contact_id'])) {
        throw new API_Exception('Parameter contact_id must be a unique id');
    }
    $contact_id = $params['contact_id'];
    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Contact_id $contact_id".PHP_EOL, FILE_APPEND | LOCK_EX);
    $publique_emails = civicrm_api3('Email', 'get', [
      'contact_id' => $contact_id,
      'location_type_id' => "Publique",
      'return' => ["id"]
    ]);
    if ($publique_emails["count"] > 0){
        //$myfile = file_put_contents(__DIR__ . '/logs.txt', print_r($publique_emails["values"], true).PHP_EOL , FILE_APPEND | LOCK_EX);
        foreach($publique_emails["values"] as $key => $value){
            $result = civicrm_api3('Email', 'delete', ['id' => $key]);
            $myfile = file_put_contents(__DIR__ . '/logs.txt', "Delete email_id $key".PHP_EOL , FILE_APPEND | LOCK_EX);
        }
        $returnValues[$contact_id] = array(
            'status_msg' => "Succesfully set primary email as public for $contact_id",
        );
    }else{
        $returnValues[$contact_id] = array(
	    'status_msg' => "No public email set for $contact_id",
        );
        $myfile = file_put_contents(__DIR__ . '/logs.txt', "No public email set for $contact_id".PHP_EOL , FILE_APPEND | LOCK_EX);
    }

    return civicrm_api3_create_success($returnValues, $params, 'Contact', 'Removepublicemail');
}

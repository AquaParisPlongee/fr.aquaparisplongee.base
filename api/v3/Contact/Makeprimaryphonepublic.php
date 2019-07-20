<?php
use CRM_Base_ExtensionUtil as E;

/**
 * Contact.Makeprimaryphonepublic API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_contact_makeprimaryphonepublic_spec(&$spec) {
    $spec['contact_id'] = array(
        'title' => 'Contact ID',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
    );
}

/**
 * Contact.Makeprimaryphonepublic API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_makeprimaryphonepublic($params) {
    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Starting Make primary phone public".PHP_EOL, FILE_APPEND | LOCK_EX);
    if (!preg_match('/[0-9]+/i', $params['contact_id'])) {
        throw new API_Exception('Parameter contact_id must be a unique id');
    }
    $contact_id = $params['contact_id'];
    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Contact_id $contact_id".PHP_EOL, FILE_APPEND | LOCK_EX);
    $primary_phone = civicrm_api3('Phone', 'get', [
	  'sequential' => 1,
	  'contact_id' => $contact_id,
	  'is_primary' => 1,
          'return' => ["phone"],
	])["values"][0]["phone"];
    $publique_phone_exist = civicrm_api3('Phone', 'getcount', [
      'contact_id' => $contact_id,
      'location_type_id' => "Publique",
    ]);
    if ($publique_phone_exist == 0){
        $myfile = file_put_contents(__DIR__ . '/logs.txt', "New public phone".PHP_EOL , FILE_APPEND | LOCK_EX);
        $result = civicrm_api3('Phone', 'create', [
             'contact_id' => $contact_id,
             'phone' => $primary_phone,
             'location_type_id' => "Publique",
        ]);
    }else{
        $myfile = file_put_contents(__DIR__ . '/logs.txt', "Replace public phone".PHP_EOL , FILE_APPEND | LOCK_EX);
        $result = civicrm_api3('Phone', 'replace', [
             'contact_id' => $contact_id,
             'location_type_id' => "Publique",
             'values' => ['0' => ['phone' => $primary_phone]],
        ]);
    }
    //$myfile = file_put_contents(__DIR__ . '/logs.txt', "Result: ".print_r($result, true).PHP_EOL, FILE_APPEND | LOCK_EX);
    $returnValues[$contact_id] = array(
        'status_msg' => "Succesfully set primary phone as public for $contact_id",
    );

    $myfile = file_put_contents(__DIR__ . '/logs.txt', "Succesfully set primary phone as public for $contact_id".PHP_EOL , FILE_APPEND | LOCK_EX);

    return civicrm_api3_create_success($returnValues, $params, 'Contact', 'Makeprimaryphonepublic');
}

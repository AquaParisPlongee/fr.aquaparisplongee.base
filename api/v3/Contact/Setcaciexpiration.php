<?php
use CRM_Base_ExtensionUtil as E;

/**
 * Contact.Setcaciexpiration API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_contact_setcaciexpiration_spec(&$spec) {
    $spec['contact_id'] = array(
        'title' => 'Contact ID',
        'type' => CRM_Utils_Type::T_INT,
        'api.required' => 1,
    );
}

/**
 * Contact.Setcaciexpiration API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_setcaciexpiration($params) {
    require __DIR__ . '/caci_fields.php';
    if (!preg_match('/[0-9]+/i', $params['contact_id'])) {
        throw new API_Exception('Parameter contact_id must be a unique id');
    }
    $contact_id = $params['contact_id'];
    $date_certificat = civicrm_api3(
        'Contact', 'getvalue', [
            'return' => $caci_date_field,
            'id' => $contact_id,
        ]);
    $date_certificat = strtotime($date_certificat);
    $date_expiration = date("Y-m-d H:i:s", strtotime('+1 year', $date_certificat));

    // $myfile = file_put_contents(__DIR__ . '/logs.txt', date("Y-m-d H:i:s").PHP_EOL , FILE_APPEND | LOCK_EX);
    // $myfile = file_put_contents(__DIR__ . '/logs.txt', "Starting CACI update for contact_id $contact_id".PHP_EOL , FILE_APPEND | LOCK_EX);
    // $myfile = file_put_contents(__DIR__ . '/logs.txt', $caci_date_field.PHP_EOL , FILE_APPEND | LOCK_EX);
    // $myfile = file_put_contents(__DIR__ . '/logs.txt', $caci_exp_date_field.PHP_EOL , FILE_APPEND | LOCK_EX);

    $result = civicrm_api3(
        'Contact',
        'create', 
        array(
            'id' => $contact_id,
            $caci_exp_date_field => $date_expiration
        )
    );

    $returnValues[$contact_id] = array(
        'status_msg' => "Succesfully set CACI expiration date of $contact_id to '$date_expiration'",
    );

    // $myfile = file_put_contents(__DIR__ . '/logs.txt', "Succesfully set CACI expiration date of $contact_id to '$date_expiration'".PHP_EOL , FILE_APPEND | LOCK_EX);

    return civicrm_api3_create_success($returnValues, $params, 'Contact', 'Setcaciexpiration');
}

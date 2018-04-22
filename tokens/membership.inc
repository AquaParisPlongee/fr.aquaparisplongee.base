<?php
// $myfile = file_put_contents(__DIR__ . '/logs.txt', 'loading script'.PHP_EOL , FILE_APPEND | LOCK_EX);

/**
 * @param $token
 * @return array
 */
function membership_civitoken_declare($token){
    // $myfile = file_put_contents(__DIR__ . '/logs.txt', 'loading declare'.PHP_EOL , FILE_APPEND | LOCK_EX);
    $result = civicrm_api3('MembershipType', 'get',
                            array('sequential' => 1,
                                  'return' => array("member_of_contact_id")
                                  ));
    $Organisms = array();

    foreach($result['values'] as $membership_type) {
            $Organisms[] = $membership_type['member_of_contact_id'];
    }
    $Organisms = array_unique($Organisms);
    $token_list = array(
                        $token. '.typename' => 'Tous les types de membre',
                      );
    foreach($Organisms as $Organism_id){
        $Organism = civicrm_api3('Contact', 'getsingle',
            array('return' => array("display_name"),
            'id' => $Organism_id,)
        );
        $token_list[$token.'.typename_' . $Organism_id] = 'Type de membre chez ' . $Organism['display_name'];
    }
    // $myfile = file_put_contents(__DIR__ . '/logs.txt', print_r($token_list, TRUE).PHP_EOL , FILE_APPEND | LOCK_EX);
  return $token_list;
}

/**
 * @param $cid
 * @param $value
 */
function membership_civitoken_get($cid, &$value){
  // $myfile = file_put_contents(__DIR__ . '/logs.txt', 'loading get'.PHP_EOL , FILE_APPEND | LOCK_EX);
  try {
    $membership_list = civicrm_api3('Membership', 'get', array('contact_id' => $cid, 'active_only' => 1));
    $all_membership = array();
    foreach ($membership_list['values'] as $membership){
        $membership_desc = civicrm_api3('MembershipType', 'getsingle', array( 'id' => $membership['membership_type_id'],));
        $all_membership[] = $membership['membership_name'];
        $value['membership.typename_'. $membership_desc['member_of_contact_id']] = $membership['membership_name'];
    }
    $value['membership.typename'] = join(", ", $all_membership);
  }
  catch (CiviCRM_API3_Exception $e) {
    $value['membership.typename'] = '';
  }


}

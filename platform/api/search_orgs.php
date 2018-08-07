<?php
// This API endpoint will return the users existing information.

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';

// get the POST vars
$search_term = "%{$_POST['search_term']}%";

// make spaces %
$search_term = preg_replace("/ /","%",$search_term);

if (isset($_POST['search_term'])){
    // let's check it
    if ($stmt = $mysqli->prepare("SELECT o_id, o_name, o_username FROM org WHERE o_name LIKE ? ORDER BY o_name LIMIT 40")) {
        $stmt->bind_param('s', $search_term);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($o_id, $o_name, $o_username);
        //$stmt->fetch();

        $html = '<select id="select_org_result" name="select_org_result" class="form-control full-input-width">';

        while ($stmt->fetch()) {
            $html .= '<option value="' . $o_id . '">' . $o_name . ' (' . $o_username . ')</option>';
        }

        $html .= '</select>';

        $response = array(
            'status' => 'success',
            'html' => $html
        );

        // send the response
        echo json_encode($response);
    }
} else {
    // POST vars not provided
    $response = array(
        'status' => 'failure',
        'fail_code' => '0',
        'reason' => 'Unable to lookup organizations.'
    );

    // print out response to stdout
    echo json_encode($response);
}
?>

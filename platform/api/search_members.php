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
    if ($stmt = $mysqli->prepare("SELECT m_id, m_full_name, m_username FROM member WHERE m_full_name LIKE ? AND m_2017 = 1 ORDER BY m_full_name LIMIT 40")) {
        $stmt->bind_param('s', $search_term);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_id, $m_full_name, $m_username);
        //$stmt->fetch();

        $html = '<select id="select_result" name="select_result" class="form-control full-input-width">';

        while ($stmt->fetch()) {
            $html .= '<option value="' . $m_id . '">' . $m_full_name . ' (' . $m_username . ')</option>';
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
        'reason' => 'Unable to lookup members.'
    );

    // print out response to stdout
    echo json_encode($response);
}
?>

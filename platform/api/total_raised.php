<?php
  // Load database connection and php functions
  include_once '../includes/db_connect.php';
  include_once '../includes/functions.php';
  // Start secure session
  sec_session_start();

$total_raised = "";

if ($stmt = $mysqli->prepare("SELECT sum(d_amount) FROM donation")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($total_raised);
  $stmt->fetch();
  $stmt->close();

} else {
  // unable to get data
  $total_raised = 0;
}

$outputSpeech = array(
    'type' => 'PlainText',
    'text' => 'This year, No Shave November has raised ' . $total_raised . ' dollars.'
);

$card = array(
    'type' => 'Simple',
    'title' => 'No Shave November Donation Total',
    'content' => 'This year, No Shave November has raised ' . $total_raised . ' dollars.'
);

$response = array(
    'outputSpeech' => $outputSpeech,
    'card' => $card,
    'shouldEndSession' => true
);


$full_response = array(
    'version' => '1.0',
    'response' => $response
);

$alexa_answer = '{"version":"1.0","response":{"outputSpeech":{"type":"PlainText","text":"This year, No Shave November has raised ' . $total_raised . ' dollars."},"card":{"type":"Simple","title":"No-Shave November Total Raised","content":"This year, No Shave November has raised ' . $total_raised . ' dollars."},"shouldEndSession":true}}';

header('Content-type:application/json');
echo json_encode($full_response);

?>

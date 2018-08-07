<?php
  // Load database connection and php functions
  include_once '../includes/db_connect.php';
  include_once '../includes/functions.php';
  // Start secure session
  sec_session_start();
?>

<?php

// This may have to be offboarded to a cronjob...

// vars
$member_table = "";
$team_table = "";
$org_table = "";
$member_count = 0;
$team_count = 0;
$org_count = 0;

$total_raised = "";
$total_members = "";
$total_teams = "";
$total_orgs = "";

// Overall totals
// SELECT sum(d_amount) FROM donation;
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

// SELECT count(m_id) FROM member WHERE m_2017 != 0;
if ($stmt = $mysqli->prepare("SELECT count(m_id) FROM member WHERE m_2017 != 0")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($total_members);
  $stmt->fetch();
  $stmt->close();

} else {
  // unable to get data
  $total_members = 0;
}

// SELECT count(t_id) FROM team;
if ($stmt = $mysqli->prepare("SELECT count(t_id) FROM team, member WHERE member.m_team_id = team.t_id and member.m_team_editor = 1 and member.m_2017 = 1")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($total_teams);
  $stmt->fetch();
  $stmt->close();

} else {
  // unable to get data
  $total_teams = 0;
}

//SELECT count(o_id) FROM org;
if ($stmt = $mysqli->prepare("SELECT count(o_id) FROM org,member WHERE member.m_org_id = org.o_id and member.m_org_editor = 1 and member.m_2017 = 1")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($total_orgs);
  $stmt->fetch();
  $stmt->close();

} else {
  // unable to get data
  $total_orgs = 0;
}

// MEMBERS
// SELECT m_username, m_full_name, sum(d_amount) FROM donation, member WHERE m_id = d_classifier_id GROUP BY d_classifier_id ORDER BY sum(d_amount) DESC LIMIT 10;
if ($stmt = $mysqli->prepare("SELECT m_username, m_full_name, m_profile_pic, sum(d_amount) FROM donation, member WHERE m_id = d_classifier_id GROUP BY d_classifier_id ORDER BY sum(d_amount) DESC LIMIT 10")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($m_username, $m_full_name, $m_profile_pic, $m_total_raised);

  while ($stmt->fetch()) {

    $member_count += 1;

    $member_table .= '
                      <tr>
                        <td class="col-md-1">
                          <h4>' . $member_count . '</h4>
                        </td>
                        <td class="vert-align col-md-1">
                          <img class="img-rounded" height="42" width="42" src="' . $m_profile_pic . '">
                        </td>
                        <td class="vert-align col-md-7">
                          <h4><a href="/member/' . $m_username . '">' . $m_full_name . '</a></h4>
                        </td>
                        <td class="vert-align col-md-3">
                          <h3 class="donation-green">$' . number_format($m_total_raised) . '</h3>
                        </td>
                      </tr>
                    ';

  }

} else {
  // unable to get data
  $member_table = "Something went wrong.";
}

// TEAMS
// SELECT t_name, t_username, sum(d_amount) FROM (SELECT DISTINCT d_id, t_id, t_name, t_username, d_amount FROM team t INNER JOIN member m ON t.t_id = m.m_team_id INNER JOIN donation d ON d.d_classifier_id = t.t_id OR d.d_classifier_id = m.m_id) as foo GROUP BY t_id ORDER BY sum(d_amount) DESC LIMIT 10;
if ($stmt = $mysqli->prepare("SELECT t_name, t_username, sum(d_amount) FROM (SELECT DISTINCT d_id, t_id, t_name, t_username, d_amount FROM team t INNER JOIN member m ON t.t_id = m.m_team_id INNER JOIN donation d ON d.d_classifier_id = t.t_id OR d.d_classifier_id = m.m_id) as foo GROUP BY t_id ORDER BY sum(d_amount) DESC LIMIT 10")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($t_name, $t_username, $t_total_raised);

  while ($stmt->fetch()) {

    $team_count += 1;

    $team_table .= '
                      <tr>
                        <td class="col-md-1">
                          <h4>' . $team_count . '</h4>
                        </td>
                        <td class="vert-align col-md-8">
                          <h4><a href="/team/' . $t_username . '">' . $t_name . '</a></h4>
                        </td>
                        <td class="vert-align col-md-3">
                          <h3 class="donation-green">$' . number_format($t_total_raised) . '</h3>
                        </td>
                      </tr>
                    ';

  }

} else {
  // unable to get data
  $team_table = "Something went wrong.";
}

// ORGS
// SELECT o_name, o_username, sum(d_amount) FROM (SELECT DISTINCT d_id, o_id, o_name, o_username, d_amount FROM org o INNER JOIN member m ON o.o_id = m.m_org_id INNER JOIN team t ON o.o_id = t.t_org_id INNER JOIN donation d ON d.d_classifier_id = o.o_id OR d.d_classifier_id = m.m_id OR d.d_classifier_id = t.t_id) as foo GROUP BY o_id ORDER BY sum(d_amount) DESC LIMIT 10;
if ($stmt = $mysqli->prepare("SELECT o_name, o_username, sum(d_amount) FROM (SELECT DISTINCT d_id, o_id, o_name, o_username, d_amount FROM org o INNER JOIN member m ON o.o_id = m.m_org_id LEFT JOIN team t ON o.o_id = t.t_org_id INNER JOIN donation d ON d.d_classifier_id = o.o_id OR d.d_classifier_id = m.m_id OR d.d_classifier_id = t.t_id) as foo GROUP BY o_id ORDER BY sum(d_amount) DESC LIMIT 10")) {
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($o_name, $o_username, $o_total_raised);

  while ($stmt->fetch()) {

    $org_count += 1;

    $org_table .= '
                      <tr>
                        <td class="col-md-1">
                          <h4>' . $org_count . '</h4>
                        </td>
                        <td class="vert-align col-md-8">
                          <h4><a href="/org/' . $o_username . '">' . $o_name . '</a></h4>
                        </td>
                        <td class="vert-align col-md-3">
                          <h3 class="donation-green">$' . number_format($o_total_raised) . '</h3>
                        </td>
                      </tr>
                    ';

  }

} else {
  // unable to get data
  $org_table = "Something went wrong.";
}

date_default_timezone_set('America/New_York');
$date= date('Y-m-d H:i:s');

// let's grab what we can based on the email
if ($stmt = $mysqli->prepare("UPDATE leaderboard SET total_raised = ?, total_members = ?, total_teams = ?, total_orgs = ?, top_members = ?, top_teams = ?, top_orgs = ? WHERE id = 1")) {
    $stmt->bind_param('iiiisss', $total_raised, $total_members, $total_teams, $total_orgs, $member_table, $team_table, $org_table);
    
    // execute the insert
    if (!$stmt->execute()) {
      // it failed
      echo $date . ": Failed to update";
    } else {
      // it succeeded
      echo $date . ": Updated successful";
    }
}

?>
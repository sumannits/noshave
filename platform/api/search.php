
<?php
// This API endpoint will return the users existing information.

// includes necessary to make the db call
include_once '../includes/db_connect.php';
include_once '../includes/psl-config.php';

// vars
$results = "";
$member_results = "";
$team_results = "";
$org_results = "";

// get the POST vars
$q = "%{$_POST['q']}%";

// make spaces %
$q = preg_replace("/ /","%",$q);

$results = '
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#member_results" aria-controls="member_results" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Members</a></li>
                <li role="presentation" class="visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#team_results" aria-controls="team_results" role="tab" data-toggle="tab"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Teams</a></li>
                <li role="presentation" class="visible-lg visible-md visible-sm hidden-xs"><a class="black-link" href="#org_results" aria-controls="org_results" role="tab" data-toggle="tab"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Organizations</a></li>

                <li role="presentation" class="active hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#member_results" aria-controls="member_results" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                <!--<li role="presentation" class="hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#team_results" aria-controls="team_results" role="tab" data-toggle="tab"><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Teams</a></li>
                <li role="presentation" class="hidden-lg hidden-md hidden-sm visible-xs"><a class="black-link" href="#org_results" aria-controls="org_results" role="tab" data-toggle="tab"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Orgs</a></li>-->
              </ul>
            ';

if (isset($_POST['q'])){

    // MEMBERS
    if ($stmt = $mysqli->prepare("SELECT m_full_name, m_username FROM member WHERE m_2017 = 1 AND (m_full_name LIKE ? OR m_username LIKE ?) ORDER BY m_full_name LIMIT 30")) {
        $stmt->bind_param('ss', $q, $q);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($m_full_name, $m_username);
        //$stmt->fetch();

        $member_results = '
                          <!-- Tab panes -->
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="member_results">
                              <br>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th class="col-md-12"><h4><i class="fa fa-user" aria-hidden="true"></i>&nbsp; Members</h4></th>
                                  </tr>
                                </thead>
                                <tbody>
                            ';

        while ($stmt->fetch()) {
            $member_results .= '
                                  <tr>
                                    <td class="vert-align col-md-12">
                                      <h4><a href="/member/' . $m_username . '">' . $m_full_name . '</a></h4>
                                    </td>
                                  </tr>
                                ';
        }

        $member_results .= '
                                </tbody>
                              </table>
                            </div>
                            ';

    }

    // TEAMS
    if ($stmt = $mysqli->prepare("SELECT t_name, t_username FROM team WHERE t_name LIKE ? OR t_username LIKE ? ORDER BY t_name LIMIT 30")) {
        $stmt->bind_param('ss', $q, $q);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($t_name, $t_username);
        //$stmt->fetch();

        $team_results = '
                            <div role="tabpanel" class="tab-pane fade in" id="team_results">
                              <br>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th class="col-md-12"><h4><i class="fa fa-users" aria-hidden="true"></i>&nbsp; Teams</h4></th>
                                  </tr>
                                </thead>
                                <tbody>
                            ';

        while ($stmt->fetch()) {
            $team_results .= '
                                  <tr>
                                    <td class="vert-align col-md-12">
                                      <h4><a href="'.base_url.'/team/' . $t_username . '">' . $t_name . '</a></h4>
                                    </td>
                                  </tr>
                                ';
        }

        $team_results .= '
                                </tbody>
                              </table>
                            </div>

                            ';

    }
   
    // ORGS
    if ($stmt = $mysqli->prepare("SELECT o_name, o_username FROM org WHERE o_name LIKE ? OR o_username LIKE ? ORDER BY o_name LIMIT 30")) {
        $stmt->bind_param('ss', $q, $q);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($o_name, $o_username);
        //$stmt->fetch();

        $org_results = '
                            <div role="tabpanel" class="tab-pane fade in" id="org_results">
                              <br>
                              <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th class="col-md-12"><h4><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp; Organizations</h4></th>
                                  </tr>
                                </thead>
                                <tbody>
                            ';

        while ($stmt->fetch()) {
            $org_results .= '
                                  <tr>
                                    <td class="vert-align col-md-12">
                                      <h4><a href="/org/' . $o_username . '">' . $o_name . '</a></h4>
                                    </td>
                                  </tr>
                                ';
        }

        $org_results .= '
                                </tbody>
                              </table>
                            </div>
                          </div>
                            ';

    }

    // add them altogether
    $results .= $member_results . $team_results . $org_results;

    // RESPONSE
    $response = array(
        'status' => 'success',
        'results' => $results
    );

    // send the response
    echo json_encode($response);


} else {
    // POST vars not provided
    $response = array(
        'status' => 'failure',
        'fail_code' => '0',
        'reason' => 'Unable to search members, teams and organizations.'
    );

    // print out response to stdout
    echo json_encode($response);
}

?>
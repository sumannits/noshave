<?php
// SLOW DEPRECATED DATABASE  CONNECTION
include_once 'psl-config.php';
include_once 'db_connect.php';
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // set 45 day lifetime... 3888000 seconds
    //$lifetime = 3888000;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    if (!session_id()){
        session_start();                // Start the PHP session 
    }
    session_regenerate_id(true);    // regenerated the session, delete the old one. 
}

function login($email_or_username, $password, $mysqli) {
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT m_id, m_password, m_2017 FROM member WHERE m_email = ? OR m_username = ? LIMIT 1")) {
        $stmt->bind_param('ss', $email_or_username, $email_or_username);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($m_id, $m_password, $m_2017);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            // Check if the password in the database matches
            // the password the user submitted.
            if (password_verify($password, $m_password)) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT'];
                // XSS protection as we might print this value
                $user_id = preg_replace("/[^0-9]+/", "", $m_id);
                $_SESSION['user_id'] = $m_id;
                $_SESSION['login_string'] = hash('sha512', $m_password . $user_browser);

                // set empty
                $error_msg = "";

                // check if registered so we can send them to register
                if ($m_2017 == 0) {
                    //$error_msg = 0;
                    header('Location: /register?e=1');
                } else {
                    // nothing
                    $error_msg = 1;
                }

                // Login successful
                return $error_msg;
            } else {
                // Password is not correct
                // We record this attempt in the database
                $error_msg = '<strong>Uh oh!</strong>  Incorrect email or password entered.  Please try again or reset your password. ';
                return $error_msg;
            }
        } else {
        // No user exists.
        $error_msg = '<strong>Uh oh!</strong>  Incorrect email or password entered.  Please try again or reset your password. ';
        return $error_msg;
        }
    }
}

function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['login_string'])) {

        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        //$username = $_SESSION['username'];

        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT m_password FROM member WHERE m_id = ? LIMIT 1")) {
                // Bind "$user_id" to parameter. 
                $stmt->bind_param('i', $user_id);
                $stmt->execute();   // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // If the user exists get variables from result.
                    $stmt->bind_result($m_password);
                    $stmt->fetch();
                    $login_check = hash('sha512', $m_password . $user_browser);

                    if ($login_check == $login_string) {
                        // Logged In!!!! 
                        return true;
                    } else {
                        // Not logged in 
                        return false;
                    }
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }

    } else {
        // Not logged in 
        return false;
    }
}

function team_owner($user_id, $mysqli) {

    if (isset($user_id)) {

        $user_id = $_SESSION['user_id'];

         if ($stmt = $mysqli->prepare("SELECT m_team_editor FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($m_team_editor);
                $stmt->fetch();

                if ($m_team_editor == 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function team_member($user_id, $mysqli) {

    if (isset($user_id)) {

        $user_id = $_SESSION['user_id'];

         if ($stmt = $mysqli->prepare("SELECT m_team_id FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($m_team_id);
                $stmt->fetch();

                if ($m_team_id != 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function is_team_owner($user_id, $mysqli) {
    if (isset($user_id)) {
         if ($stmt = $mysqli->prepare("SELECT m_team_editor FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($m_team_editor);
                $stmt->fetch();
                if ($m_team_editor == 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function is_team_member($user_id, $mysqli) {
    if (isset($user_id)) {
         if ($stmt = $mysqli->prepare("SELECT m_team_id FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($m_team_id);
                $stmt->fetch();
                if ($m_team_id != 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function org_owner($user_id, $mysqli) {

    if (isset($user_id)) {

        $user_id = $_SESSION['user_id'];

         if ($stmt = $mysqli->prepare("SELECT m_org_editor FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($m_org_editor);
                $stmt->fetch();

                if ($m_org_editor == 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function org_member($user_id, $mysqli) {

    if (isset($user_id)) {

        $user_id = $_SESSION['user_id'];

         if ($stmt = $mysqli->prepare("SELECT m_org_id FROM member WHERE m_id = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($m_org_id);
                $stmt->fetch();

                if ($m_org_id != 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// function owns_team($mysqli) { 
//     if (isset($_SESSION['user_id'])) {

//     $user_id = $_SESSION['user_id'];

//     if ($stmt = $mysqli->prepare("SELECT nsn_is_captain FROM member WHERE nsn_uid = ? LIMIT 1")) {
//         $stmt->bind_param('i', $user_id);
//             $stmt->execute();
//             $stmt->store_result();

//             if ($stmt->num_rows == 1) {
//                 $stmt->bind_result($nsn_is_captain);
//                 $stmt->fetch();

//                 if ($nsn_is_captain == 1) {
//                     // CAPTAIN 
//                     return true;
//                 } else {
//                     // Not captain
//                     return false;
//                 }
//             } else {
//                 // Not captain
//                 return false;
//             }
//         } else {
//             // Not captain
//             return false;
//         }
//     } else {
//         // Not captain
//         return false;
//     }
// }

function esc_url($url) {

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

// opposite of nl2br I suppose
function br2nl($string){
    RETURN PREG_REPLACE('#<br\s*?/?>#i', "\n", $string); 
}
<?php
//Checkout API

require_once "checkoutconfig.php";
$lastver = file_get_contents("/var/checkout/lastversion.php");        //Get previous file version

$message = 'Last ver = '.$lastver;
if (array_key_exists('version', $_POST) && array_key_exists('password', $_POST)) {
  $ver = $_POST['version'];
  $pas = $_POST['password'];

  if($checkoutPassword != $pas) {
    $message = "Wrong password";
  } else if (!valid_git_branch($ver)) {
    $message = "Invalid branch name";
  } else {
    $message  = 'Last ver = '.$lastver.'<br />';
    shell_exec('eval `ssh-agent` 2>&1; ssh-add '.$githubKey.' 2>&1; cd '.$projectGithubDirectory.' 2>&1; git fetch github '.$ver.':'.$ver.' -v 2>&1; git --work-tree='.$projectWorkingDirectory.' checkout -f '.$ver.' 2>&1');
    $file = fwrite(fopen("/var/checkout/lastversion.php", "w"), $ver);
    fclose($file);
  }
}

// function fetch($ver,$redis){ 
// //  $chmod    = "chmod 777 -R /var/www/";
//   $agent    = "eval `ssh-agent` 2>&1";
//   $key      = "ssh-add /etc/befittd/befittd_github_key 2>&1";
//   $fetch    = 'cd /home/ubuntu/befittd/ 2>&1 ; echo "folder changed"; git fetch github '.$ver.':'.$ver.' -v 2>&1';
//   $checkout = 'git --work-tree=/var/befittd/ checkout -f '.$ver.' 2>&1';
//   $command  = $agent.' ; echo "did agent command"; '.$key.' ; echo "did key command"; '.$fetch.' ; echo "did fetch command"; '.$checkout. '; echo "did checkout command";';
//   $output   = shell_exec($command); 
  

  // $redis->set("API:checkout:ver",$ver);
//   return "exec command: ".$command.'</br>Output: '
//             .nl2br($output).'<br />';
// }

/*
  Checks that the branch name passed contains only ASCII letters, numbers, hyphens, underscores, and single dots, and
  that it does not start with a dot.
  
  This is a quick and dirty hack to protect us from shell injections; probably all I really need is to blacklist
  semicolons and newlines, but I'm not sure and I'm paranoid so instead I'm taking a whitelist approach.
*/
function valid_git_branch($branch_name) {
  $allowed_charset = array(
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 
    'w', 'x', 'y', 'z',
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
    'W', 'X', 'Y', 'Z',
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    '-', '_', '.'
  );
  
  $branch_name_with_valid_chars_removed = str_replace($allowed_charset, '', $branch_name);
  if (strlen($branch_name_with_valid_chars_removed) > 0) {
    return false; // There are some invalid chars in the name
  }
  
  if (strpos($branch_name, '..') !== false) {
    return false; // The branch name contained a double dot, which is not legal in a git branch name
  }
  
  if (substr($branch_name, 0, 1) === '.') {
    return false; // The branch name starts with a dot, which is not legal in a git branch name
  }
  
  return true; // All seems to be well.
}


?>
 


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
    
<head>
  <meta charset="utf-8" />
  <title>Befittd checkout API</title>
  <link rel="stylesheet" type="text/css" href="/media/css/bootstrap.css"/>
  <link rel="stylesheet" type="text/css" href="/media/css/datepicker.css" />
  <link rel="stylesheet" type="text/css" href="/media/css/style.css" />
  <link rel="stylesheet" type="text/css" href="/media/css/restorepass.css" />
  <script type="text/javascript" src="/media/js/libs/jquery-1.8.3.min.js"></script>
  <script type="text/javascript" src="/media/js/libs/animate.js"></script>
  <script type="text/javascript" src="/media/js/libs/bootstrap.min.js"></script>
</head>  
   
<body>
  <h3>Befittd checkout API</h3>
  <div id="password-reset-box">
    <div id="passwords-div">
      <h4>Please enter password and Branch version:</h4>
      <br />
      <form id="passwords-form" action="" method="POST">
        <div class="restore-page">
          <input type="password" id="password" name="password" placeholder="Password"
            class="input-large close-join" />
          <div class="start-hidden alert pozRestore" id="resto-pass">
            <strong><span>Invalid password</span></strong> 
          </div>
          <br/>
        </div>
        <div class="restore-page">
          <input type="text" id="version" name="version" placeholder="Branch version"
            title="Please retype your password" class="input-large close-join"/>
          <div class="start-hidden alert pozRestore" id="resto-repass">
            <strong><span>Invalid passwords</span></strong>
          </div>
        </div>
        <button class="btn btn-primary" name="submit" data-dismiss="modal" id="checkout" type="submit">Checkout</button>
      </form>
      <br/>
    </div>
    <div class="start-hidden" id="done-alert">
      <div class="alert">
        <strong><span id="done-msg"></span></strong>
      </div>
    </div>
  </div>
<?php  
//}else{
?>
 <div id="password-reset-box">
   <h4>Response message:</h4>
   <br/>
   <pre><?=$message?></pre>
 </div>
<?php  

?>
</body>
</html>
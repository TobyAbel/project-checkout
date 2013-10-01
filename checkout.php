<?php
//Checkout API
// Test

require_once "checkoutconfig.php";

if(!file_exists($projectGithubDirectory)){
  mkdir($projectGithubDirectory);
}
if(!file_exists($projectWorkingDirectory)){
  mkdir($projectWorkingDirectory);
}

$commands = '';
$commands .= "sudo chmod 777 $projectWorkingDirectory;";
  if (file_exists($lastVersionFile)) {
    $lastver = file_get_contents($lastVersionFile); // Get previous file version
  } else {
    $lastver = 0;
  }

$message = "Last ver = $lastver";
if (isset($_POST['version']) && isset($_POST['password'])) {
  $ver = $_POST['version'];
  $pas = $_POST['password'];

  if ($checkoutPassword == $pas) {
    // if $projectGithubDirectory does not contain a git repo
      // make one.
    if (!file_exists($projectGithubDirectory.'HEAD')) {// It's a silly hack but it doesn't need to be resilient
      $commands .= "cd $projectGithubDirectory 2>&1; eval `ssh-agent` 2>&1; ssh-add $githubKey 2>&1; git clone --bare $github ./ 2>&1; git remote add github-remote $github 2>&1;";
      $output = shell_exec($commands);
      $message .= $commands.PHP_EOL.$output.PHP_EOL;
      $commands = '';
    }
    if (valid_git_branch($ver)) {
      $message  .= 'Last ver = '.$lastver.'<br />';
      $commands .= "cd $projectGithubDirectory 2>&1; eval `ssh-agent`; ssh-add $githubKey 2>&1; git fetch github-remote $ver:$ver 2>&1; git --work-tree=$projectWorkingDirectory checkout -f $ver 2>&1";
      $output = shell_exec($commands);
      $message .= str_replace(';', ';'.PHP_EOL, $commands).PHP_EOL;
      $message .= $output;

    if (!file_exists($lastVersionFile)) {
      shell_exec("touch $lastVersionFile 2>&1");
      file_put_contents($lastVersionFile, $lastver);
    }
    
      $file = fopen($lastVersionFile, "w");
      fwrite($file, $ver);
      fclose($file);
    } else {
      $message .= 'invalid git branch: '.$ver;
    }

    }

     else {
      $message .= "Wrong password";
    }
  }


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
  <title>Server Checkout API</title>
</head>  
   
<body>
  <h3>Server Checkout API</h3>
  <div id="password-reset-box">
    <div id="passwords-div">
      <h4>Please enter password and Branch version:</h4>
      <br />
      <form id="passwords-form" action="" method="POST">
        <div class="restore-page">
          <input type="password" id="password" name="password" placeholder="Password" class="input-large close-join" />
          <div class="start-hidden alert pozRestore" id="resto-pass">
          </div>
          <br />
        </div>
        <div class="restore-page">
          <input type="text" id="version" name="version" placeholder="Branch version" title="Please retype your password" class="input-large close-join"/>
          <div class="start-hidden alert pozRestore" id="resto-repass">
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
 <div id="password-reset-box">
   <h4>Response message:</h4>
   <br/>
   <pre><?=$message?></pre>
 </div>
</body>
</html>
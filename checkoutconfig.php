<?php
	//Edit these only!
	$reponame = "testing";
	$keyname = "clonekey";
	//End Editable variables.

	$checkoutPassword = 111;
	$githubKey = "/home/www-data/.ssh/$keyname";
	$projectGithubDirectory = "/home/www-data/$reponame";		//Project github directory
	$github = "git@github.com:peterbrescia/$reponame.git";		//Github project location.
	$lastVersionFile = "/home/www-data/$reponame/lastversion.php";
	$githubkeyorigin = "/var/www/project-checkout/$keyname";
	$projectWorkingDirectory = "/var/git-working-directory/";
?>
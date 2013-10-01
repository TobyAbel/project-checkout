<?php
	//Edit these only!
	$reponame = "unicorn";
	$webroot = "wordpress/wp-content/themes/unicorn/";
	$keyname = "clonekey";
	//End Editable variables.

	$checkoutPassword = 111;
	$githubKey = "/home/www-data/.ssh/$keyname";
	$projectGithubDirectory = "/home/www-data/$reponame";		//Producct github directory
	$projectWorkingDirectory = "/var/www/$webroot";
	$github = "git@github.com:peterbrescia/$reponame.git";		//Github project location.
	$directory = "/home/www-data/$reponame/";
	$lastVersionFile = "/home/www-data/$reponame/lastversion.php";
	$githubkeyorigin = "/var/www/project-checkout/$keyname";
?>
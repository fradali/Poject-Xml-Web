<?php
	/**
	 * XML Project
	 * @version		1.0.0
	 * @authors 		Frad Ali and Jmal Chadly	 	 
	 */
	require_once('xml.class.php');
	
	$newFile = new xmlMap();
	// CREATE A GROUPS TAG
	$newFile->groups = new xmlNode();
	// CREATE THE GROUP ARRAY
	$newFile->groups->group = array();
	// ADD FIRST GROUP
	$group = new xmlNode();
	$group->name = 'admin';
	$group->enabled = 'true';
	$newFile->groups->add('group', $group); // ADD THE GROUP
	// ADD SECOND GROUP
	$group = new xmlNode();
	$group->name = 'guests';
	$group->enabled = 'false';
	$newFile->groups->add('group', $group); // ADD THE GROUP
	// SAVE
	$newFile->save('groups.xml');
	echo 'Done...';
?>
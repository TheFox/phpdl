<?php

/* Created @ 16.03.2010 by TheFox */

define('ANTIHACK', 1);

include_once('./lib/config.php');
include_once('./lib/functions.php');


$a = $_GET['a'];
$id = (int)$_GET['id'];
$smarty = smartyNew();

switch($a){
	
	default:
		
		$tpl = 'default.tpl';
		$cacheId = 'default';
		if(!$smarty->isCached($tpl, $cacheId)){
			smartyAssignStd($smarty);
			
			$dbh = dbConnect();
			dbClose($dbh);
			
			$smarty->assign('content', '');
			
		}
		$smarty->display($tpl, $cacheId);
		
	break;
	
	case 'dlpacketEdit':
		
		$tpl = $a.'.tpl';
		$cacheId = $a;
		if(!$smarty->isCached($tpl, $cacheId)){
			smartyAssignStd($smarty);
			
			$dbh = dbConnect();
			
			dbClose($dbh);
			
			$filesOut = '';
			if($id){
				// Edit
			}
			else{
				// Add new
			}
			
			
			$smarty->assign('id', $id);
			$smarty->assign('files', $filesOut);
			
		}
		$smarty->display($tpl, $cacheId);
		
		
	break;
	
	case 'dlpacketEditSave':
		
		if($id){
			// Edit
		}
		else{
			// Add new
		}
		
		//header('Location: ?');
		
	break;
	
}

?>
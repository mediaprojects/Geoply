<?php

$markers = array();

define('MONGODB_NAME', 'geo');

if(isset($_GET['limit'])) $limit = (int)$_GET['limit'];
else $limit = 500;

if(isset($_GET['feature_code'])) $feature_code = $_GET['feature_code'];
else $feature_code = false;

require_once(__DIR__.'/mb/classes/mb_base.class.php'); // The one file to rule them all
require_once(__DIR__.'/mb/classes/mb_db.class.php'); // Required for MongoDB Connections

$db = new MONGOBASE_DB; // Assign the DB

if(isset($_POST['lat'])) $lat = (float)$_POST['lat'];
else $lat = 3.152864; // These defaults represent KL MUG HQ
if(isset($_POST['lng'])) $lng = (float)$_POST['lng'];
else $lng = 101.712624; // These defaults represent KL MUG HQ
// More information on KL MUG - http://lauli.ma/klmug

$query = array(
	'col'	=> 'geonames',
	'limit'	=> $limit,
	'near'	=> array( $lng, $lat )
);
$results = $db->find($query);

if((isset($results))&&(is_array($results))){
	foreach($results as $result){
		$continue = true;
		if($feature_code){
			if($feature_code==$result['feature_code']){
				$continue = true;
			}else{
				$continue = false;
			}
		} if($continue){
			$marker_info['lat'] = $result['loc']['lat'];
			$marker_info['lng'] = $result['loc']['lng'];
			$marker_info['title'] = $result['name'];
			$marker_info['content'] = '<pre>'.print_r($result,true).'</pre>';
			$marker_info['this_id'] = $db->_id($result['_id']);
			$marker_info['slug'] = '?id='.$db->_id($result['_id']);
			if(file_exists(__DIR__.'/img/'.$result['feature_code'].'.png')){
				$marker_info['icon'] = $result['feature_code'].'.png';
			}else{
				$marker_info['icon'] = false;
			}
			if(count($markers)<1) $marker_info['open'] = true;
			else $marker_info['open'] = false;
			$markers[] = $marker_info;
		}
	}
}

echo json_encode($markers);
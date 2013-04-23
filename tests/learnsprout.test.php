<?php

	$userid = "506bc8800e130a4b4b919424";
	$type = "student";

	$courseid = "506bc8630e130a4b4b9191b1";

	require_once('../sislib/learnsprout.class.php');
	
	$api = new SisApi('fcb8534c-e4ee-4e02-8b22-9328db1dac18',
			  'https://v1.api.learnsprout.com',
			  '506b8b1f780aa79602388b42');
	$response = $api->getObjects( $userid, 'sections', $type );

	$apiuser = $api->getUser($userid, $type);
	echo $apiuser->id;
	// Entire Student Object Response
        if( empty($response) ){
          echo "Student Not Found";
          exit;
        }

	$sections = (object) $response;
	print_r($response);
	foreach( $sections as $section ){
		echo "Teacher: ".$section['teacher']['id'];
		//print_r($section);
	}

	$course = $api->getCourse( $courseid );
	echo $course['id'];
	print_r($course);
?>

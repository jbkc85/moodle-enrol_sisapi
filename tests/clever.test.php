<?php

	require_once('../sislib/clever.class.php');
	
	$api = new SisApi('DEMO_KEY','https://api.getclever.com/v1.1');
	$response = $api->getStudentId( "" );
	$response = $api->getStudentId( '"name.first":"Cleveland","name.middle":"N","name.last":"Waterssss"' );

	// Entire Student Object Response
        if( empty($response->data[0]) ){
          echo "Student Not Found";
          exit;
        }
	echo print_r($response);
	// Object Reference to the District ID
	// echo $response->data[0]->data->district;
	// Object Reference to the School ID
	// echo $response->data[0]->data->school;
	// Object Reference to the Student ID
	//echo $response->data[0]->data->id;
	// Object Reference to the URI of the Student Info (w/o search)
	// echo $response->data[0]->uri;

	#$sections = $api->getCourses( $response->data[0]->data->id );
	// Entire Course Object Response
	#echo print_r($sections);
	// Print out Sections in a foreach loop
	#foreach($sections->data as $section){
		// Entire Section
		//echo print_r($section);
                // Section ID
        #        echo $section->data->course_number;
	#}

?>

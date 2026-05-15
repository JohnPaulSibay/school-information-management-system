<?php
    $gradingMethodId = isset($_REQUEST['gid']) ? $_REQUEST['gid'] : "";

    if($gradingMethodId == "")
    {
        echo "Grade category ID is required.";
        exit;
    }

    $apiUrl = "http://localhost:3000/api/grade-categories/".rawurlencode($gradingMethodId);
    $context = stream_context_create(array(
        "http" => array(
            "method" => "DELETE",
            "timeout" => 5,
            "ignore_errors" => true,
            "header" => "Accept: application/json\r\n"
        )
    ));

    $response = @file_get_contents($apiUrl, false, $context);

    if($response === false)
    {
        echo "Failed to connect to Student API.";
        exit;
    }

    $result = json_decode($response, true);

    if(!isset($result['status']) || $result['status'] != "success")
    {
        echo isset($result['message']) ? htmlspecialchars($result['message'], ENT_QUOTES, "UTF-8") : "Student API error.";
    }
?>

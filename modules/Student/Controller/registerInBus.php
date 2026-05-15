<?php
    $studentId = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : "";
    $busId = isset($_REQUEST['bid']) ? $_REQUEST['bid'] : "";
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

    function sendBusApiRequest($method, $path, $data = null)
    {
        $apiUrl = "http://localhost:3000/api".$path;
        $headers = "Accept: application/json\r\n";
        $httpOptions = array(
            "method" => $method,
            "timeout" => 5,
            "ignore_errors" => true,
            "header" => $headers
        );

        if($data !== null)
        {
            $headers .= "Content-Type: application/json\r\n";
            $httpOptions["header"] = $headers;
            $httpOptions["content"] = json_encode($data);
        }

        $context = stream_context_create(array("http" => $httpOptions));
        $response = @file_get_contents($apiUrl, false, $context);

        if($response === false)
        {
            return array("status" => "error", "message" => "Failed to connect to Student API.");
        }

        $result = json_decode($response, true);

        if(json_last_error() !== JSON_ERROR_NONE)
        {
            return array("status" => "error", "message" => "Invalid response from Student API.");
        }

        return $result;
    }

    if($studentId == "" || $busId == "" || $action == "")
    {
        echo "<div style='text-align:center; font-size:18px; margin-top:15px;' class='text-danger'><strong>Missing bus registration information.</strong></div>";
        exit;
    }

    if($action == 1)
    {
        $result = sendBusApiRequest("POST", "/bus-registrations", array(
            "bus_id" => $busId,
            "student_id" => $studentId
        ));
    }
    else if($action == 0)
    {
        $result = sendBusApiRequest("DELETE", "/bus-registrations/student/".rawurlencode($studentId));
    }
    else
    {
        $result = array("status" => "error", "message" => "Invalid bus registration action.");
    }

    if(isset($result['status']) && $result['status'] == "success")
    {
        $message = htmlspecialchars(isset($result['message']) ? $result['message'] : "Bus registration updated successfully.", ENT_QUOTES, "UTF-8");
        echo "<div style='text-align:center; font-size:18px; margin-top:15px;' class='text-success'><strong>$message</strong></div>";
    }
    else
    {
        $message = htmlspecialchars(isset($result['message']) ? $result['message'] : "Student API error.", ENT_QUOTES, "UTF-8");
        echo "<div style='text-align:center; font-size:18px; margin-top:15px;' class='text-danger'><strong>$message</strong></div>";
    }
?>

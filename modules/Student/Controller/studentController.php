<?php 
    require_once '../View/studentView.php';
    require_once 'finalGrade.php';
    require_once 'assignmentGrade.php';
    require_once 'quizGrade.php';
    require_once 'projectGrade.php';
    require_once '../Model/studentModel.php';
    require_once '../../gradingMethodModel.php';
    require_once '../../semesterModel.php';
    require_once '../../systemLog.php';
    require_once '../../notificationModel.php';
    require_once '../../userModel.php';
    require_once '../../lookup.php';
    require_once '../../registrationModel.php';
    require_once '../../bus.php';
    if(session_status() == PHP_SESSION_NONE){session_start();} 
?>

<!DOCTYPE html>

<html lang="en">


<head>

    <meta name="viewport" content="width=device-width, inital-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://kit.fontawesome.com/4733528720.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <title>Student Dashboard</title>

</head>

<body>

<?php

class StudentController
{
    public $studentView;

    public function __construct()
    {
        $this->studentView = new StudentView();
    }

    private function sendApiRequest($path, $method = "GET", $data = null)
    {
        $apiUrl = "http://localhost:3000/api".$path;
        $headers = "Accept: application/json\r\n";
        $httpOptions = array(
            "method" => $method,
            "timeout" => 5,
            "ignore_errors" => true,
            "header" => $headers
        );

        if ($data !== null) {
            $headers .= "Content-Type: application/json\r\n";
            $httpOptions["header"] = $headers;
            $httpOptions["content"] = json_encode($data);
        }

        $context = stream_context_create(array(
            "http" => $httpOptions
        ));

        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            die("Failed to connect to Student API.");
        }

        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Invalid response from Student API.");
        }

        if (!isset($result['status']) || $result['status'] != "success") {
            $message = isset($result['message']) ? $result['message'] : "Student API error.";
            die($message);
        }

        return $result;
    }

    private function getLoggedStudentEmail($user)
    {
        $user->assignAll("WHERE id=".$_SESSION['loggedId']);

        if (count($user->usersArray) == 0) {
            return "";
        }

        return $user->usersArray[0]->email;
    }

    private function getLinkedApiStudentIdForLoggedUser()
    {
        $db = DB::getInstance();
        $localStudentId = (int)$_SESSION['loggedId'];
        $tableCheck = mysqli_query($db, "SHOW TABLES LIKE 'api_student_links'");

        if (!$tableCheck || mysqli_num_rows($tableCheck) == 0) {
            return null;
        }

        $query = "SELECT api_student_id FROM api_student_links WHERE local_student_id = $localStudentId LIMIT 1";
        $result = mysqli_query($db, $query);

        if (!$result || mysqli_num_rows($result) == 0) {
            return null;
        }

        $row = mysqli_fetch_assoc($result);
        return $row['api_student_id'];
    }

    private function getApiStudentForLoggedUser($user)
    {
        if (isset($_SESSION['apiStudentId']) && $_SESSION['apiStudentId'] != "") {
            $result = $this->sendApiRequest("/students/".rawurlencode($_SESSION['apiStudentId']));
            $student = isset($result['data']) ? $result['data'] : null;

            if (isset($student[0]) && is_array($student[0])) {
                $student = $student[0];
            }

            if (is_array($student)) {
                return $student;
            }
        }

        $linkedApiStudentId = $this->getLinkedApiStudentIdForLoggedUser();
        $email = strtolower($this->getLoggedStudentEmail($user));
        $result = $this->sendApiRequest("/students");
        $students = isset($result['data']) ? $result['data'] : array();

        if ($linkedApiStudentId != null) {
            for ($i = 0; $i < count($students); $i++) {
                if (isset($students[$i]['student_id']) && (string)$students[$i]['student_id'] == (string)$linkedApiStudentId) {
                    return $students[$i];
                }
            }
        }

        for ($i = 0; $i < count($students); $i++) {
            if (isset($students[$i]['email']) && strtolower($students[$i]['email']) == $email) {
                return $students[$i];
            }
        }

        for ($i = 0; $i < count($students); $i++) {
            if (isset($students[$i]['student_id']) && $students[$i]['student_id'] == $_SESSION['loggedId']) {
                return $students[$i];
            }
        }

        return null;
    }

    private function getEventsForApiStudent($apiStudent)
    {
        $result = $this->sendApiRequest("/events");
        $events = isset($result['data']) ? $result['data'] : array();
        $studentEvents = array();
        $courseCode = isset($apiStudent['course_code']) ? strtolower($apiStudent['course_code']) : "";

        for ($i = 0; $i < count($events); $i++) {
            if (!isset($events[$i]['course_code'])) {
                continue;
            }

            if (strtolower($events[$i]['course_code']) == $courseCode) {
                array_push($studentEvents, $events[$i]);
            }
        }

        return $studentEvents;
    }
    
    public function subjectsGradesView($studentModel)
    {
        if(isset($_SESSION['usingCentralApi']) && $_SESSION['usingCentralApi'])
        {
            $apiStudent = $this->getApiStudentForLoggedUser(new User());

            if($apiStudent == null || !isset($apiStudent['student_id']))
            {
                $this->studentView->studentApiLinkMissing("Your Subjects' Grades");
                return;
            }

            $gradesResult = $this->sendApiRequest("/grades/student/".rawurlencode($apiStudent['student_id']));
            $categoriesResult = $this->sendApiRequest("/grade-categories");
            $grades = isset($gradesResult['data']) && is_array($gradesResult['data']) ? $gradesResult['data'] : array();
            $categories = isset($categoriesResult['data']) && is_array($categoriesResult['data']) ? $categoriesResult['data'] : array();

            $this->studentView->showApiSubjectsWithGrades($apiStudent, $grades, $categories);
            new SystemLog("Student Viewed API Grades", $_SESSION['loggedId']);
            return;
        }

        $studentSubjects = $studentModel->getSubjects();
            $overallGrades = array();
            for($i = 0; $i < count($studentSubjects); $i++)
            {
                $finalGradeModel = new FinalGrade($studentSubjects[$i]->id);
                $finalGradeModel = new Assignment($finalGradeModel, $studentSubjects[$i]->id);
                $finalGradeModel = new Quiz($finalGradeModel, $studentSubjects[$i]->id);
                $finalGradeModel = new Project($finalGradeModel, $studentSubjects[$i]->id);
                $overallGrades[] = $finalGradeModel->upgradeGrade();
            }
           
            
            $gradingMethodModel = new GradingMethod();
            $gradingMethods = $gradingMethodModel->getFromDB();
        
            $this->studentView->showSubjectsWithGrades($studentSubjects, $overallGrades, $gradingMethods);
            new SystemLog("Student Viewed His Grades", $_SESSION['loggedId']);
    }

    public function notificationPageView()
    {
        $notifyObj = new NotificationModel();
        $id = $_SESSION['loggedId'];
        $this->studentView->ShowNotification($notifyObj->fetchAll("WHERE user_id = $id"));
    }

    public function myIDView($studentModel, $semesterModel, $regModel, $user, $lookUp)
    {
            $semesterName = $semesterModel->selectSemesterWithName($_SESSION['loggedId']);
            $regDate = $regModel->fetchDate($_SESSION['loggedId']);
            $qrLink = $lookUp->fetchRows("*", "qr_link");
            $user->assignAll("WHERE id=".$_SESSION['loggedId']);
            if($studentModel->checkIfRegistered())
                $this->studentView->MyIdPage($qrLink[0]->name, $semesterName, $user->usersArray[0], $studentModel->decryptImage($user->usersArray[0]->face_image), $regDate);
            else
                $this->studentView->notRegisteredErrorInIdPage();
    }

    public function registerInBusView($busModel)
    {
        $apiStudent = $this->getApiStudentForLoggedUser(new User());

        if($apiStudent == null || !isset($apiStudent['student_id']))
        {
            $this->studentView->studentApiLinkMissing("Bus Registration");
            return;
        }

        $busesResult = $this->sendApiRequest("/buses");
        $busesArray = $this->convertApiBusesToLegacyObjects(isset($busesResult['data']) ? $busesResult['data'] : array());

        $registrationResult = $this->sendApiRequest("/bus-registrations?student_id=".rawurlencode($apiStudent['student_id']));
        $registrations = isset($registrationResult['data']) && is_array($registrationResult['data']) ? $registrationResult['data'] : array();
        $checkIfRegistered = count($registrations) > 0 && isset($registrations[0]['bus_id']) ? $registrations[0]['bus_id'] : -1;

        $this->studentView->registerInBus($busesArray, $checkIfRegistered, $apiStudent['student_id']);
    }

    private function convertApiBusesToLegacyObjects($apiBuses)
    {
        $buses = array();

        for($i = 0; $i < count($apiBuses); $i++)
        {
            $apiBus = $apiBuses[$i];
            $bus = new stdClass();
            $bus->id = isset($apiBus['bus_id']) ? $apiBus['bus_id'] : "";
            $bus->route = isset($apiBus['route_name']) ? $apiBus['route_name'] : "";
            $bus->meetAt = isset($apiBus['meet_at']) ? $apiBus['meet_at'] : "";
            $bus->code = isset($apiBus['bus_code']) ? $apiBus['bus_code'] : "";
            $bus->driverName = isset($apiBus['driver_name']) ? $apiBus['driver_name'] : "";
            $bus->supervisorName = isset($apiBus['supervisor_name']) ? $apiBus['supervisor_name'] : "";
            $bus->supervisorPhoneNumber = isset($apiBus['supervisor_phone_number']) ? $apiBus['supervisor_phone_number'] : "";
            $bus->seatsLeft = isset($apiBus['seats_left']) ? $apiBus['seats_left'] : 0;
            $bus->timeMove = isset($apiBus['time_move']) ? $apiBus['time_move'] : "";
            $bus->timeArrive = isset($apiBus['time_arrive']) ? $apiBus['time_arrive'] : "";
            $bus->fees = isset($apiBus['route_fees']) ? $apiBus['route_fees'] : "";
            $buses[] = $bus;
        }

        return $buses;
    }

    public function attendanceView($user)
    {
        $apiStudent = $this->getApiStudentForLoggedUser($user);

        if ($apiStudent == null || !isset($apiStudent['student_id'])) {
            $this->studentView->studentApiLinkMissing("My Attendance");
            return;
        }

        $result = $this->sendApiRequest("/attendance/student/".rawurlencode($apiStudent['student_id']));
        $attendance = isset($result['data']) ? $result['data'] : array();
        $this->studentView->displayAttendance($apiStudent, $attendance);
        new SystemLog("Student Viewed API Attendance", $_SESSION['loggedId']);
    }

    public function eventsView($user)
    {
        $apiStudent = $this->getApiStudentForLoggedUser($user);

        if ($apiStudent == null) {
            $this->studentView->studentApiLinkMissing("My Events");
            return;
        }

        $events = $this->getEventsForApiStudent($apiStudent);
        $this->studentView->displayEvents($apiStudent, $events);
        new SystemLog("Student Viewed API Events", $_SESSION['loggedId']);
    }

    public function dashboardView($studentModel, $user)
    {
        if(isset($_SESSION['usingCentralApi']) && $_SESSION['usingCentralApi'] && isset($_SESSION['apiRole']) && $_SESSION['apiRole'] == "Student")
        {
            $apiStudent = $this->getApiStudentForLoggedUser($user);
            $this->studentView->displayCentralApiDashboardPage($apiStudent);
            return;
        }

        $notifyObj = new NotificationModel();
        $id = $_SESSION['loggedId'];
        $this->studentView->displayDashboardPage($user->fetchName($id), $studentModel->getSemesterName(new SemesterModel()), $notifyObj->fetchAll("WHERE user_id = $id AND IsRead = 0"));
    }
}

$studentController = new StudentController();

if(isset($_REQUEST['selected']))
    {
        if($_REQUEST['selected'] == "SubjectsGrades")
        {
            $studentController->subjectsGradesView(new Student);
            
        }
        if($_REQUEST['selected'] == "NotificationPage")
        {
           $studentController->notificationPageView();
        }

        if($_REQUEST['selected'] == "MyID")
        {
            $studentController->myIDView(new Student, new SemesterModel, new RegistrationModel, new user, new LookUp);
        }

        if($_REQUEST['selected'] == "RegisterInBus")
        {
           $studentController->registerInBusView(new Bus);
        }

        if($_REQUEST['selected'] == "Attendance")
        {
           $studentController->attendanceView(new user);
        }

        if($_REQUEST['selected'] == "Events")
        {
           $studentController->eventsView(new user);
        }
    }
    else
    {
        $studentController->dashboardView(new Student, new user);
    }
?>

</body>


</html>

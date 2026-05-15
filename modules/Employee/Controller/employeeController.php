<?php
    if(session_status() == PHP_SESSION_NONE){session_start();}
    require_once 'employeeControllerFactory.php';
    $factory = new EmployeeControllerFactory();
    $employeeModelTemp = $factory->createObject("employeeModel");
    $employees = $employeeModelTemp->selectAllEmployees();
    $hasPermission = false;

    if(isset($_SESSION['apiRole']) && $_SESSION['apiRole'] == "Administrator" && isset($_REQUEST['access']) && $_REQUEST['access'] == "api_session")
    {
        $hasPermission = true;
    }

    for($i = 0; $i < count($employees); $i++)
    {
        if(isset($_REQUEST['access']) && $_REQUEST['access'] == $employeeModelTemp->pwdEncryption($employees[$i]->password))
        {
            $hasPermission = true;
            break;
        }
    }

    if(!$hasPermission)
    {
        die("No Permission");
    }

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
    <script src="https://cdn.ckeditor.com/ckeditor5/23.1.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" type="text/css" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://kit.fontawesome.com/4733528720.js" crossorigin="anonymous"></script>

    <title>Employee cPanel</title>

</head>

<body>

<?php

class EmployeeController
{
    public $factory;
    public $employeeModel;
    public $employeeView;
    /*public $subjectsModel = $factory->createObject("subjectModel");
    public $subjectsObj = $subjectsModel->selectAllSubjects();
    public $semestersArray = $semesterModel->selectAllSemesters();
    public $registrationDetailsModel = $factory->createObject("registrationDetailsModel");
    public $registrationModel = $factory->createObject("registrationModel");
    public $regItemModel = $factory->createObject("registrationItemDetailsModel");
    public $itemModel = $factory->createObject("itemModel"); //*/
    //public $busModel = $factory->createObject("bus");

    public function __construct()
    {
        $this->factory = new EmployeeControllerFactory();
        $this->employeeView = $this->factory->createObject("employeeView");
        $this->employeeModel = $this->factory->createObject("employeeModel");
    }

    public function showAllTypesView()
    {
        $students = $this->getStudentsFromApi();
        $lecturers = $this->getLecturersFromApi();
        $parents = $this->getParentsFromApi();
        $this->employeeView->showAllTypes(count($students), count($lecturers), count($parents));
    }

    public function aboutUsView()
    {
        $aboutUs = $this->getAboutUsFromApi();
        $oldContent = isset($aboutUs['html_data']) ? $aboutUs['html_data'] : "";
        $this->employeeView->aboutUs($oldContent);
    }

    public function addSubjectsView($semestersArray)
    {
        $this->employeeView->displayApiSubjects($this->getSubjectsFromApi());
    }

    public function createApiSubjectView()
    {
        $this->sendStudentApiRequest("POST", "/subjects", $this->getApiSubjectPayloadFromPost());
        new SystemLog("Employee Add API Subject", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=addsubjects';</script>";
        exit;
    }

    public function editApiSubjectView()
    {
        if(!isset($_REQUEST['id']))
        {
            die("Subject ID is required.");
        }

        $subject = $this->getSubjectFromApiById($_REQUEST['id']);

        if($subject == null)
        {
            die("Subject was not found in the S3 API.");
        }

        $this->employeeView->apiSubjectForm($subject, "Edit API Subject", "updateApiSubject");
    }

    public function updateApiSubjectView()
    {
        if(!isset($_REQUEST['id']))
        {
            die("Subject ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/subjects/".rawurlencode($_REQUEST['id']), $this->getApiSubjectPayloadFromPost());
        new SystemLog("Employee Update API Subject", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=addsubjects';</script>";
        exit;
    }

    public function deleteApiSubjectView()
    {
        if(!isset($_REQUEST['id']))
        {
            die("Subject ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/subjects/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Subject", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=addsubjects';</script>";
        exit;
    }

    public function billsPageView()
    {
        $this->employeeView->billsPage();
    }

    public function configGradesView()
    {
        if(isset($_POST['submitGradeType']))
        {
            $this->sendStudentApiRequest("POST", "/grade-categories", $this->getApiGradeCategoryPayloadFromPost());
            new SystemLog("Employee Add API Grade Category", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=ConfigGrades';</script>";
            exit;
        }

        $this->employeeView->configGradesPage($this->getGradeCategoriesFromApi());
    }

    public function gradeRecordsView()
    {
        $this->employeeView->displayApiGradeRecords($this->getGradeRecordsFromApi());
    }

    public function addNewSemesterView()
    {
        $this->employeeView->displayApiSemesters($this->getSemestersFromApi());
    }

    public function createApiSemesterView()
    {
        $this->sendStudentApiRequest("POST", "/semesters", $this->getApiSemesterPayloadFromPost());
        new SystemLog("Employee Add API Semester", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=AddNewSemester';</script>";
        exit;
    }

    public function editApiSemesterView()
    {
        if(!isset($_REQUEST['id']))
        {
            die("Semester ID is required.");
        }

        $semester = $this->getSemesterFromApiById($_REQUEST['id']);

        if($semester == null)
        {
            die("Semester was not found in the S3 API.");
        }

        $this->employeeView->apiSemesterForm($semester, "Edit API Semester", "updateApiSemester");
    }

    public function updateApiSemesterView()
    {
        if(!isset($_REQUEST['id']))
        {
            die("Semester ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/semesters/".rawurlencode($_REQUEST['id']), $this->getApiSemesterPayloadFromPost());
        new SystemLog("Employee Update API Semester", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=AddNewSemester';</script>";
        exit;
    }

    public function deleteApiSemesterView()
    {
        if(!isset($_REQUEST['id']))
        {
            die("Semester ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/semesters/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Semester", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=AddNewSemester';</script>";
        exit;
    }

    public function systemMessagesView()
    {
        if(isset($_POST['submitSystemMessage']))
        {
            $this->sendStudentApiRequest("POST", "/system-messages", $this->getApiSystemMessagePayloadFromPost());
            new SystemLog("Employee Add API System Message", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=SystemMessages';</script>";
            exit;
        }

        $this->employeeView->systemMessagesPage($this->getSystemMessagesFromApi());
    }

    public function paymentEavView()
    {
        $this->employeeView->paymentEavPage();
    }

    public function paymentMethodPageView()
    {
        $this->employeeView->paymentMethodPage($this->getPaymentMethodsFromApi());

        if(isset($_POST['submitPaymentMethod']))
        {
            $this->sendStudentApiRequest("POST", "/payment-methods", $this->getApiPaymentMethodPayloadFromPost());
            new SystemLog("Employee Add API Payment Method", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=PaymentMethods';</script>";
            exit;
        }
    }

    public function paymentOptionView($lookUp)
    {
        $this->employeeView->paymentOptionPage($this->getPaymentMethodsFromApi(), $this->getPaymentOptionsFromApi());

        if(isset($_POST['submitPaymentOption']))
        {
            $this->sendStudentApiRequest("POST", "/payment-options", $this->getApiPaymentOptionPayloadFromPost());
            new SystemLog("Employee Add API Payment Option", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=PaymentOption';</script>";
            exit;
        }
    }

    public function qrLinkView()
    {
        if(isset($_POST['updateQR']))
        {
            $this->sendStudentApiRequest("PUT", "/qr-link", $this->getApiQrLinkPayloadFromPost());
            new SystemLog("Employee Update API QR Link", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=QrLink';</script>";
            exit;
        }

        $qr = $this->getQrLinkFromApi();
        $this->employeeView->QrLinkPage(isset($qr['qr_link']) ? $qr['qr_link'] : "");
    }

    public function busView()
    {
        $this->employeeView->busPage($this->getBusesFromApi(), $this->getBusRoutesFromApi());
    }

    public function addNewBusView($lookUp, $busModel)
    {
        if(isset($_POST['submitBus']))
        {
            $this->sendStudentApiRequest("POST", "/buses", $this->getApiBusPayloadFromPost());
            new SystemLog("Employee Add API Bus", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=Bus';</script>";
            exit;
        }

        $this->employeeView->addNewbusPage($this->getBusRoutesFromApi());
    }

    public function addNewRouteView($busModel)
    {
        if(isset($_POST['submitRoute']))
        {
            $this->sendStudentApiRequest("POST", "/bus-routes", $this->getApiBusRoutePayloadFromPost());
            new SystemLog("Employee Add API Bus Route", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=Bus';</script>";
            exit;
        }

        $this->employeeView->addNewRoute();
    }

    public function editAboutUsView()
    {
        if(isset($_POST['content']))
        $newContent = $_POST['content'];

        if($newContent)
        {
            $this->sendStudentApiRequest("PUT", "/about-us", array("html_data" => $newContent));
            new SystemLog("Employee Edited API About Us Page", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&page=aboutUsEmployee';</script>";
            exit;
        }
    }

    private function getStudentsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/students");

        if (!isset($result['data']) || !is_array($result['data'])) {
            die("Student API error.");
        }

        return $this->convertApiStudentsToLegacyObjects($result['data']);
    }

    private function sendStudentApiRequest($method, $path, $data = null)
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

    private function getApiStudentPayloadFromPost()
    {
        return array(
            "student_number" => isset($_POST['student_number']) ? trim($_POST['student_number']) : "",
            "first_name" => isset($_POST['first_name']) ? trim($_POST['first_name']) : "",
            "last_name" => isset($_POST['last_name']) ? trim($_POST['last_name']) : "",
            "course_code" => isset($_POST['course_code']) ? trim($_POST['course_code']) : "",
            "section_name" => isset($_POST['section_name']) ? trim($_POST['section_name']) : "",
            "year_level" => isset($_POST['year_level']) ? trim($_POST['year_level']) : "",
            "email" => isset($_POST['email']) ? trim($_POST['email']) : "",
            "contact_number" => isset($_POST['contact_number']) ? trim($_POST['contact_number']) : "",
            "status" => isset($_POST['status']) ? trim($_POST['status']) : "Active"
        );
    }

    private function getApiCoursePayloadFromPost()
    {
        return array(
            "course_code" => isset($_POST['course_code']) ? trim($_POST['course_code']) : "",
            "course_name" => isset($_POST['course_name']) ? trim($_POST['course_name']) : "",
            "teacher_id" => isset($_POST['teacher_id']) ? trim($_POST['teacher_id']) : ""
        );
    }

    private function getApiEventPayloadFromPost()
    {
        return array(
            "event_name" => isset($_POST['event_name']) ? trim($_POST['event_name']) : "",
            "course_id" => isset($_POST['course_id']) ? trim($_POST['course_id']) : "",
            "room" => isset($_POST['room']) ? trim($_POST['room']) : "",
            "event_date" => isset($_POST['event_date']) ? trim($_POST['event_date']) : ""
        );
    }

    private function getApiAttendancePayloadFromPost()
    {
        return array(
            "student_id" => isset($_POST['student_id']) ? trim($_POST['student_id']) : "",
            "attendance_date" => isset($_POST['attendance_date']) ? trim($_POST['attendance_date']) : "",
            "status" => isset($_POST['status']) ? trim($_POST['status']) : "",
            "time_in" => isset($_POST['time_in']) ? trim($_POST['time_in']) : "",
            "remarks" => isset($_POST['remarks']) ? trim($_POST['remarks']) : ""
        );
    }

    private function getApiLecturerPayloadFromPost()
    {
        return array(
            "teacher_name" => isset($_POST['teacher_name']) ? trim($_POST['teacher_name']) : "",
            "email" => isset($_POST['email']) ? trim($_POST['email']) : "",
            "contact_number" => isset($_POST['contact_number']) ? trim($_POST['contact_number']) : ""
        );
    }

    private function getApiParentPayloadFromPost()
    {
        return array(
            "first_name" => isset($_POST['first_name']) ? trim($_POST['first_name']) : "",
            "last_name" => isset($_POST['last_name']) ? trim($_POST['last_name']) : "",
            "email" => isset($_POST['email']) ? trim($_POST['email']) : "",
            "contact_number" => isset($_POST['contact_number']) ? trim($_POST['contact_number']) : "",
            "address" => isset($_POST['address']) ? trim($_POST['address']) : "",
            "student_id" => isset($_POST['student_id']) ? trim($_POST['student_id']) : ""
        );
    }

    private function getApiSubjectPayloadFromPost()
    {
        return array(
            "subject_code" => isset($_POST['subject_code']) ? trim($_POST['subject_code']) : (isset($_POST['subjectCode']) ? trim($_POST['subjectCode']) : ""),
            "subject_name" => isset($_POST['subject_name']) ? trim($_POST['subject_name']) : (isset($_POST['subjectName']) ? trim($_POST['subjectName']) : "")
        );
    }

    private function getApiSemesterPayloadFromPost()
    {
        return array(
            "semester_name" => isset($_POST['semester_name']) ? trim($_POST['semester_name']) : (isset($_POST['semesterName']) ? strtoupper(trim($_POST['semesterName'])) : ""),
            "fees" => isset($_POST['fees']) ? trim($_POST['fees']) : (isset($_POST['semesterFees']) ? trim($_POST['semesterFees']) : "")
        );
    }

    private function getApiBillItemPayloadFromPost()
    {
        return array(
            "item_name" => isset($_POST['item_name']) ? trim($_POST['item_name']) : (isset($_POST['itemName']) ? trim($_POST['itemName']) : ""),
            "amount" => isset($_POST['amount']) ? trim($_POST['amount']) : (isset($_POST['itemValue']) ? trim($_POST['itemValue']) : "")
        );
    }

    private function getApiPaymentMethodPayloadFromPost()
    {
        return array(
            "method_name" => isset($_POST['method_name']) ? trim($_POST['method_name']) : (isset($_POST['methodName']) ? trim($_POST['methodName']) : "")
        );
    }

    private function getApiPaymentOptionPayloadFromPost()
    {
        $methodId = isset($_POST['method_id']) ? trim($_POST['method_id']) : "";

        if($methodId == "" && isset($_POST['methodNameV2']))
        {
            $methods = $this->getPaymentMethodsFromApi();
            for($i = 0; $i < count($methods); $i++)
            {
                if(isset($methods[$i]['method_name']) && strtolower($methods[$i]['method_name']) == strtolower(trim($_POST['methodNameV2'])))
                {
                    $methodId = $methods[$i]['method_id'];
                    break;
                }
            }
        }

        return array(
            "method_id" => $methodId,
            "option_name" => isset($_POST['option_name']) ? trim($_POST['option_name']) : (isset($_POST['optionName']) ? trim($_POST['optionName']) : ""),
            "option_type" => isset($_POST['option_type']) ? trim($_POST['option_type']) : (isset($_POST['optionType']) ? trim($_POST['optionType']) : "")
        );
    }

    private function getApiBusRoutePayloadFromPost()
    {
        return array(
            "route_name" => isset($_POST['route_name']) ? trim($_POST['route_name']) : (isset($_POST['routeName']) ? strtolower(trim($_POST['routeName'])) : ""),
            "route_fees" => isset($_POST['route_fees']) ? trim($_POST['route_fees']) : (isset($_POST['routeFees']) ? trim($_POST['routeFees']) : "")
        );
    }

    private function getApiBusPayloadFromPost()
    {
        return array(
            "route_id" => isset($_POST['route_id']) ? trim($_POST['route_id']) : "",
            "bus_code" => isset($_POST['bus_code']) ? trim($_POST['bus_code']) : (isset($_POST['busCode']) ? trim($_POST['busCode']) : ""),
            "meet_at" => isset($_POST['meet_at']) ? trim($_POST['meet_at']) : (isset($_POST['meetAt']) ? trim($_POST['meetAt']) : ""),
            "driver_name" => isset($_POST['driver_name']) ? trim($_POST['driver_name']) : (isset($_POST['driverName']) ? trim($_POST['driverName']) : ""),
            "supervisor_name" => isset($_POST['supervisor_name']) ? trim($_POST['supervisor_name']) : (isset($_POST['supervisorName']) ? trim($_POST['supervisorName']) : ""),
            "supervisor_phone_number" => isset($_POST['supervisor_phone_number']) ? trim($_POST['supervisor_phone_number']) : (isset($_POST['supervisorPhoneNumber']) ? trim($_POST['supervisorPhoneNumber']) : ""),
            "seats_capacity" => isset($_POST['seats_capacity']) ? trim($_POST['seats_capacity']) : (isset($_POST['busSeats']) ? trim($_POST['busSeats']) : ""),
            "time_move" => isset($_POST['time_move']) ? trim($_POST['time_move']) : (isset($_POST['timeMove']) ? strtolower(trim($_POST['timeMove'])) : ""),
            "time_arrive" => isset($_POST['time_arrive']) ? trim($_POST['time_arrive']) : (isset($_POST['timeArrive']) ? strtolower(trim($_POST['timeArrive'])) : "")
        );
    }

    private function getApiGradeCategoryPayloadFromPost()
    {
        return array(
            "category_name" => isset($_POST['category_name']) ? trim($_POST['category_name']) : (isset($_POST['gradeType']) ? strtolower(trim($_POST['gradeType'])) : ""),
            "max_score" => isset($_POST['max_score']) ? trim($_POST['max_score']) : (isset($_POST['typeMarks']) ? trim($_POST['typeMarks']) : ""),
            "display_order" => isset($_POST['display_order']) ? trim($_POST['display_order']) : 0
        );
    }

    private function getApiSystemMessagePayloadFromPost()
    {
        return array(
            "message_type" => isset($_POST['message_type']) ? trim($_POST['message_type']) : (isset($_POST['messageType']) ? trim($_POST['messageType']) : ""),
            "message_content" => isset($_POST['message_content']) ? trim($_POST['message_content']) : (isset($_POST['messageContent']) ? trim($_POST['messageContent']) : "")
        );
    }

    private function getApiQrLinkPayloadFromPost()
    {
        return array(
            "qr_link" => isset($_POST['qr_link']) ? trim($_POST['qr_link']) : (isset($_POST['qrLink']) ? trim($_POST['qrLink']) : "")
        );
    }

    private function getApiStudentId($apiStudent)
    {
        if (isset($apiStudent['student_id'])) {
            return $apiStudent['student_id'];
        }

        if (isset($apiStudent['id'])) {
            return $apiStudent['id'];
        }

        return "";
    }

    private function convertApiStudentsToLegacyObjects($apiStudents)
    {
        $students = array();

        for ($i = 0; $i < count($apiStudents); $i++) {
            $apiStudent = $apiStudents[$i];
            $student = new stdClass();
            $status = isset($apiStudent['status']) ? strtolower($apiStudent['status']) : "active";
            $studentId = $this->getApiStudentId($apiStudent);

            $student->id = $studentId;
            $student->user_type = 3;
            $student->first_name = isset($apiStudent['first_name']) ? $apiStudent['first_name'] : "";
            $student->second_name = isset($apiStudent['last_name']) ? $apiStudent['last_name'] : "";
            $student->third_name = "";
            $student->accepted = in_array($status, array("pending", "inactive")) ? 0 : 1;
            $student->isDeleted = in_array($status, array("deleted", "archived")) ? 1 : 0;

            $student->student_id = $studentId;
            $student->student_number = isset($apiStudent['student_number']) ? $apiStudent['student_number'] : "";
            $student->course_code = isset($apiStudent['course_code']) ? $apiStudent['course_code'] : "";
            $student->section_name = isset($apiStudent['section_name']) ? $apiStudent['section_name'] : "";
            $student->year_level = isset($apiStudent['year_level']) ? $apiStudent['year_level'] : "";
            $student->email = isset($apiStudent['email']) ? $apiStudent['email'] : "";
            $student->contact_number = isset($apiStudent['contact_number']) ? $apiStudent['contact_number'] : "";
            $student->status = isset($apiStudent['status']) ? $apiStudent['status'] : "";

            array_push($students, $student);
        }

        return $students;
    }

    private function getStudentFromApiById($studentId)
    {
        $result = $this->sendStudentApiRequest("GET", "/students/".rawurlencode($studentId));

        if (!isset($result['data']) || !is_array($result['data'])) {
            return null;
        }

        $apiStudent = $result['data'];

        if (isset($apiStudent[0]) && is_array($apiStudent[0])) {
            $apiStudent = $apiStudent[0];
        }

        $students = $this->convertApiStudentsToLegacyObjects(array($apiStudent));
        return $students[0];
    }

    private function getAttendanceForApiStudent($studentId)
    {
        $result = $this->sendStudentApiRequest("GET", "/attendance/student/".rawurlencode($studentId));
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getEventsForApiStudent($student)
    {
        $result = $this->sendStudentApiRequest("GET", "/events");
        $events = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
        $studentEvents = array();
        $courseCode = strtolower($student->course_code);

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

    private function getLecturersFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/lecturers");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getLecturerFromApiById($teacherId)
    {
        $lecturers = $this->getLecturersFromApi();

        for ($i = 0; $i < count($lecturers); $i++) {
            if (isset($lecturers[$i]['teacher_id']) && (string)$lecturers[$i]['teacher_id'] == (string)$teacherId) {
                return $lecturers[$i];
            }
        }

        return null;
    }

    private function getCourseFromApiById($courseId)
    {
        $result = $this->sendStudentApiRequest("GET", "/courses");
        $courses = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();

        for ($i = 0; $i < count($courses); $i++) {
            if (isset($courses[$i]['course_id']) && (string)$courses[$i]['course_id'] == (string)$courseId) {
                return $courses[$i];
            }
        }

        return null;
    }

    private function getCoursesFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/courses");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getSubjectsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/subjects");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getSubjectFromApiById($subjectId)
    {
        $result = $this->sendStudentApiRequest("GET", "/subjects/".rawurlencode($subjectId));
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : null;
    }

    private function getSemestersFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/semesters");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getSemesterFromApiById($semesterId)
    {
        $result = $this->sendStudentApiRequest("GET", "/semesters/".rawurlencode($semesterId));
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : null;
    }

    private function getParentsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/parents");
        $parents = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
        return $this->convertApiParentsToLegacyObjects($parents);
    }

    private function getParentFromApiById($parentId)
    {
        $result = $this->sendStudentApiRequest("GET", "/parents/".rawurlencode($parentId));
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : null;
    }

    private function convertApiParentsToLegacyObjects($apiParents)
    {
        $parents = array();

        for($i = 0; $i < count($apiParents); $i++)
        {
            $apiParent = $apiParents[$i];
            $parent = new stdClass();
            $parent->id = isset($apiParent['parent_id']) ? $apiParent['parent_id'] : "";
            $parent->parent_id = $parent->id;
            $parent->user_type = 4;
            $parent->first_name = isset($apiParent['first_name']) ? $apiParent['first_name'] : "";
            $parent->second_name = isset($apiParent['last_name']) ? $apiParent['last_name'] : "";
            $parent->third_name = "";
            $parent->email = isset($apiParent['email']) ? $apiParent['email'] : "";
            $parent->contact_number = isset($apiParent['contact_number']) ? $apiParent['contact_number'] : "";
            $parent->address = isset($apiParent['address']) ? $apiParent['address'] : "";
            $parent->student_id = isset($apiParent['student_id']) ? $apiParent['student_id'] : "";
            $parent->student_name = isset($apiParent['student_name']) ? $apiParent['student_name'] : "";
            $parent->accepted = 1;
            $parent->isDeleted = 0;
            array_push($parents, $parent);
        }

        return $parents;
    }

    private function getBillItemsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/bill-items");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getPaymentMethodsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/payment-methods");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getPaymentOptionsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/payment-options");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getBillsFromApi($studentId = null)
    {
        $path = "/bills";
        if($studentId != null && $studentId != "")
        {
            $path .= "?student_id=".rawurlencode($studentId);
        }

        $result = $this->sendStudentApiRequest("GET", $path);
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getBusRoutesFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/bus-routes");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getBusesFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/buses");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getGradeCategoriesFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/grade-categories");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getGradeRecordsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/grades");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getSystemMessagesFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/system-messages");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getAboutUsFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/about-us");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getQrLinkFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/qr-link");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getReportsSummaryFromApi()
    {
        $result = $this->sendStudentApiRequest("GET", "/reports/summary");
        return isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    }

    private function getEventFromApiById($eventId)
    {
        $result = $this->sendStudentApiRequest("GET", "/events");
        $events = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();

        for ($i = 0; $i < count($events); $i++) {
            if (isset($events[$i]['event_id']) && (string)$events[$i]['event_id'] == (string)$eventId) {
                return $events[$i];
            }
        }

        return null;
    }

    private function getAttendanceFromApiById($attendanceId)
    {
        $result = $this->sendStudentApiRequest("GET", "/attendance");
        $attendance = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();

        for ($i = 0; $i < count($attendance); $i++) {
            if (isset($attendance[$i]['attendance_id']) && (string)$attendance[$i]['attendance_id'] == (string)$attendanceId) {
                return $attendance[$i];
            }
        }

        return null;
    }

    private function getLocalStudentForApiStudent($apiStudent, $localStudents = null)
    {
        $students = $localStudents == null ? $this->employeeModel->selectAllStudents() : $localStudents;
        $linkedLocalStudentId = $this->employeeModel->getLinkedLocalStudentId($apiStudent->student_id);
        $apiEmail = strtolower(trim($apiStudent->email));
        $apiStudentId = (string)$apiStudent->student_id;

        if ($linkedLocalStudentId != null) {
            for ($i = 0; $i < count($students); $i++) {
                if ((string)$students[$i]->id == (string)$linkedLocalStudentId) {
                    return $students[$i];
                }
            }
        }

        for ($i = 0; $i < count($students); $i++) {
            if ($apiEmail != "" && strtolower(trim($students[$i]->email)) == $apiEmail) {
                return $students[$i];
            }
        }

        for ($i = 0; $i < count($students); $i++) {
            if ((string)$students[$i]->id == $apiStudentId) {
                return $students[$i];
            }
        }

        return null;
    }

    private function getApiStudentsWithLocalLinks()
    {
        $apiStudents = $this->getStudentsFromApi();
        $localStudents = $this->employeeModel->selectAllStudents();
        $links = array();

        for ($i = 0; $i < count($apiStudents); $i++) {
            $link = new stdClass();
            $link->apiStudent = $apiStudents[$i];
            $link->isExplicit = $this->employeeModel->getLinkedLocalStudentId($apiStudents[$i]->student_id) != null;
            $link->localStudent = $this->getLocalStudentForApiStudent($apiStudents[$i], $localStudents);
            array_push($links, $link);
        }

        return $links;
    }

    private function getLocalTeacherForApiLecturer($apiLecturer, $localTeachers = null)
    {
        $teachers = $localTeachers == null ? $this->employeeModel->selectAllTeachers() : $localTeachers;
        $apiTeacherId = isset($apiLecturer['teacher_id']) ? $apiLecturer['teacher_id'] : "";
        $linkedLocalTeacherId = $this->employeeModel->getLinkedLocalTeacherId($apiTeacherId);
        $apiEmail = isset($apiLecturer['email']) ? strtolower(trim($apiLecturer['email'])) : "";

        if($linkedLocalTeacherId != null)
        {
            for($i = 0; $i < count($teachers); $i++)
            {
                if((string)$teachers[$i]->id == (string)$linkedLocalTeacherId)
                {
                    return $teachers[$i];
                }
            }
        }

        for($i = 0; $i < count($teachers); $i++)
        {
            if($apiEmail != "" && strtolower(trim($teachers[$i]->email)) == $apiEmail)
            {
                return $teachers[$i];
            }
        }

        return null;
    }

    private function getApiLecturersWithLocalLinks()
    {
        $apiLecturers = $this->getLecturersFromApi();
        $localTeachers = $this->employeeModel->selectAllTeachers();
        $links = array();

        for($i = 0; $i < count($apiLecturers); $i++)
        {
            $link = new stdClass();
            $link->apiLecturer = $apiLecturers[$i];
            $link->isExplicit = isset($apiLecturers[$i]['teacher_id']) && $this->employeeModel->getLinkedLocalTeacherId($apiLecturers[$i]['teacher_id']) != null;
            $link->localTeacher = $this->getLocalTeacherForApiLecturer($apiLecturers[$i], $localTeachers);
            array_push($links, $link);
        }

        return $links;
    }

    public function studentsView()
    {
        $students = $this->getStudentsFromApi();
        $this->employeeView->displayAll($students, "Students");
    }

    private function showPortalReadOnlyNotice($returnQuery = "page=home")
    {
        $this->employeeView->displayReadOnlyPortalNotice($this->employeeView->url."&".$returnQuery);
    }

    public function attendanceView()
    {
        $result = $this->sendStudentApiRequest("GET", "/attendance");
        $attendance = isset($result['data']) ? $result['data'] : array();
        $this->employeeView->displayApiAttendance($attendance);
    }

    public function addApiAttendanceView()
    {
        $this->showPortalReadOnlyNotice("page=Attendance");
        return;
        $this->employeeView->apiAttendanceForm(null, $this->getStudentsFromApi(), "Add API Attendance", "addApiAttendance");
    }

    public function createApiAttendanceView()
    {
        $this->showPortalReadOnlyNotice("page=Attendance");
        return;
        $this->sendStudentApiRequest("POST", "/attendance", $this->getApiAttendancePayloadFromPost());
        new SystemLog("Employee Add API Attendance", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Attendance';</script>";
        exit;
    }

    public function editApiAttendanceView()
    {
        $this->showPortalReadOnlyNotice("page=Attendance");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Attendance ID is required.");
        }

        $attendance = $this->getAttendanceFromApiById($_REQUEST['id']);

        if ($attendance == null) {
            die("Attendance record was not found in the S3 API.");
        }

        $this->employeeView->apiAttendanceForm($attendance, $this->getStudentsFromApi(), "Edit API Attendance", "updateApiAttendance");
    }

    public function updateApiAttendanceView()
    {
        $this->showPortalReadOnlyNotice("page=Attendance");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Attendance ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/attendance/".rawurlencode($_REQUEST['id']), $this->getApiAttendancePayloadFromPost());
        new SystemLog("Employee Update API Attendance", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Attendance';</script>";
        exit;
    }

    public function deleteApiAttendanceView()
    {
        $this->showPortalReadOnlyNotice("page=Attendance");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Attendance ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/attendance/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Attendance", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Attendance';</script>";
        exit;
    }

    public function eventsView()
    {
        $result = $this->sendStudentApiRequest("GET", "/events");
        $events = isset($result['data']) ? $result['data'] : array();
        $this->employeeView->displayApiEvents($events);
    }

    public function addApiEventView()
    {
        $this->showPortalReadOnlyNotice("page=Events");
        return;
        $this->employeeView->apiEventForm(null, $this->getCoursesFromApi(), "Add API Event", "addApiEvent");
    }

    public function createApiEventView()
    {
        $this->showPortalReadOnlyNotice("page=Events");
        return;
        $this->sendStudentApiRequest("POST", "/events", $this->getApiEventPayloadFromPost());
        new SystemLog("Employee Add API Event", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Events';</script>";
        exit;
    }

    public function editApiEventView()
    {
        $this->showPortalReadOnlyNotice("page=Events");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Event ID is required.");
        }

        $event = $this->getEventFromApiById($_REQUEST['id']);

        if ($event == null) {
            die("Event was not found in the S3 API.");
        }

        $this->employeeView->apiEventForm($event, $this->getCoursesFromApi(), "Edit API Event", "updateApiEvent");
    }

    public function updateApiEventView()
    {
        $this->showPortalReadOnlyNotice("page=Events");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Event ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/events/".rawurlencode($_REQUEST['id']), $this->getApiEventPayloadFromPost());
        new SystemLog("Employee Update API Event", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Events';</script>";
        exit;
    }

    public function deleteApiEventView()
    {
        $this->showPortalReadOnlyNotice("page=Events");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Event ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/events/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Event", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Events';</script>";
        exit;
    }

    public function coursesView()
    {
        $result = $this->sendStudentApiRequest("GET", "/courses");
        $courses = isset($result['data']) ? $result['data'] : array();
        $this->employeeView->displayApiCourses($courses);
    }

    public function addApiCourseView()
    {
        $this->showPortalReadOnlyNotice("page=Courses");
        return;
        $this->employeeView->apiCourseForm(null, $this->getLecturersFromApi(), "Add API Course", "addApiCourse");
    }

    public function createApiCourseView()
    {
        $this->showPortalReadOnlyNotice("page=Courses");
        return;
        $this->sendStudentApiRequest("POST", "/courses", $this->getApiCoursePayloadFromPost());
        new SystemLog("Employee Add API Course", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Courses';</script>";
        exit;
    }

    public function editApiCourseView()
    {
        $this->showPortalReadOnlyNotice("page=Courses");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Course ID is required.");
        }

        $course = $this->getCourseFromApiById($_REQUEST['id']);

        if ($course == null) {
            die("Course was not found in the S3 API.");
        }

        $this->employeeView->apiCourseForm($course, $this->getLecturersFromApi(), "Edit API Course", "updateApiCourse");
    }

    public function updateApiCourseView()
    {
        $this->showPortalReadOnlyNotice("page=Courses");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Course ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/courses/".rawurlencode($_REQUEST['id']), $this->getApiCoursePayloadFromPost());
        new SystemLog("Employee Update API Course", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Courses';</script>";
        exit;
    }

    public function deleteApiCourseView()
    {
        $this->showPortalReadOnlyNotice("page=Courses");
        return;
        if (!isset($_REQUEST['id'])) {
            die("Course ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/courses/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Course", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Courses';</script>";
        exit;
    }

    public function lecturersView()
    {
        $result = $this->sendStudentApiRequest("GET", "/lecturers");
        $lecturers = isset($result['data']) ? $result['data'] : array();
        $this->employeeView->displayApiLecturers($lecturers);
    }

    public function addApiLecturerView()
    {
        $this->employeeView->apiLecturerForm(null, "Add API Lecturer", "addApiLecturer");
    }

    public function createApiLecturerView()
    {
        $this->sendStudentApiRequest("POST", "/lecturers", $this->getApiLecturerPayloadFromPost());
        new SystemLog("Employee Add API Lecturer", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Lecturers';</script>";
        exit;
    }

    public function editApiLecturerView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Lecturer ID is required.");
        }

        $lecturer = $this->getLecturerFromApiById($_REQUEST['id']);

        if ($lecturer == null) {
            die("Lecturer was not found in the S3 API.");
        }

        $this->employeeView->apiLecturerForm($lecturer, "Edit API Lecturer", "updateApiLecturer");
    }

    public function updateApiLecturerView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Lecturer ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/lecturers/".rawurlencode($_REQUEST['id']), $this->getApiLecturerPayloadFromPost());
        new SystemLog("Employee Update API Lecturer", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Lecturers';</script>";
        exit;
    }

    public function deleteApiLecturerView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Lecturer ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/lecturers/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Lecturer", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&page=Lecturers';</script>";
        exit;
    }

    public function studentLinksView()
    {
        $this->showPortalReadOnlyNotice("page=home");
    }

    public function teacherLinksView()
    {
        $this->showPortalReadOnlyNotice("page=home");
    }

    public function linkApiStudentView()
    {
        $this->showPortalReadOnlyNotice("page=home");
        return;
        if(!isset($_POST['apiStudentId']) || !isset($_POST['localStudentId']))
        {
            die("API student and local student are required.");
        }

        if($this->employeeModel->linkApiStudentToLocalStudent($_POST['apiStudentId'], $_POST['localStudentId']))
        {
            new SystemLog("Employee Link API Student ".$_POST['apiStudentId']." to Local Student ".$_POST['localStudentId'], $_SESSION['loggedId']);
        }

        $url = $this->employeeView->url."&page=StudentLinks";
        if(isset($_POST['returnToProfile']))
        {
            $url = $this->employeeView->url."&id=".rawurlencode($_POST['apiStudentId'])."&source=apiStudent";
        }
        header("location:$url");
        exit;
    }

    public function unlinkApiStudentView()
    {
        $this->showPortalReadOnlyNotice("page=home");
        return;
        if(!isset($_POST['apiStudentId']))
        {
            die("API student is required.");
        }

        if($this->employeeModel->unlinkApiStudent($_POST['apiStudentId']))
        {
            new SystemLog("Employee Unlink API Student ".$_POST['apiStudentId'], $_SESSION['loggedId']);
        }

        $url = $this->employeeView->url."&page=StudentLinks";
        if(isset($_POST['returnToProfile']))
        {
            $url = $this->employeeView->url."&id=".rawurlencode($_POST['apiStudentId'])."&source=apiStudent";
        }
        header("location:$url");
        exit;
    }

    public function linkApiTeacherView()
    {
        $this->showPortalReadOnlyNotice("page=home");
        return;
        if(!isset($_POST['apiTeacherId']) || !isset($_POST['localTeacherId']))
        {
            die("API lecturer and local teacher are required.");
        }

        if($this->employeeModel->linkApiTeacherToLocalTeacher($_POST['apiTeacherId'], $_POST['localTeacherId']))
        {
            new SystemLog("Employee Link API Lecturer ".$_POST['apiTeacherId']." to Local Teacher ".$_POST['localTeacherId'], $_SESSION['loggedId']);
        }

        $url = $this->employeeView->url."&page=TeacherLinks";
        header("location:$url");
        exit;
    }

    public function unlinkApiTeacherView()
    {
        $this->showPortalReadOnlyNotice("page=home");
        return;
        if(!isset($_POST['apiTeacherId']))
        {
            die("API lecturer is required.");
        }

        if($this->employeeModel->unlinkApiTeacher($_POST['apiTeacherId']))
        {
            new SystemLog("Employee Unlink API Lecturer ".$_POST['apiTeacherId'], $_SESSION['loggedId']);
        }

        $url = $this->employeeView->url."&page=TeacherLinks";
        header("location:$url");
        exit;
    }

    public function addApiStudentView()
    {
        $this->employeeView->apiStudentForm(null, "Add API Student", "addApiStudent");
    }

    public function createApiStudentView()
    {
        $this->sendStudentApiRequest("POST", "/students", $this->getApiStudentPayloadFromPost());
        new SystemLog("Employee Add API Student", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&selected=student';</script>";
        exit;
    }

    public function editApiStudentView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Student ID is required.");
        }

        $student = $this->getStudentFromApiById($_REQUEST['id']);

        if ($student == null) {
            die("Student was not found in the Student API.");
        }

        $this->employeeView->apiStudentForm($student, "Edit API Student", "updateApiStudent");
    }

    public function updateApiStudentView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Student ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/students/".rawurlencode($_REQUEST['id']), $this->getApiStudentPayloadFromPost());
        new SystemLog("Employee Update API Student", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&id=".$_REQUEST['id']."&source=apiStudent';</script>";
        exit;
    }

    public function deleteApiStudentView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Student ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/students/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Student", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&selected=student';</script>";
        exit;
    }

    public function teachersView()
    {
        $teachers = $this->employeeModel->selectAllTeachers();
        $this->employeeView->displayAll($teachers, "Teachers");
    }

    public function parentsView()
    {
        $parents = $this->getParentsFromApi();
        $this->employeeView->displayAll($parents, "Parents");
    }

    public function addApiParentView()
    {
        $this->employeeView->apiParentForm(null, $this->getStudentsFromApi(), "Add API Parent", "addApiParent");
    }

    public function createApiParentView()
    {
        $this->sendStudentApiRequest("POST", "/parents", $this->getApiParentPayloadFromPost());
        new SystemLog("Employee Add API Parent", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&selected=parent';</script>";
        exit;
    }

    public function editApiParentView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Parent ID is required.");
        }

        $parent = $this->getParentFromApiById($_REQUEST['id']);

        if ($parent == null) {
            die("Parent was not found in the Student API.");
        }

        $this->employeeView->apiParentForm($parent, $this->getStudentsFromApi(), "Edit API Parent", "updateApiParent");
    }

    public function updateApiParentView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Parent ID is required.");
        }

        $this->sendStudentApiRequest("PUT", "/parents/".rawurlencode($_REQUEST['id']), $this->getApiParentPayloadFromPost());
        new SystemLog("Employee Update API Parent", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&id=".$_REQUEST['id']."&source=apiParent';</script>";
        exit;
    }

    public function deleteApiParentView()
    {
        if (!isset($_REQUEST['id'])) {
            die("Parent ID is required.");
        }

        $this->sendStudentApiRequest("DELETE", "/parents/".rawurlencode($_REQUEST['id']));
        new SystemLog("Employee Delete API Parent", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&selected=parent';</script>";
        exit;
    }

    public function createItemView()
    {
        $this->employeeView->createItem($this->getBillItemsFromApi());
    }

    public function createBillView($itemModel)
    {
        $students = $this->employeeModel->selectAllStudents();
        $this->employeeView->createBill($this->getBillItemsFromApi(), $this->getStudentsFromApi());
    }

    public function searchBillsView()
    {
        $this->employeeView->SearchBills($this->getStudentsFromApi());
    }

    public function displayStudentBillView()
    {
        //$students = $this->employeeModel->selectAllStudents();
        $selectedStudentId = explode(" ", $_POST['selectedStudentId2']);
        $url = $this->employeeView->url."&selected=bills&StudentIdBill=".$selectedStudentId[0];
        new SystemLog("Employee Display Bill", $_SESSION['loggedId']);
        header("location:$url");
    }

    public function nameSearchInputView()
    {
        $employees = $this->employeeModel->selectAllEmployees();
        $encID = $_REQUEST['access'];
        $url = $employees[0]->link."?access=".$encID."&".$_REQUEST['selected']."name";
        $nameArray = explode(" ", $_POST['nameSearchInput']);
        $printedName = "";
        for($i = 0; $i < count($nameArray); $i++)
        {
            if($i > 0)
            {
                $printedName.= "_";
            }
            $printedName.=$nameArray[$i];
        }
        header('location:'.$url.'='.$printedName);
    }

    public function searchWithNameStudentsView()
    {
        $students = $this->getStudentsFromApi();
        $nameArray = explode("_", $_REQUEST['studentname']);
        $this->employeeView->searchWithName($students, $nameArray);
    }

    public function searchWithNameTeachersView()
    {
        $teachers = $this->employeeModel->selectAllTeachers();
        $nameArray = explode("_", $_REQUEST['teachername']);
        $this->employeeView->searchWithName($teachers, $nameArray);
    }

    public function searchWithNameParentsView()
    {
        $parents = $this->getParentsFromApi();
        $nameArray = explode("_", $_REQUEST['parentname']);
        $this->employeeView->searchWithName($parents, $nameArray);
    }

    public function displaySpecificUserView()
    {
        if (isset($_REQUEST['source']) && $_REQUEST['source'] == "apiStudent") {
            if (!isset($_REQUEST['id'])) {
                die("Student ID is required.");
            }

            $student = $this->getStudentFromApiById($_REQUEST['id']);

            if ($student == null) {
                die("Student was not found in the Student API.");
            }

            $attendance = $this->getAttendanceForApiStudent($student->student_id);
            $events = $this->getEventsForApiStudent($student);
            $localStudent = $this->getLocalStudentForApiStudent($student);
            $isExplicitLink = $this->employeeModel->getLinkedLocalStudentId($student->student_id) != null;
            $this->employeeView->displayApiStudent($student, $attendance, $events, $localStudent, $this->employeeModel->selectAllStudents(), $isExplicitLink);
            return;
        }

        if (isset($_REQUEST['source']) && $_REQUEST['source'] == "apiParent") {
            if (!isset($_REQUEST['id'])) {
                die("Parent ID is required.");
            }

            $parent = $this->getParentFromApiById($_REQUEST['id']);

            if ($parent == null) {
                die("Parent was not found in the S3 API.");
            }

            $this->employeeView->displayApiParent($parent);
            return;
        }

        $users = $this->employeeModel->selectAllUsers();
        $userType = 0;
        $userObj = null;
        for($i = 0; $i < count($users); $i++)
        {
            if($users[$i]->id == $_REQUEST['id'])
            {
                $userType = $users[$i]->user_type;
                $userObj = $users[$i];
                break;
            }
        }
            $this->employeeView->displaySpecificUser($userObj);
    }

    public function acceptUserView()
    {
        $id = $_REQUEST['id'];
        $this->employeeModel->acceptUser($id);
        new SystemLog("Employee Accept User", $_SESSION['loggedId']);
    }

    public function deleteUserView()
    {
        $id = $_REQUEST['id'];
        $this->employeeModel->deleteUser($id);
        new SystemLog("Employee Delete User", $_SESSION['loggedId']);
    }

    public function reactivateUserView()
    {
        $id = $_REQUEST['id'];
        $this->employeeModel->reActivateUser($id);
        new SystemLog("Employee Re-activate User's Account", $_SESSION['loggedId']);
    }

    public function studentsRegistrationView($userObj, $semestersArray)
    {
        $this->employeeView->studentsRegisterPage($userObj, $semestersArray);
        if(isset($_POST['registerStudent']))
        {
            $this->employeeModel->registerStudent($_REQUEST['id'], $_POST['selectedSemester'], $_POST['regFees']);
            new SystemLog("Employee Register Student", $_SESSION['loggedId']);
        }
    }

    public function subjectsRegistrationForStudentsView($userObj, $subjectsObj, $registrationModel, $registrationDetailsModel)
    {
        $studentSemester = $this->employeeModel->selectStudentSemesterId($_REQUEST['id']);
        $this->employeeView->subjectsRegisterPage("student", $studentSemester, $userObj, $subjectsObj);
        if(isset($_POST['addSubject']))
        {
            $selectedSubject = explode(" ", $_POST['selectedSubject']);
            $regId = $registrationModel->regIdForStudent($_REQUEST['id']);
            $registrationDetailsModel->insertRegistrationDetailsToStudents($regId, $selectedSubject[0]);
            new SystemLog("Employee Add Subject to Student", $_SESSION['loggedId']);
        }
    }

    public function subjectsRegistrationForTeachersView($userObj, $subjectsObj)
    {
        $this->employeeView->subjectsRegisterPage("teacher", null, $userObj, $subjectsObj);
        if(isset($_POST['addSubject']))
        {
            $selectedSubject = explode(" ", $_POST['selectedSubject']);
            $this->employeeModel->insertSubjectsToTeacher($_REQUEST['id'], $selectedSubject[0]);
            $this->employeeModel->insertSemesterToTeacher($_REQUEST['id'], $selectedSubject[count($selectedSubject) - 1]);
            new SystemLog("Employee Add Subject to Teacher", $_SESSION['loggedId']);
        }
    }

    public function studentCertificateView()
    {
        $certificateObj = $this->employeeModel->getCertificate($_REQUEST['id']);
        $this->employeeView->displayStudentCertificate($certificateObj);
        new SystemLog("Employee Generate Certificate", $_SESSION['loggedId']);
        if(isset($_POST['transferStudent']))
        {
            $this->employeeModel->TransferStudentToNextSemester($_REQUEST['id']);
            new SystemLog("Employee Transfer Student to next Semester", $_SESSION['loggedId']);
        }
    }

    public function generateBillView()
    {
        $id = $_REQUEST['StudentIdBill'];
        if($_REQUEST['StudentIdBill'])
        {
            $this->employeeView->showApiBills($this->getBillsFromApi($id));
        }
    }

    public function addItemView($itemModel)
    {
        $this->employeeView->createItem($this->getBillItemsFromApi());
    }

    public function createApiBillItemView()
    {
        $this->sendStudentApiRequest("POST", "/bill-items", $this->getApiBillItemPayloadFromPost());
        new SystemLog("Employee Add API Bill Item", $_SESSION['loggedId']);
        echo "<script>window.location.href='".$this->employeeView->url."&selected=additem';</script>";
        exit;
    }

    public function createBill2View($registrationModel, $itemModel, $regItemModel)
    {
        $selectedStudentId = isset($_POST['selectedStudentId1']) ? explode(" ", $_POST['selectedStudentId1']) : array("");
        if(isset($_POST['itemscheckbox']) && isset($selectedStudentId[0]) && $selectedStudentId[0] != "")
        {
            $this->sendStudentApiRequest("POST", "/bills", array(
                "student_id" => $selectedStudentId[0],
                "item_ids" => $_POST['itemscheckbox']
            ));
            new SystemLog("Employee Create New Bill", $_SESSION['loggedId']);
            echo "<script>window.location.href='".$this->employeeView->url."&selected=bills&StudentIdBill=".$selectedStudentId[0]."';</script>";
            exit;
        }
    }

    public function specificResearchView($reports)
    {
        $this->employeeView->displayApiReportsSummary($this->getReportsSummaryFromApi(), $this->getGradeRecordsFromApi());
    }

    public function specificSearchResultsView($results)
    {
        $this->employeeView->specificSearchResultsView($results);
    }

}
    $employeeController = new EmployeeController();
    $semesterModel = $factory->createObject("semesterModel");
    $employeeModel = $factory->createObject("employeeModel");
    $subjectsModel = $factory->createObject("subjectModel");
    $customizedReports = $factory->createObject("customizedReports");

if(isset($_POST['addApiStudent']))
{
    $employeeController->createApiStudentView();
}

if(isset($_POST['itemadd']))
{
    $employeeController->createApiBillItemView();
}

if(isset($_POST['addSubjectToSystem']) || isset($_POST['addApiSubject']))
{
    $employeeController->createApiSubjectView();
}

if(isset($_POST['submitSemester']) || isset($_POST['addApiSemester']))
{
    $employeeController->createApiSemesterView();
}

if(isset($_POST['updateApiSubject']))
{
    $employeeController->updateApiSubjectView();
}

if(isset($_POST['updateApiSemester']))
{
    $employeeController->updateApiSemesterView();
}

if(isset($_POST['deleteApiSubject']))
{
    $employeeController->deleteApiSubjectView();
}

if(isset($_POST['deleteApiSemester']))
{
    $employeeController->deleteApiSemesterView();
}

if(isset($_POST['updateApiStudent']))
{
    $employeeController->updateApiStudentView();
}

if(isset($_POST['deleteApiStudent']))
{
    $employeeController->deleteApiStudentView();
}

if(isset($_POST['addApiParent']))
{
    $employeeController->createApiParentView();
}

if(isset($_POST['updateApiParent']))
{
    $employeeController->updateApiParentView();
}

if(isset($_POST['deleteApiParent']))
{
    $employeeController->deleteApiParentView();
}

if(isset($_POST['addApiCourse']))
{
    $employeeController->createApiCourseView();
}

if(isset($_POST['updateApiCourse']))
{
    $employeeController->updateApiCourseView();
}

if(isset($_POST['deleteApiCourse']))
{
    $employeeController->deleteApiCourseView();
}

if(isset($_POST['addApiEvent']))
{
    $employeeController->createApiEventView();
}

if(isset($_POST['updateApiEvent']))
{
    $employeeController->updateApiEventView();
}

if(isset($_POST['deleteApiEvent']))
{
    $employeeController->deleteApiEventView();
}

if(isset($_POST['addApiAttendance']))
{
    $employeeController->createApiAttendanceView();
}

if(isset($_POST['updateApiAttendance']))
{
    $employeeController->updateApiAttendanceView();
}

if(isset($_POST['deleteApiAttendance']))
{
    $employeeController->deleteApiAttendanceView();
}

if(isset($_POST['addApiLecturer']))
{
    $employeeController->createApiLecturerView();
}

if(isset($_POST['updateApiLecturer']))
{
    $employeeController->updateApiLecturerView();
}

if(isset($_POST['deleteApiLecturer']))
{
    $employeeController->deleteApiLecturerView();
}

if(isset($_POST['linkApiStudent']))
{
    $employeeController->linkApiStudentView();
}

if(isset($_POST['unlinkApiStudent']))
{
    $employeeController->unlinkApiStudentView();
}

if(isset($_POST['linkApiTeacher']))
{
    $employeeController->linkApiTeacherView();
}

if(isset($_POST['unlinkApiTeacher']))
{
    $employeeController->unlinkApiTeacherView();
}

if(isset($_REQUEST['page']))
{
    if($_REQUEST['page'] == "home")
    {
        $employeeController->showAllTypesView();
    }

    if( $_REQUEST['page'] == "aboutUsEmployee")
    {
        $employeeController->aboutUsView();
    }

    if($_REQUEST['page'] == "addsubjects")
    {
        $employeeController->addSubjectsView($semesterModel->selectAllSemesters());
    }

    if($_REQUEST['page'] == "editApiSubject")
    {
        $employeeController->editApiSubjectView();
    }

    if($_REQUEST['page'] == "editApiSemester")
    {
        $employeeController->editApiSemesterView();
    }

    if($_REQUEST['page'] == "bills")
    {
        $employeeController->billsPageView();
    }

    if($_REQUEST['page'] == "ConfigGrades")
    {
        $employeeController->configGradesView();
    }

    if($_REQUEST['page'] == "GradeRecords")
    {
        $employeeController->gradeRecordsView();
    }

    if($_REQUEST['page'] == "AddNewSemester")
    {
        $employeeController->addNewSemesterView();
    }

    if($_REQUEST['page'] == "SystemMessages")
    {
        $employeeController->systemMessagesView();
    }

    if($_REQUEST['page'] == "PaymentEAV")
    {
        $employeeController->paymentEavView();
    }

    if($_REQUEST['page'] == "PaymentMethods")
    {
        $employeeController->paymentMethodPageView();
    }

    if($_REQUEST['page'] == "PaymentOption")
    {
        $employeeController->paymentOptionView($factory->createObject("lookup"));
    }

    if($_REQUEST['page'] == "QrLink")
    {
        $employeeController->qrLinkView();
    }

    if($_REQUEST['page'] == "Attendance")
    {
        $employeeController->attendanceView();
    }

    if($_REQUEST['page'] == "addApiAttendance")
    {
        $employeeController->addApiAttendanceView();
    }

    if($_REQUEST['page'] == "editApiAttendance")
    {
        $employeeController->editApiAttendanceView();
    }

    if($_REQUEST['page'] == "Events")
    {
        $employeeController->eventsView();
    }

    if($_REQUEST['page'] == "addApiEvent")
    {
        $employeeController->addApiEventView();
    }

    if($_REQUEST['page'] == "editApiEvent")
    {
        $employeeController->editApiEventView();
    }

    if($_REQUEST['page'] == "Courses")
    {
        $employeeController->coursesView();
    }

    if($_REQUEST['page'] == "addApiCourse")
    {
        $employeeController->addApiCourseView();
    }

    if($_REQUEST['page'] == "editApiCourse")
    {
        $employeeController->editApiCourseView();
    }

    if($_REQUEST['page'] == "Lecturers")
    {
        $employeeController->lecturersView();
    }

    if($_REQUEST['page'] == "addApiLecturer")
    {
        $employeeController->addApiLecturerView();
    }

    if($_REQUEST['page'] == "editApiLecturer")
    {
        $employeeController->editApiLecturerView();
    }

    if($_REQUEST['page'] == "StudentLinks")
    {
        $employeeController->studentLinksView();
    }

    if($_REQUEST['page'] == "TeacherLinks")
    {
        $employeeController->teacherLinksView();
    }

    if($_REQUEST['page'] == "Bus")
    {
        $employeeController->busView();
    }

    if($_REQUEST['page'] == "AddNewBus")
    {
        $employeeController->addNewBusView($factory->createObject("lookup"), $factory->createObject("bus"));
    }

    if($_REQUEST['page'] == "AddNewRoute")
    {
        $employeeController->addNewRouteView($factory->createObject("bus"));
    }

    if($_REQUEST['page'] == "SpecificSearch")
    {
        $employeeController->specificResearchView($customizedReports->fetchRows());
        if(isset($_POST['condition']) && isset($_POST['whereGrade']))
        {
            $employeeController->specificSearchResultsView($customizedReports->sqlStatement($_POST['condition'], $_POST['whereGrade']));
        }
    }

    if($_REQUEST['page'] == "addApiStudent")
    {
        $employeeController->addApiStudentView();
    }

    if($_REQUEST['page'] == "editApiStudent")
    {
        $employeeController->editApiStudentView();
    }

    if($_REQUEST['page'] == "addApiParent")
    {
        $employeeController->addApiParentView();
    }

    if($_REQUEST['page'] == "editApiParent")
    {
        $employeeController->editApiParentView();
    }
}

if(isset($_POST['editAboutUs']))
{
    $employeeController->editAboutUsView();
}

if(isset($_REQUEST['selected']))
{
    if($_REQUEST['selected'] == "student")
    {
        $employeeController->studentsView();
    }
    else if($_REQUEST['selected'] == "teacher")
    {
        $employeeController->teachersView();
    }
    else if($_REQUEST['selected'] == "parent")
    {
        $employeeController->parentsView();
    }
    else if($_REQUEST['selected'] == "additem")
    {
      $employeeController->createItemView();
    }
    else if($_REQUEST['selected'] == "fatora")
    {
      $employeeController->createBillView($factory->createObject("itemModel"));
    }
    else if($_REQUEST['selected'] == "bills")
    {
      $employeeController->searchBillsView();
    }
    
}

if(isset($_POST['displayStudentBill']))
{
    $employeeController->displayStudentBillView();
}

if(isset($_POST['nameSearchInput']))
{
    $employeeController->nameSearchInputView();
}

if(isset($_REQUEST['studentname']))
{
  $employeeController->searchWithNameStudentsView();
}
else if(isset($_REQUEST['teachername']))
{
  $employeeController->searchWithNameTeachersView();
}
else if(isset($_REQUEST['parentname']))
{
  $employeeController->searchWithNameParentsView();
}

if(isset($_REQUEST['id']) && !isset($_REQUEST['action']) && !isset($_REQUEST['page']))
{
    $employeeController->displaySpecificUserView();
}

if(isset($_POST['accept']))
{
    $employeeController->acceptUserView();
}

if(isset($_POST['delete']))
{
    /*$query1 = "DELETE FROM users  WHERE id = $id";
    $query2 = "DELETE FROM address  WHERE user_id = $id";
    $query3 = "DELETE FROM identity_images  WHERE user_id = $id";
    $query4 = "DELETE FROM phone_numbers  WHERE user_id = $id";
    $query5 = "DELETE FROM students_data  WHERE user_id = $id";
    if($mydb->query($query1) !== true || $mydb->query($query2) !== true || $mydb->query($query3) !== true || $mydb->query($query4) !== true || $mydb->query($query5) !== true)
    {
        die("Something went wrong.");
    }
    else
    {
        $encID = $_REQUEST['access'];
        $url = $employees[0]->link."?access=".$encID."&page=home";
        header("location:$url");
    }*/
    $employeeController->deleteUserView();
}

if(isset($_POST['reActivate']))
{
    $employeeController->reactivateUserView();
}

if(isset($_REQUEST['action']))
{
    $users = $employeeModel->selectAllUsers();
    $userType = 0;
        $userObj = null;
        for($i = 0; $i < count($users); $i++)
        {
            if($users[$i]->id == $_REQUEST['id'])
            {
                $userType = $users[$i]->user_type;
                $userObj = $users[$i];
                break;
            }
        }

    if($_REQUEST['action'] == "studentregistration")
    {
        $employeeController->studentsRegistrationView($userObj, $semesterModel->selectAllSemesters());
    }

    if($_REQUEST['action'] == "subjectregistrationForStudents")
    {
        $employeeController->subjectsRegistrationForStudentsView($userObj, $subjectsModel->selectAllSubjects(), $factory->createObject("registrationModel"), $factory->createObject("registrationDetailsModel"));
    }
    else if($_REQUEST['action'] == "subjectregistrationForTeachers")
    {
        $employeeController->subjectsRegistrationForTeachersView($userObj, $subjectsModel->selectAllSubjects());
    }

    if($_REQUEST['action'] == "studentcertificate")
    {
        $employeeController->studentCertificateView();
    }
}

if(isset($_REQUEST['StudentIdBill']) && isset($_REQUEST['selected']))
{
    if($_REQUEST['selected'] == "bills")
    {
        /*for($i = 0; $i < count($bill); $i++)
        {
            //$total += $bill[0][$i]->netamount;
        }*/
        $employeeController->generateBillView();
    }
}

if(isset($_POST['createbill']))
{
  $employeeController->createBill2View($factory->createObject("registrationModel"), $factory->createObject("itemModel"), $factory->createObject("registrationItemDetailsModel"));
  //echo "<div class='text-success' style='text-align:center; margin-top:30px; margin-bottom: 60px; '>Successfully Created</div>";   
  //NEED TO MAKE SURE IT HAS SUCCEDDED
}
?>


</body>
</html>

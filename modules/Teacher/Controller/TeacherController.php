<?php
  require_once '../Model/TeacherModel.php';
  require_once '../View/TeacherView.php';
  require_once '../../subjectsModel.php';
  require_once '../../userModel.php';
  require_once '../../gradingMethodModel.php';
  require_once '../../systemLog.php';
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

     <title>Teacher Dashboard</title>

 </head>
   <body>
<?php

class TeacherController
{
  public function homepageView($teacherView)
  {
    $teacherView->homePage();
  }

  public function selectSubjectsAndStudentsView($subjectModel, $teacherView)
  {
    if(isset($_SESSION["loggedId"]))
      {
        $subjectModel->selectTeacherSubjects($_SESSION["loggedId"]);
        $teacherView->showSubjectsAndStudentsPage($subjectModel->subjectsArray);
        if(isset($_POST["displayStudents"]))
        {
          $selectedSubject = explode(" ", $_POST['selectedSubject']);
          $studentsWithSpecificSubject = $subjectModel->getStudentsWithSpecificSubject($selectedSubject[0]);
          $tempSubjectModel = new SubjectModel();
          $whichSubjectSelected = $tempSubjectModel->selectSpecificSubject($selectedSubject[0]);
          $gradingMethod = new GradingMethod();
          $teacherView->displayStudents($selectedSubject[0], $whichSubjectSelected, $studentsWithSpecificSubject, $gradingMethod->getFromDB());
        }
      }
      else
      {
        echo "You're not logging in an ethical way.";
      }
  }

  public function addHomeworkView($teacherView, $subjectModel, $teacherModel)
  {
    $teacherView->homeworkPage($subjectModel->selectTeacherSubjects($_SESSION['loggedId']));
    if(isset($_POST['next']))
        {
          $hwDegree = $_POST['HWdegree'];
          $hwDetails = $_POST['HWdetails'];
          $hwTitle = $_POST['HWtitle'];
          $hwDeadline = $_POST['deadline'];
          $image = 'test';              //TEMP FOR NOW
          $subjectId = explode(" ", $_POST['selectedSubject']);
          $teacherModel->addHomeWork($subjectId[0], $hwTitle, $hwDegree, $hwDetails, $image, $hwDeadline);
          new SystemLog("Teacher Add Homework", $_SESSION['loggedId']);
        }
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
      die("Failed to connect to S3 API.");
    }

    $result = json_decode($response, true);

    if(json_last_error() !== JSON_ERROR_NONE)
    {
      die("Invalid response from S3 API.");
    }

    if(!isset($result['status']) || $result['status'] != "success")
    {
      $message = isset($result['message']) ? $result['message'] : "S3 API error.";
      die($message);
    }

    return $result;
  }

  private function getLinkedApiTeacherId($teacherModel, $teacherView)
  {
    if(isset($_SESSION['apiTeacherId']) && $_SESSION['apiTeacherId'] != "")
    {
      return $_SESSION['apiTeacherId'];
    }

    if(!isset($_SESSION['loggedId']))
    {
      die("Teacher session is missing.");
    }

    $apiTeacherId = $teacherModel->getLinkedApiTeacherIdForLocalTeacher($_SESSION['loggedId']);

    if($apiTeacherId == null)
    {
      $teacher = $teacherModel->getLocalTeacherById($_SESSION['loggedId']);
      $teacherView->teacherApiLinkMissing($teacher);
      return null;
    }

    return $apiTeacherId;
  }

  private function filterByTeacherId($rows, $teacherId)
  {
    $filtered = array();

    for($i = 0; $i < count($rows); $i++)
    {
      if(isset($rows[$i]['teacher_id']) && (string)$rows[$i]['teacher_id'] == (string)$teacherId)
      {
        array_push($filtered, $rows[$i]);
      }
    }

    return $filtered;
  }

  public function apiCoursesView($teacherView, $teacherModel)
  {
    $apiTeacherId = $this->getLinkedApiTeacherId($teacherModel, $teacherView);
    if($apiTeacherId == null)
    {
      return;
    }

    $result = $this->sendApiRequest("/courses");
    $courses = isset($result['data']) && is_array($result['data']) ? $this->filterByTeacherId($result['data'], $apiTeacherId) : array();
    $teacherView->displayApiTeacherCourses($courses);
  }

  public function apiEventsView($teacherView, $teacherModel)
  {
    $apiTeacherId = $this->getLinkedApiTeacherId($teacherModel, $teacherView);
    if($apiTeacherId == null)
    {
      return;
    }

    $result = $this->sendApiRequest("/events");
    $events = isset($result['data']) && is_array($result['data']) ? $this->filterByTeacherId($result['data'], $apiTeacherId) : array();
    $teacherView->displayApiTeacherEvents($events);
  }

  public function apiAttendanceView($teacherView, $teacherModel)
  {
    $apiTeacherId = $this->getLinkedApiTeacherId($teacherModel, $teacherView);
    if($apiTeacherId == null)
    {
      return;
    }

    $result = $this->sendApiRequest("/attendance/lecturer/".rawurlencode($apiTeacherId));
    $attendance = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
    $teacherView->displayApiTeacherAttendance($attendance);
  }

  public function apiGradesView($teacherView, $teacherModel)
  {
    $apiTeacherId = $this->getLinkedApiTeacherId($teacherModel, $teacherView);
    if($apiTeacherId == null)
    {
      return;
    }

    $coursesResult = $this->sendApiRequest("/courses");
    $studentsResult = $this->sendApiRequest("/students");
    $categoriesResult = $this->sendApiRequest("/grade-categories");

    $courses = isset($coursesResult['data']) && is_array($coursesResult['data']) ? $this->filterByTeacherId($coursesResult['data'], $apiTeacherId) : array();
    $students = isset($studentsResult['data']) && is_array($studentsResult['data']) ? $studentsResult['data'] : array();
    $categories = isset($categoriesResult['data']) && is_array($categoriesResult['data']) ? $categoriesResult['data'] : array();
    $gradebook = array();

    for($i = 0; $i < count($courses); $i++)
    {
      $course = $courses[$i];
      $courseCode = isset($course['course_code']) ? strtolower($course['course_code']) : "";

      $courseStudents = array();
      for($j = 0; $j < count($students); $j++)
      {
        if(isset($students[$j]['course_code']) && strtolower($students[$j]['course_code']) == $courseCode)
        {
          $student = $students[$j];
          $student['grades'] = array();

          if(isset($student['student_id']) && isset($course['course_id']))
          {
            $studentGradesResult = $this->sendApiRequest("/grades/student/".rawurlencode($student['student_id']));
            $studentSubjects = isset($studentGradesResult['data']) && is_array($studentGradesResult['data']) ? $studentGradesResult['data'] : array();

            for($g = 0; $g < count($studentSubjects); $g++)
            {
              if(isset($studentSubjects[$g]['course_id']) && (string)$studentSubjects[$g]['course_id'] == (string)$course['course_id'])
              {
                $student['grades'] = isset($studentSubjects[$g]['grades']) && is_array($studentSubjects[$g]['grades']) ? $studentSubjects[$g]['grades'] : array();
                break;
              }
            }
          }

          $courseStudents[] = $student;
        }
      }

      $gradebook[] = array(
        "course" => $course,
        "students" => $courseStudents
      );
    }

    $teacherView->displayApiTeacherGrades($gradebook, $categories, $apiTeacherId);
  }
}

$teacherController = new TeacherController();
$teacherView = new TeacherView();
$teacherModel = new TeacherModel();
$subjectModel = new SubjectModel();

  if(isset($_REQUEST['page']))
  {
    if($_REQUEST['page'] == "Home")
    {
      $teacherController->homepageView($teacherView);
    }

    if($_REQUEST['page'] == "SelectSubjectAndStudent")
    {
      $teacherController->selectSubjectsAndStudentsView($subjectModel, $teacherView);
    }

    if($_REQUEST['page']=="AddHw")
    {
      $teacherController->addHomeworkView($teacherView, $subjectModel, $teacherModel);
    }

    if($_REQUEST['page']=="ApiCourses")
    {
      $teacherController->apiCoursesView($teacherView, $teacherModel);
    }

    if($_REQUEST['page']=="ApiEvents")
    {
      $teacherController->apiEventsView($teacherView, $teacherModel);
    }

    if($_REQUEST['page']=="ApiAttendance")
    {
      $teacherController->apiAttendanceView($teacherView, $teacherModel);
    }

    if($_REQUEST['page']=="ApiGrades")
    {
      $teacherController->apiGradesView($teacherView, $teacherModel);
    }

    else if($_REQUEST['page']=="AddExam")
    {

    }

    else if($_REQUEST['page']=="CorrectHomeWork")
    {

    }
  }


  

  if(isset($_REQUEST['step']))
  { 
    //$teacherView->displayQuestionsStep();   LATER ---
  }

  ?>
  </body>
</html>

<?php
  require_once '../../userModel.php';
  require_once '../../examModel.php';
  require_once '../../homeworkModel.php';
  require_once '../../questionModel.php';
  require_once '../../attendanceInterface.php';
  require_once '../../assignInterface.php';
  require_once '../../userModel.php';


  class TeacherModel extends user
  {
  	public $usersObj;
  	public $studentsArr=array();
    public $teacherType;
    public $mydb;
    
    public function __construct()
    {
      $this->mydb = DB::getInstance();
    }

  	public function selectAllStudents($user = null)
  	{
  		$user=new user();
  		$user->assignAll();
  		$this->usersObj=$user->usersArray;
  		for($i=0;$i<count($this->usersObj);$i++)
  		{
  			if($this->usersObj[$i]->user_type == 3)
  			{
  				array_push($this->studentsArr,$this->usersObj[$i]);
  			}
  		}
  		return $this->studentsArr;
  	}
  	
    public function SelectHomework($hw = null)
    {
        $hw = new homework();
        $hw->assignAll();
        $this->hw = $hw->hwArr;
        for($i=0;$i<count($this->hw);$i++)
        {
          array_push($this->hwArr,$this->hw[$i]);
        }
    }
    

    public function addHomeWork($subject_id, $title, $degree, $details, $image, $deadline)
    {
        $insertHwQuery = "INSERT INTO homework (subject_id, title, degree, details, image, deadline) 
                         VALUES ($subject_id,'$title', $degree,'$details','$image','$deadline')";
      
      if($this->mydb->query($insertHwQuery) !== true)
      {
        echo"something went wrong ";
        die(mysqli_error($this->mydb));
      }
      else
      {
        echo"<div class='text-success' style='text-align:center;'>Successfully added.</style>";
       // header('location:?step=addquestions');    // with respect homework id -- later
      }

    }

    private function ensureApiTeacherLinksTable()
    {
      $query = "CREATE TABLE IF NOT EXISTS api_teacher_links (
        id int(11) NOT NULL AUTO_INCREMENT,
        api_teacher_id varchar(64) NOT NULL,
        local_teacher_id int(11) NOT NULL,
        date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        date_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY api_teacher_id (api_teacher_id),
        UNIQUE KEY local_teacher_id (local_teacher_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

      if($this->mydb->query($query) !== true)
      {
        die(mysqli_error($this->mydb));
      }
    }

    public function getLinkedApiTeacherIdForLocalTeacher($localTeacherId)
    {
      $this->ensureApiTeacherLinksTable();
      $localTeacherId = (int)$localTeacherId;
      $query = "SELECT api_teacher_id FROM api_teacher_links WHERE local_teacher_id = $localTeacherId LIMIT 1";
      $queryResult = mysqli_query($this->mydb, $query);

      if($queryResult && $row = mysqli_fetch_assoc($queryResult))
      {
        return $row['api_teacher_id'];
      }

      return null;
    }

    public function getLocalTeacherById($localTeacherId)
    {
      $localTeacherId = (int)$localTeacherId;
      $user = new user();
      $user->assignAll("WHERE u.id = $localTeacherId AND u.user_type = 2");

      if(count($user->usersArray) > 0)
      {
        return $user->usersArray[0];
      }

      return null;
    }

    /*public function selectExams($exam = null)
  	{
        $exam = new Exam();
        $exam->assignAll();
        $this->exam = $exam->questions;
        for($i=0;$i<count($this->exam);$i++)
        {
          array_push($this->qArray,$this->exam[$i]);
        }
        return $this->qArray;
    }*/
    
    /*public function AddExam()
    {

    }

    public function addAttendancePassword()
    {

    }

    public function addAttendance()
    {
      
    }*/

  }

 ?>

<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<?php

require_once '../Model/TeacherModel.php';

class TeacherView{
  public function __construct()
  {
    $this->navigationControls();
  }

  private function h($value)
  {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
  }

  private function apiValue($row, $key)
  {
    return isset($row[$key]) ? $this->h($row[$key]) : "";
  }

  private function apiDate($row, $key)
  {
    if(!isset($row[$key]) || $row[$key] == "")
    {
      return "";
    }

    $time = strtotime($row[$key]);
    if($time === false)
    {
      return $this->h($row[$key]);
    }

    return date("Y-m-d", $time);
  }

  private function navigationControls()
  {
    ?>
      <div style="position:fixed; top:15px; right:20px; z-index:9999;">
        <a href="TeacherController.php?page=Home">
          <button type="button" class="btn btn-outline-dark btn-sm">Home</button>
        </a>
        <a href="../../../logout.php">
          <button type="button" class="btn btn-outline-danger btn-sm">Logout</button>
        </a>
      </div>
    <?php
  }

  private function backToDashboardButton()
  {
    echo "<div style='text-align:center; margin-top:25px; margin-bottom:35px;'><a href='TeacherController.php?page=Home'><button type='button' class='btn btn-outline-dark'>Back to Dashboard</button></a></div>";
  }

  public function homePage()
  {
    echo "<h1 style='text-align:left;  margin-top:40px; margin-bottom: 150px; margin-left:25px;'>Teacher Dashboard</h1>";
    if(isset($_SESSION['usingCentralApi']) && $_SESSION['usingCentralApi'] && isset($_SESSION['apiRole']) && $_SESSION['apiRole'] == "Lecturer")
    {
      ?>
        <div style="width: 75%; margin: 0 auto;">
          <div class="d-flex justify-content-center flex-wrap" style="margin-top: 50px;">
            <div class="col-sm-2" style="text-align:center;">
              <a href="?page=ApiCourses">
                <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                  <i class="ion-ios-book" style="font-size:50px;"></i>
                  <p class="card-text">S3 Courses</p>
                </button>
              </a>
            </div>

            <div class="col-sm-2" style="text-align:center;">
              <a href="?page=ApiEvents">
                <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                  <i class="ion-ios-calendar" style="font-size:50px;"></i>
                  <p class="card-text">S3 Events</p>
                </button>
              </a>
            </div>

            <div class="col-sm-2" style="text-align:center;">
              <a href="?page=ApiAttendance">
                <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                  <i class="ion-clipboard" style="font-size:50px;"></i>
                  <p class="card-text">S3 Attendance</p>
                </button>
              </a>
            </div>

            <div class="col-sm-2" style="text-align:center;">
              <a href="?page=ApiGrades">
                <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                  <i class="ion-university" style="font-size:50px;"></i>
                  <p class="card-text">S3 Grades</p>
                </button>
              </a>
            </div>
          </div>
        </div>
      <?php
      return;
    }

    echo "<div style='text-align:center;' margin-bottom:50px>";
    ?>
        <div style="width: 75%; margin: 0 auto;">
                    <div class="d-flex justify-content-center flex-wrap" style="margin-top: 50px;">
                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=SelectSubjectAndStudent">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-university" style="font-size:50px;"></i>
                                        <p class="card-text">Add Grades</p>
                                    </button>
                                </a>
                            </div>
                    

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=AddHw">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-list" style="font-size:50px;"></i>
                                        <p class="card-text">Add Assignment</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=ApiCourses">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-book" style="font-size:50px;"></i>
                                        <p class="card-text">S3 Courses</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=ApiEvents">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-calendar" style="font-size:50px;"></i>
                                        <p class="card-text">S3 Events</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=ApiAttendance">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-clipboard" style="font-size:50px;"></i>
                                        <p class="card-text">S3 Attendance</p>
                                    </button>
                                </a>
                            </div>

                            <!--
                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=AddExam">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-document-text" style="font-size:50px;"></i>
                                        <p class="card-text">Add Exam</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="?page=CorrectHomeWork">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-compose" style="font-size:50px;"></i>
                                        <p class="card-text">Correct Homeworks</p>
                                    </button>
                                </a>
                            </div> -->
                    </div>
                </div>
    <?php
    //echo "<h1><a href=$this->url&page=aboutUsEmployee><button type='button' style='width:25%;' class='btn btn-dark'>About us editor</button></a></h1>";
    echo "</div>";
  }
  
  public function homeworkPage($subjectsArr)
  {
    echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Add New Assignment</h1>";

    ?>
      <form action="" style="width: 35%; margin: 50px auto;" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" class="form-control" name="HWtitle"  placeholder="Assignment Title" required>
          </div>

        <div class="form-group">
          <input type="text" class="form-control"  name="HWdegree" placeholder="Assignment Degree" required>
        </div>

        <div class="form-group">
          <textarea class="form-control"  name="HWdetails" rows="4" placeholder="Details" required></textarea>
        </div>

          <div class="form-group">
            <input type="text" name="deadline" class="form-control"  placeholder="Deadline" required>
          </div>

          <div class="form-group">
                  <div class="custom-file">
                      <input type="file" class="custom-file-input" id="customFile1" name="homeworkImage" required>
                      <label class="custom-file-label custom-file-label1" for="customFile1">Upload Image</label>
                      <script>
                          $('#customFile1').on('change',function(){
                              var fileName = $(this).val().replace('C:\\fakepath\\', " ");
                              $(this).next('.custom-file-label1').html(fileName);
                          })
                      </script>
                  </div>
          </div>


          <div class="form-group">
          <select id="inputState" class="form-control" onchange="document.getElementById('selectedSubject').value=this.options[this.selectedIndex].text" required>
                    <?php
                    echo "<option value='' disabled selected>Subjects:</option>";
                        for($i = 0; $i < count($subjectsArr); $i++)
                        {
                            echo "<option>".$subjectsArr[$i]->id." - ".$subjectsArr[$i]->Name." - ".$subjectsArr[$i]->Code." - ".$subjectsArr[$i]->semesterName."</option>";
                        }
                    ?>
                </select>
            <input type="hidden" name="selectedSubject" id="selectedSubject" value="" />
          </div>

        <button type="submit" name="next" class="btn btn-outline-dark" style="width: 100%;" >Next</button>
        </form>
      <?php
  }

  public function displayQuestionsStep()
  {
    echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Add Questions</h1>";

  }

  public function showSubjectsAndStudentsPage($subjectsArray)
  {
    echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Select from your subjects</h1>";
    ?>
    <form action="" style="width: 35%; margin: 50px auto;" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                <select id="inputState" class="form-control" id="selectionId" onchange="document.getElementById('selectedSubject').value=this.options[this.selectedIndex].text" required>
                    <?php
                    echo "<option value='' disabled selected>Subjects:</option>";
                        for($i = 0; $i < count($subjectsArray); $i++)
                        {
                         echo "<option>".$subjectsArray[$i]->id." - ".$subjectsArray[$i]->Name. " - ". $subjectsArray[$i]->Code." - Semester ".$subjectsArray[$i]->semesterId."</option>";
                            
                        }
                    ?>
                </select>
                <input type="hidden" name="selectedSubject" id="selectedSubject" value="" />
            </div>
            <button type="submit" name="displayStudents" class="btn btn-outline-dark" style="width: 100%;" >Display Students</button>
    </form>
            
    <?php
    
  }

  public function displayStudents($selectedSubjectId, $subjectSelected, $students, $gradingMethods)
  {
    if($subjectSelected)
    {
      echo "<h2 style='text-align:center;  margin-top:40px; margin-bottom: 20px; '>$subjectSelected->Name - $subjectSelected->Code - $subjectSelected->semesterName</h2>";
      //$this->gradeDesign1($selectedSubjectId, $subjectSelected, $students, $gradingMethods);
      //$this->gradeDesign2($selectedSubjectId, $subjectSelected, $students, $gradingMethods);
      $this->gradeDesign3($selectedSubjectId, $subjectSelected, $students, $gradingMethods);
     
    }
  }

  public function gradeDesign1($selectedSubjectId, $subjectSelected, $students, $gradingMethods)
  {
    echo "<div style='width:100%; margin-top:40px;' class='list-group'>"; // Design 1
    for($i = 0, $b = 0; $i < count($students); $i++, $b++)
    {
      $fullName = $students[$i]->first_name." ".$students[$i]->second_name." ".$students[$i]->third_name;
      $Id = $students[$i]->id;
      ?>
        <button type='button' data-toggle='modal' data-target='#exampleModal' class='text-dark list-group-item list-group-item-action' onclick="document.getElementById('selectedStudentId').value = <?php echo $Id; ?>; document.getElementById('studentName').value = <?php echo $fullName ?>">
         <strong>NAME: <?php echo $fullName?> <br> ID: <?php echo $Id?></strong>
        </button>
        <?php
    }
    ?>
         <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel"><strong><script>document.getElementById('studentName').value</script>'s Grading</strong></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <div class="modal-body">
                <?php echo "<h5 style='text-align:center; margin-bottom:20px;'>$subjectSelected->Name - $subjectSelected->Code</h5>";?>
                <div class="form-group">
                <table cellspacing="0" cellpadding="0" class="table table-borderless" style="border-radius:20px; margin: 0 auto;  width:85%; box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                <tbody>
                <tr class="text-white bg-danger" style="text-align: center;" >
                    <?php
                        for($i = 0; $i < count($gradingMethods); $i++)
                        {
                          ?>
                              <th><?php echo ucfirst($gradingMethods[$i]['name']); ?></th>
                        <?php
                        }
                        echo "</tr>";
                    ?>
                  <tr style="text-align: center;">
                    <?php
                        for($i = 0; $i < count($gradingMethods); $i++)
                        {
                          ?>
                              <td><input type="number" id="studentGrade" class="form-control" style="border-radius: 20px;"  placeholder="0" name="grade" pattern="[0-9] | [0-9][0-9] | [100]" required></td>
                        <?php
                        }
                        echo "</tr>";
                    ?>
                </tbody>
                </table>
                <input type="hidden" name="selectedGradingMethod" id="selectedGradingMethod" value="" />
            </div>

                </div>
                <div class="modal-footer">
                  
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="hidden" id="selectedStudentId" value="">
                    <input type="hidden" name="selectedSubjectId" id="selectedSubjectId" value=<?php echo $selectedSubjectId; ?>>
                    <input type="hidden" id="teacherId" value="<?php echo $_SESSION['loggedId']; ?>" />
                    <input type="hidden" id="studentName" value="">
                    <button type="submit" name="submitGrade" class="btn btn-danger" onclick="xmlUpdate()">ADD</button>
            </div>
          </div>
        </div>
        </div>
    <?php
    echo "</div>";
  }

  public function gradeDesign2($selectedSubjectId, $subjectSelected, $students, $gradingMethods)
  {
    ?>
        <div class="d-flex flex-wrap align-content-start justify-content-left" >
          <?php
        for($i = 0, $b = 0; $i < count($students); $i++, $b++)
        {
          if($i % 2 == 0)     // let 2 to 1
            $i = 0;

          if($b > 4)
            break;

          $fullName = $students[$i]->first_name." ".$students[$i]->second_name." ".$students[$i]->third_name;
          $Id = $students[$i]->id;
          ?>
      <div style="font-size: 20px; border-radius:20px; border:2px solid rgb(220, 220, 220);  width:460px; box-shadow: 0px 4px 8px rgb(235, 235, 235); margin: 15px; padding:25px;">
          <div style="text-align: center; margin-bottom:15px;"><strong><?php echo $fullName?> - <?php echo $Id?></strong></div>
          <table cellspacing="0" cellpadding="0" class="table table-borderless" style="border-radius:20px;  width:400px; box-shadow: 0px 4px 8px rgb(235, 235, 235);">
              <tbody>
              <tr class="text-white bg-dark" style="text-align: center;" >
                  <?php
                      for($j = 0; $j < count($gradingMethods); $j++)
                      {
                        ?>
                            <th><?php echo ucfirst($gradingMethods[$j]['name']); ?></th>
                      <?php
                      }
                      echo "</tr>";
                  ?>
                <tr style="text-align: center;">
                  <?php
                      for($j = 0; $j < count($gradingMethods); $j++)
                      {
                        ?>
                            <td><input type="number" id="studentGrade" class="form-control" style="border-radius: 20px;"  placeholder="0" name="grade" pattern="[0-9] | [0-9][0-9] | [100]" required></td>
                      <?php
                      }
                      echo "</tr>";
                  ?>
                  
              </tbody>
              </table>
                  <input type="hidden" id="selectedStudentId" value="">
                  <input type="hidden" name="selectedSubjectId" id="selectedSubjectId" value=<?php echo $selectedSubjectId; ?>>
                  <input type="hidden" id="teacherId" value="<?php echo $_SESSION['loggedId']; ?>" />
                  
                  <button type="submit" name="submitGrade" class="btn btn-outline-dark w-100" onclick="xmlUpdate()">ADD</button>
        </div>
        <?php
          
        }
    ?>
    </div>


    <?php
  }

  public function gradeDesign3($selectedSubjectId, $subjectSelected, $students, $gradingMethods)
  {
    if($students)
    {
      ?>
          <h6 class="text-primary" style="text-align: center; margin-bottom: 30px;">Grades are automatically updated when you insert it.</h6>
      <?php
      ?>
          <table cellspacing="0" cellpadding="0" class="table table-borderless" style="margin: 0 auto; width:850px; box-shadow: 0px 4px 8px rgb(235, 235, 235);">
      <?php
        ?>
             <tbody>
          <tr class="text-white bg-dark" style="text-align: center;" >
          <th style="width: 35%;">Name</th>
          <th>ID</th>
              <?php
                  for($j = 0; $j < count($gradingMethods); $j++)
                  {
                    ?>
                        <th><?php echo ucfirst($gradingMethods[$j]['name'])."/".$gradingMethods[$j]['marks']; ?></th>
                  <?php
                  }
                  //echo "<th>Submit</th>";
                  echo "</tr>";
              ?>
              
          </tbody>
        <?php
    }

    for($i = 0; $i < count($students); $i++)
    {
      $fullName = $students[$i]->first_name." ".$students[$i]->second_name." ".$students[$i]->third_name;
      $Id = $students[$i]->id;

      ?>
        <tbody>
          <tr style="text-align: center; border-bottom: 1px solid rgb(230, 230, 230);" >
          <td style="vertical-align: middle;"><strong><?php echo $fullName ?></strong></td>
          <td style="vertical-align: middle;"><strong><?php echo $Id ?></strong></td>
              <?php
                  for($j = 0; $j < count($gradingMethods); $j++)
                  {
                    $gradingMethodId = $gradingMethods[$j]['id'];
                    $placeholder = "Not yet";
                    $v = -10;
                    for($k = 0; $k < count($students[$i]->grade); $k++)
                    {
                      $v = null;
                        if(isset($students[$i]->grade[$k]) && $gradingMethods[$j]['name'] == $students[$i]->gradeMethod[$k])
                        {
                            $placeholder = $students[$i]->grade[$k];
                            $v = $placeholder;
                            break;
                        }
                        else
                        {
                            $placeholder = "Not yet";
                            $v = -10;
                        }

                    }
                      echo "<td class='text-info' style='vertical-align: middle;'><input type='number' id='studentGrade' min='0' class='form-control text-primary ' style='border-radius: 20px;'  placeholder='$placeholder' name='grade' pattern='[0-9] | [0-9][0-9] | [100]' onblur='xmlUpdate(this.value, $Id, $gradingMethodId, $v);'></td>";
                    
                    ?>
                    
                      <input type="hidden" id="studentGrades[]" value="">
                  <?php
                  }
                  ?>
                  <!--<td style="vertical-align: middle;"><button type="submit" name="submitGrade" class="btn btn-outline-dark w-100" style="border-radius: 20px;" onclick="xmlUpdate(<?php echo $Id ?>)">ADD</button></td>-->
                  <?
                  echo "</tr>";
              ?>
              
          </tbody>
      <?php

      
      ?>
        <input type="hidden" name="selectedSubjectId" id="selectedSubjectId" value=<?php echo $selectedSubjectId; ?>>
        <input type="hidden" id="teacherId" value="<?php echo $_SESSION['loggedId']; ?>" />
    <?php
      
    }

    ?>
      </table>

    <?php

  }

  public function teacherApiLinkMissing($teacher = null)
  {
    echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>S3 Teacher Integration</h1>";
    echo "<div class='alert alert-warning' style='width:70%; margin:0 auto; text-align:center;'>";
    echo "This S1 teacher account is not linked to a central S3 lecturer record yet.";

    if($teacher != null)
    {
      echo "<br><br><strong>Local teacher:</strong> ".$this->h($teacher->id." - ".$teacher->first_name." ".$teacher->second_name." ".$teacher->third_name." (".$teacher->email.")");
    }

    echo "<br><br>Ask an Employee/Admin to open <strong>Teacher Links</strong> and connect this local teacher account to the matching S3 lecturer.";
    echo "</div>";
    $this->backToDashboardButton();
  }

  public function displayApiTeacherCourses($courses)
  {
    echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>My S3 Courses</h1>";

    if(count($courses) == 0)
    {
      echo "<h4 class='text-secondary' style='text-align:center;'>No S3 courses are assigned to this linked lecturer.</h4>";
      $this->backToDashboardButton();
      return;
    }
    ?>
      <div class="table-responsive" style="width:90%; margin: 0 auto 35px auto;">
        <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
          <tbody>
            <tr class="text-white bg-dark" style="text-align:center;">
              <th>Course ID</th>
              <th>Code</th>
              <th>Name</th>
              <th>Lecturer</th>
            </tr>
            <?php for($i = 0; $i < count($courses); $i++): ?>
              <tr style="text-align:center; border-bottom:1px solid rgb(230, 230, 230);">
                <td><?php echo $this->apiValue($courses[$i], 'course_id') ?></td>
                <td><?php echo $this->apiValue($courses[$i], 'course_code') ?></td>
                <td><?php echo $this->apiValue($courses[$i], 'course_name') ?></td>
                <td><?php echo $this->apiValue($courses[$i], 'lecturer') ?></td>
              </tr>
            <?php endfor ?>
          </tbody>
        </table>
      </div>
    <?php
    $this->backToDashboardButton();
  }

  public function displayApiTeacherEvents($events)
  {
    echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>My S3 Events</h1>";

    if(count($events) == 0)
    {
      echo "<h4 class='text-secondary' style='text-align:center;'>No S3 events are assigned to this linked lecturer.</h4>";
      $this->backToDashboardButton();
      return;
    }
    ?>
      <div class="table-responsive" style="width:90%; margin: 0 auto 35px auto;">
        <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
          <tbody>
            <tr class="text-white bg-dark" style="text-align:center;">
              <th>Event ID</th>
              <th>Event</th>
              <th>Course</th>
              <th>Room</th>
              <th>Date</th>
            </tr>
            <?php for($i = 0; $i < count($events); $i++): ?>
              <tr style="text-align:center; border-bottom:1px solid rgb(230, 230, 230);">
                <td><?php echo $this->apiValue($events[$i], 'event_id') ?></td>
                <td><?php echo $this->apiValue($events[$i], 'event_name') ?></td>
                <td><?php echo $this->apiValue($events[$i], 'course_code')." - ".$this->apiValue($events[$i], 'course_name') ?></td>
                <td><?php echo $this->apiValue($events[$i], 'room') ?></td>
                <td><?php echo $this->apiDate($events[$i], 'event_date') ?></td>
              </tr>
            <?php endfor ?>
          </tbody>
        </table>
      </div>
    <?php
    $this->backToDashboardButton();
  }

  public function displayApiTeacherAttendance($attendance)
  {
    echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>My S3 Attendance Records</h1>";

    if(count($attendance) == 0)
    {
      echo "<h4 class='text-secondary' style='text-align:center;'>No S3 attendance records were found for this linked lecturer.</h4>";
      $this->backToDashboardButton();
      return;
    }
    ?>
      <div class="table-responsive" style="width:95%; margin: 0 auto 35px auto;">
        <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
          <tbody>
            <tr class="text-white bg-dark" style="text-align:center;">
              <th>ID</th>
              <th>Student No.</th>
              <th>Student</th>
              <th>Event</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Status</th>
              <th>Time In</th>
              <th>Remarks</th>
            </tr>
            <?php for($i = 0; $i < count($attendance); $i++): ?>
              <tr style="text-align:center; border-bottom:1px solid rgb(230, 230, 230);">
                <td><?php echo $this->apiValue($attendance[$i], 'attendance_id') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'student_number') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'student_name') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'event_name') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'subject_code')." - ".$this->apiValue($attendance[$i], 'subject_name') ?></td>
                <td><?php echo $this->apiDate($attendance[$i], 'attendance_date') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'status') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'time_in') ?></td>
                <td><?php echo $this->apiValue($attendance[$i], 'remarks') ?></td>
              </tr>
            <?php endfor ?>
          </tbody>
        </table>
      </div>
    <?php
    $this->backToDashboardButton();
  }

  public function displayApiTeacherGrades($gradebook, $categories, $teacherId)
  {
    echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>S3 Grade Input</h1>";
    echo "<h6 class='text-primary' style='text-align:center; margin-bottom:30px;'>Grades are saved directly to the central API database.</h6>";

    if(count($gradebook) == 0)
    {
      echo "<h4 class='text-secondary' style='text-align:center;'>No S3 courses are assigned to this lecturer.</h4>";
      $this->backToDashboardButton();
      return;
    }

    if(count($categories) == 0)
    {
      echo "<h4 class='text-danger' style='text-align:center;'>No S3 grade categories found.</h4>";
      $this->backToDashboardButton();
      return;
    }

    for($i = 0; $i < count($gradebook); $i++)
    {
      $course = $gradebook[$i]['course'];
      $students = $gradebook[$i]['students'];
      $courseId = isset($course['course_id']) ? $this->apiValue($course, 'course_id') : "";
      ?>
        <h3 style="text-align:center; margin-top:35px;"><?php echo $this->apiValue($course, 'course_code')." - ".$this->apiValue($course, 'course_name') ?></h3>
        <?php if(count($students) == 0): ?>
          <h5 class="text-secondary" style="text-align:center; margin:20px 0;">No students found for this S3 course.</h5>
        <?php else: ?>
          <div class="table-responsive" style="width:95%; margin:20px auto 45px auto;">
            <table class="table table-borderless table-hover" style="box-shadow:0px 4px 8px rgb(235,235,235);">
              <tbody>
                <tr class="text-white bg-dark" style="text-align:center;">
                  <th>Student No.</th>
                  <th>Student</th>
                  <?php for($c = 0; $c < count($categories); $c++): ?>
                    <th><?php echo ucfirst($this->apiValue($categories[$c], 'category_name'))." / ".$this->apiValue($categories[$c], 'max_score') ?></th>
                  <?php endfor ?>
                </tr>
                <?php for($s = 0; $s < count($students); $s++): ?>
                  <tr style="text-align:center; border-bottom:1px solid rgb(230,230,230);">
                    <td style="vertical-align:middle;"><?php echo $this->apiValue($students[$s], 'student_number') ?></td>
                    <td style="vertical-align:middle;"><strong><?php echo $this->apiValue($students[$s], 'first_name')." ".$this->apiValue($students[$s], 'last_name') ?></strong></td>
                    <?php for($c = 0; $c < count($categories); $c++): ?>
                      <?php
                        $placeholder = "Not yet";
                        $grades = isset($students[$s]['grades']) && is_array($students[$s]['grades']) ? $students[$s]['grades'] : array();

                        for($g = 0; $g < count($grades); $g++)
                        {
                          if(isset($grades[$g]['category_id']) && isset($categories[$c]['category_id']) && (string)$grades[$g]['category_id'] == (string)$categories[$c]['category_id'])
                          {
                            $placeholder = isset($grades[$g]['grade_score']) ? $this->apiValue($grades[$g], 'grade_score') : "Not yet";
                            break;
                          }
                        }
                      ?>
                      <td style="vertical-align:middle;">
                        <input type="number"
                               min="0"
                               max="<?php echo $this->apiValue($categories[$c], 'max_score') ?>"
                               class="form-control text-primary"
                               style="border-radius:20px;"
                               placeholder="<?php echo $placeholder ?>"
                               onblur="xmlUpdateApiGrade(this.value, <?php echo $this->apiValue($students[$s], 'student_id') ?>, <?php echo $courseId ?>, <?php echo $this->apiValue($categories[$c], 'category_id') ?>, <?php echo $teacherId ?>);">
                      </td>
                    <?php endfor ?>
                  </tr>
                <?php endfor ?>
              </tbody>
            </table>
          </div>
        <?php endif ?>
      <?php
    }

    $this->backToDashboardButton();
  }
  
}

  ?>

  <script>

    function beforeXmlUpdate(studentGrade, selectedStudentId, gradeMethodId, v)
    {
      document.getElementById("studentGrade").onblur = function() {xmlUpdate(studentGrade, selectedStudentId, gradeMethodId, v)};
    }

     function xmlUpdate(studentGrade, selectedStudentId, gradeMethodId, v){
        var selectedSubject = document.getElementById('selectedSubjectId').value;
        //var gradingMethod = document.getElementById('selectedGradingMethod').value;///
        var teacherId = document.getElementById('teacherId').value;
        if(parseInt(studentGrade) >= 0)
        {
            var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                  //alert(this.responseText);
              } else {
                  //alert(this.status + " " + this.readyState);
              }
          };
          xmlhttp.open("GET", "update_grade.php?ss=" + selectedSubject + "&ssi=" + selectedStudentId + "&sg=" + studentGrade + "&gmid=" + gradeMethodId + "&tid=" + teacherId + "&v=" + v, true);
          xmlhttp.send();
        }
        else
        {
          //alert("Grade must >= 0");
          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                  //alert(this.responseText);
              } else {
                  //alert(this.status + " " + this.readyState);
              }
          };
          xmlhttp.open("GET", "update_grade.php?ss=" + selectedSubject + "&ssi=" + selectedStudentId + "&sg=" + v + "&gmid=" + gradeMethodId + "&tid=" + teacherId, true);
          xmlhttp.send();
        }
      }

      function xmlUpdateApiGrade(studentGrade, selectedStudentId, selectedSubjectId, gradeCategoryId, teacherId){
        if(studentGrade === "" || parseFloat(studentGrade) < 0)
        {
          return;
        }

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Saved through S3 API.
            }
        };
        xmlhttp.open("GET", "update_grade.php?api=1&ss=" + selectedSubjectId + "&ssi=" + selectedStudentId + "&sg=" + studentGrade + "&gmid=" + gradeCategoryId + "&tid=" + teacherId, true);
        xmlhttp.send();
      }
  </script>

  <style>
    table { border-collapse: separate; }
    td { border: solid 1px #000; }
    tr:first-child th:first-child { border-top-left-radius: 15px; }
    tr:first-child th:last-child { border-top-right-radius: 15px; }
  </style>




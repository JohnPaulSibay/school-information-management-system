<?php
    require_once '../Model/employeeModel.php';

    class EmployeeView
    {
        public $employeeModel;
        public $employees;
        public $url;
        public $encID;
        public $imageURL;
        
        function __construct()
        {
            $this->employeeModel = new EmployeeModel();
            $this->employees = $this->employeeModel->selectAllEmployees();
            $this->encID = isset($_REQUEST['access']) ? $_REQUEST['access'] : "api_session";
            $this->url = "/PharaohSchoolSystem/modules/Employee/Controller/employeeController.php?access=".$this->encID;
            if(count($this->employees) > 0 && isset($this->employees[0]->link))
            {
                $this->url = $this->employees[0]->link."?access=".$this->encID;
            }
            $this->imageURL = "../../../";
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
            if (!isset($row[$key]) || $row[$key] == "") {
                return "";
            }

            $time = strtotime($row[$key]);
            if ($time === false) {
                return $this->h($row[$key]);
            }

            return date("Y-m-d", $time);
        }

        public function displayReadOnlyPortalNotice($returnUrl = null)
        {
            $backUrl = $returnUrl == null ? $this->url."&page=home" : $returnUrl;
            echo "<h1 style='text-align:center; margin-top:60px; margin-bottom:25px;'>S1 School Portal</h1>";
            echo "<div class='alert alert-info' style='width:70%; margin:0 auto; text-align:center;'>";
            echo "This PHP system is configured as a viewing portal. Create, update, and delete operations are handled by the C# Attendance Management System through the central API.";
            echo "</div>";
            echo "<div style='text-align:center; margin-top:25px;'><a href='".$this->h($backUrl)."'><button type='button' class='btn btn-outline-dark'>Back</button></a></div>";
        }

        private function localStudentOptionLabel($student)
        {
            return $student->id." - ".$student->first_name." ".$student->second_name." ".$student->third_name." (".$student->email.")";
        }

        private function localTeacherOptionLabel($teacher)
        {
            return $teacher->id." - ".$teacher->first_name." ".$teacher->second_name." ".$teacher->third_name." (".$teacher->email.")";
        }

        private function navigationControls()
        {
            ?>
            <div style="position:fixed; top:15px; right:20px; z-index:9999;">
                <a href="<?php echo $this->url ?>&page=home">
                    <button type="button" class="btn btn-outline-dark btn-sm">Home</button>
                </a>
                <a href="../../../logout.php">
                    <button type="button" class="btn btn-outline-danger btn-sm">Logout</button>
                </a>
            </div>
            <?php
        }

        public function showAllTypes($noOfStudents, $noOfTeachers, $noOfParents)
        {
            echo "<h1 style='text-align:left; margin-left:40px; margin-top:40px; margin-bottom: 100px;'>Employee cPanel</h1>";
            ?>
            <div style="width:100%;">
                <div class="d-flex justify-content-center flex-wrap" style="margin: 0 auto;">
                    <div class="col-sm-3">
                        <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $noOfStudents ?></h3>
                            <i class="ion-ios-people" style="font-size:30px;"></i>
                            <p class="card-text">Total Number of Students.</p>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $noOfTeachers ?></h3>
                            <i class="ion-person-stalker" style="font-size:30px;"></i>
                            <p class="card-text">Total Number of Lecturers.</p>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $noOfParents ?></h3>
                            <i class="ion-ios-person" style="font-size:30px;"></i>
                            <p class="card-text">Total Number of Parents.</p>
                        </div>
                        </div>
                    </div>
                
                </div>

                <div style="width: 75%; margin: 0 auto;">
                    <div class="d-flex justify-content-center flex-wrap" style="margin-top: 50px;">
                            <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&selected=student">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-people" style="font-size:50px;"></i>
                                        <p class="card-text">Students</p>
                                    </button>
                                </a>
                            </div>
                    

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&page=Lecturers">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-person-stalker" style="font-size:50px;"></i>
                                        <p class="card-text">Lecturers</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&selected=parent">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-person" style="font-size:50px;"></i>
                                        <p class="card-text">Parents</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&page=addsubjects">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-android-funnel" style="font-size:50px;"></i>
                                        <p class="card-text">Subjects</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&page=bills">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-ios-paper" style="font-size:50px;"></i>
                                        <p class="card-text">Bills</p>
                                    </button>
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&page=AddNewSemester">
                                <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                <i class="ion-ios-bookmarks" style="font-size:50px;"></i>
                                <p class="card-text">Semesters</p>
                                </button>
                                </a>
                            </div>
                            
                            
                    </div>
                </div>

                <div style="width: 75%; margin: 0 auto;">
                    <div class="d-flex justify-content-left flex-wrap" style="margin-top: 50px;">

                    <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=Bus">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="fa fa-bus-alt" style="font-size:50px;"></i>
                            <p class="card-text">Bus</p>
                           </button>
                            </a>
                      </div>

                        <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=ConfigGrades">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="ion-university" style="font-size:50px;"></i>
                            <p class="card-text">Grades</p>
                           </button>
                            </a>
                      </div>

                      <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=PaymentEAV">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="ion-card" style="font-size:50px;"></i>
                            <p class="card-text">Payment</p>
                           </button>
                            </a>
                      </div>

                      <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=SpecificSearch">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="ion-ios-search-strong" style="font-size:50px;"></i>
                            <p class="card-text">Specific Search</p>
                           </button>
                            </a>
                      </div>

                      <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=SystemMessages">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="ion-ios-cog" style="font-size:50px;"></i>
                            <p class="card-text">System Messages</p>
                           </button>
                            </a>
                      </div>
                      

                      <div class="col-sm-2" style="text-align:center;">
                                <a href="<?php echo $this->url ?>&page=aboutUsEmployee">
                                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                        <i class="ion-edit" style="font-size:50px;"></i>
                                        <p class="card-text">About Us</p>
                                    </button>
                                </a>
                            </div>

                    </div>
                </div>

                <div style="width: 75%; margin: 0 auto;">
                    <div class="d-flex justify-content-left flex-wrap" style="margin-top: 50px;">

                    <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=QrLink">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="fa fa-qrcode" style="font-size:50px;"></i>
                            <p class="card-text">QR-Link</p>
                           </button>
                            </a>
                      </div>

                      <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=Attendance">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="fa fa-calendar-check" style="font-size:50px;"></i>
                            <p class="card-text">Attendance</p>
                           </button>
                            </a>
                      </div>

                      <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=Events">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="fa fa-calendar-alt" style="font-size:50px;"></i>
                            <p class="card-text">Events</p>
                           </button>
                            </a>
                      </div>

                      <div class="col-sm-2" style="text-align:center;">
                            <a href="<?php echo $this->url ?>&page=Courses">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                             <i class="fa fa-book" style="font-size:50px;"></i>
                            <p class="card-text">Courses</p>
                           </button>
                            </a>
                      </div>

                    </div>
                </div>
                
            </div>


            <?php

            
        }

        public function billsPage()
        {
            echo "<h1 style='font-size:50px; text-align:center;  margin-top:200px;'>Bills</h1>";
            echo "<div style='margin-top:50px; text-align:center;'>";
            echo "<div><a href=$this->url&selected=additem><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'>Add Item</button></a></div>";
            echo "<div><a href=$this->url&selected=fatora><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'>Add Bill</button></a></div>";
            echo "<div><a href=$this->url&selected=bills><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'>View Bills</button></a></div>";
            echo "</div>";
        }

        public function displayAll($userType, $headerTitle)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Pharaohs $headerTitle</h1>";
            ?>
                <form action=" " method="POST" style="margin-left: 10px; margin-bottom:0px;">
                <div class="form-row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Search by name" name="nameSearchInput" required pattern="[a-z | A-Z]+">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-outline-dark">Search</button>
                    </div>
                </div>
                </form>
            <?php

            if($headerTitle == "Students")
            {
                ?>
                    <div style="text-align:right; width:95%; margin:20px auto 0 auto;">
                        <a href="<?php echo $this->h($this->url) ?>&page=addApiStudent">
                            <button type="button" class="btn btn-outline-success">Add Student</button>
                        </a>
                    </div>
                <?php
            }

            if($headerTitle == "Parents")
            {
                ?>
                    <div style="text-align:right; width:95%; margin:20px auto 0 auto;">
                        <a href="<?php echo $this->h($this->url) ?>&page=addApiParent">
                            <button type="button" class="btn btn-outline-success">Add Parent</button>
                        </a>
                    </div>
                <?php
            }

            
            echo "<div style='width:100%; margin-top:40px;' class='list-group'>";
            for($i = 0; $i < count($userType); $i++)
            {
                $fullName = $userType[$i]->first_name." ".$userType[$i]->second_name." ".$userType[$i]->third_name;
                $Id = $userType[$i]->id;

                if((isset($userType[$i]->student_id) && !isset($userType[$i]->parent_id)) || isset($userType[$i]->student_number))
                {
                    $displayId = isset($userType[$i]->student_number) && $userType[$i]->student_number != "" ? $userType[$i]->student_number : $Id;
                    $apiDetails = "<br> COURSE: ".$this->h($userType[$i]->course_code)." - ".$this->h($userType[$i]->section_name)."<br> YEAR: ".$this->h($userType[$i]->year_level)." | STATUS: ".$this->h($userType[$i]->status);
                    echo "<a target='_blank' href='".$this->h($this->url)."&id=".rawurlencode((string)$userType[$i]->id)."&source=apiStudent' style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>NAME: ".$this->h($fullName)." <br> ID: ".$this->h($displayId).$apiDetails."</strong></button></a>";
                    continue;
                }

                if(isset($userType[$i]->parent_id))
                {
                    $apiDetails = "<br> EMAIL: ".$this->h($userType[$i]->email)."<br> CONTACT: ".$this->h($userType[$i]->contact_number);
                    if(isset($userType[$i]->student_name) && $userType[$i]->student_name != "")
                    {
                        $apiDetails .= "<br> STUDENT: ".$this->h($userType[$i]->student_name);
                    }

                    echo "<a target='_blank' href='".$this->h($this->url)."&id=".rawurlencode((string)$userType[$i]->parent_id)."&source=apiParent' style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>NAME: ".$this->h($fullName)." <br> ID: ".$this->h($userType[$i]->parent_id).$apiDetails."</strong></button></a>";
                    continue;
                }

                if($userType[$i]->accepted == 0)
                {
                    echo "<a target=”_blank” href=$this->url&id=".$userType[$i]->id." style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-secondary list-group-item list-group-item-action'><strong>NAME: $fullName <br> ID: $Id</strong></button></a>";
                }
                else
                {
                    if($userType[$i]->isDeleted == 0)
                        echo "<a target=”_blank” href=$this->url&id=".$userType[$i]->id." style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>NAME: $fullName <br> ID: $Id</strong></button></a>";
                    else
                        echo "<a target=”_blank” href=$this->url&id=".$userType[$i]->id." style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-danger list-group-item list-group-item-action'><strong>NAME: $fullName [DELETED]<br> ID: $Id</strong></button></a>";
                }
            }
            echo "</div>";
        }

        public function displayApiAttendance($attendance)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; margin-bottom:40px;'>Central Attendance Records</h1>";

            if(count($attendance) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No attendance records found in the Student API.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:95%; margin: 0 auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Attendance ID</th>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Time In</th>
                                <th>Remarks</th>
                            </tr>
                            <?php for($i = 0; $i < count($attendance); $i++): ?>
                                <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->apiValue($attendance[$i], 'attendance_id') ?></td>
                                    <td><?php echo $this->apiValue($attendance[$i], 'student_name') ?></td>
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
        }

        public function apiAttendanceForm($attendance, $students, $title, $submitName)
        {
            $isEdit = $attendance != null;
            $attendanceDate = $isEdit && isset($attendance['attendance_date']) && $attendance['attendance_date'] != "" ? date("Y-m-d", strtotime($attendance['attendance_date'])) : "";
            $timeIn = $isEdit && isset($attendance['time_in']) ? $this->h($attendance['time_in']) : "";
            $remarks = $isEdit && isset($attendance['remarks']) ? $this->h($attendance['remarks']) : "";
            $status = $isEdit && isset($attendance['status']) ? $attendance['status'] : "Present";
            $currentStudentId = $isEdit && isset($attendance['student_id']) ? (string)$attendance['student_id'] : "";
            ?>
            <h1 style="text-align:center; margin-top:35px; margin-bottom:35px;"><?php echo $this->h($title) ?></h1>
            <form action="" method="POST" style="width:45%; margin: 0 auto;">
                <div class="form-group">
                    <label>Student</label>
                    <select class="form-control" name="student_id" required>
                        <option value="" disabled <?php echo $currentStudentId == "" ? "selected" : "" ?>>Select student</option>
                        <?php for($i = 0; $i < count($students); $i++): ?>
                            <?php $selected = (string)$students[$i]->student_id == $currentStudentId ? "selected" : ""; ?>
                            <option value="<?php echo $this->h($students[$i]->student_id) ?>" <?php echo $selected ?>>
                                <?php echo $this->h($students[$i]->student_number." - ".$students[$i]->first_name." ".$students[$i]->second_name) ?>
                            </option>
                        <?php endfor ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="attendance_date" value="<?php echo $attendanceDate ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" required>
                        <option value="Present" <?php echo strtolower($status) == "present" ? "selected" : "" ?>>Present</option>
                        <option value="Absent" <?php echo strtolower($status) == "absent" ? "selected" : "" ?>>Absent</option>
                        <option value="Late" <?php echo strtolower($status) == "late" ? "selected" : "" ?>>Late</option>
                        <option value="Excused" <?php echo strtolower($status) == "excused" ? "selected" : "" ?>>Excused</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Time In</label>
                    <input type="time" class="form-control" name="time_in" value="<?php echo $timeIn ?>">
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" name="remarks" rows="3"><?php echo $remarks ?></textarea>
                </div>
                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>
                <div style="text-align:center; margin-top:20px;">
                    <a href="<?php echo $this->h($this->url) ?>&page=Attendance">Back to Attendance</a>
                </div>
            </form>
            <?php
        }

        public function displayApiEvents($events)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; margin-bottom:40px;'>Central Events</h1>";

            if(count($events) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No events found in the Student API.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:95%; margin: 0 auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Event ID</th>
                                <th>Event Name</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Room</th>
                                <th>Event Date</th>
                            </tr>
                            <?php for($i = 0; $i < count($events); $i++): ?>
                                <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->apiValue($events[$i], 'event_id') ?></td>
                                    <td><?php echo $this->apiValue($events[$i], 'event_name') ?></td>
                                    <td><?php echo $this->apiValue($events[$i], 'course_code') ?></td>
                                    <td><?php echo $this->apiValue($events[$i], 'course_name') ?></td>
                                    <td><?php echo $this->apiValue($events[$i], 'room') ?></td>
                                    <td><?php echo $this->apiDate($events[$i], 'event_date') ?></td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php
        }

        public function apiEventForm($event, $courses, $title, $submitName)
        {
            $isEdit = $event != null;
            $eventName = $isEdit && isset($event['event_name']) ? $this->h($event['event_name']) : "";
            $room = $isEdit && isset($event['room']) ? $this->h($event['room']) : "";
            $eventDate = $isEdit && isset($event['event_date']) && $event['event_date'] != "" ? date("Y-m-d", strtotime($event['event_date'])) : "";
            $currentCourseCode = $isEdit && isset($event['course_code']) ? $event['course_code'] : "";
            ?>
            <h1 style="text-align:center; margin-top:35px; margin-bottom:35px;"><?php echo $this->h($title) ?></h1>
            <form action="" method="POST" style="width:45%; margin: 0 auto;">
                <div class="form-group">
                    <label>Event Name</label>
                    <input type="text" class="form-control" name="event_name" value="<?php echo $eventName ?>" required>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <select class="form-control" name="course_id" required>
                        <option value="" disabled <?php echo $currentCourseCode == "" ? "selected" : "" ?>>Select course</option>
                        <?php for($i = 0; $i < count($courses); $i++): ?>
                            <?php $selected = isset($courses[$i]['course_code']) && $courses[$i]['course_code'] == $currentCourseCode ? "selected" : ""; ?>
                            <option value="<?php echo $this->apiValue($courses[$i], 'course_id') ?>" <?php echo $selected ?>>
                                <?php echo $this->apiValue($courses[$i], 'course_code') ?> - <?php echo $this->apiValue($courses[$i], 'course_name') ?>
                            </option>
                        <?php endfor ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Room</label>
                    <input type="text" class="form-control" name="room" value="<?php echo $room ?>">
                </div>
                <div class="form-group">
                    <label>Event Date</label>
                    <input type="date" class="form-control" name="event_date" value="<?php echo $eventDate ?>" required>
                </div>
                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>
                <div style="text-align:center; margin-top:20px;">
                    <a href="<?php echo $this->h($this->url) ?>&page=Events">Back to Events</a>
                </div>
            </form>
            <?php
        }

        public function displayApiCourses($courses)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; margin-bottom:40px;'>Central Courses</h1>";

            if(count($courses) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No courses found in S3.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:95%; margin: 0 auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Course ID</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Lecturer</th>
                            </tr>
                            <?php for($i = 0; $i < count($courses); $i++): ?>
                                <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
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
        }

        public function apiCourseForm($course, $lecturers, $title, $submitName)
        {
            $isEdit = $course != null;
            $courseCode = $isEdit && isset($course['course_code']) ? $this->h($course['course_code']) : "";
            $courseName = $isEdit && isset($course['course_name']) ? $this->h($course['course_name']) : "";
            $currentLecturer = $isEdit && isset($course['lecturer']) ? $course['lecturer'] : "";
            ?>
            <h1 style="text-align:center; margin-top:35px; margin-bottom:35px;"><?php echo $this->h($title) ?></h1>
            <form action="" method="POST" style="width:45%; margin: 0 auto;">
                <div class="form-group">
                    <label>Course Code</label>
                    <input type="text" class="form-control" name="course_code" value="<?php echo $courseCode ?>" required>
                </div>
                <div class="form-group">
                    <label>Course Name</label>
                    <input type="text" class="form-control" name="course_name" value="<?php echo $courseName ?>" required>
                </div>
                <div class="form-group">
                    <label>Lecturer</label>
                    <select class="form-control" name="teacher_id" required>
                        <option value="" disabled <?php echo $currentLecturer == "" ? "selected" : "" ?>>Select lecturer</option>
                        <?php for($i = 0; $i < count($lecturers); $i++): ?>
                            <?php $selected = isset($lecturers[$i]['teacher_name']) && $lecturers[$i]['teacher_name'] == $currentLecturer ? "selected" : ""; ?>
                            <option value="<?php echo $this->apiValue($lecturers[$i], 'teacher_id') ?>" <?php echo $selected ?>>
                                <?php echo $this->apiValue($lecturers[$i], 'teacher_name') ?>
                            </option>
                        <?php endfor ?>
                    </select>
                </div>
                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>
                <div style="text-align:center; margin-top:20px;">
                    <a href="<?php echo $this->h($this->url) ?>&page=Courses">Back to Courses</a>
                </div>
            </form>
            <?php
        }

        public function displayApiLecturers($lecturers)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; margin-bottom:40px;'>Central Lecturers</h1>";
            ?>
                <div style="text-align:right; width:95%; margin:0 auto 20px auto;">
                    <a href="<?php echo $this->h($this->url) ?>&page=addApiLecturer">
                        <button type="button" class="btn btn-outline-success">Add Lecturer</button>
                    </a>
                </div>
            <?php

            if(count($lecturers) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No lecturers found in S3.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:95%; margin: 0 auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Lecturer ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Actions</th>
                            </tr>
                            <?php for($i = 0; $i < count($lecturers); $i++): ?>
                                <?php $teacherId = isset($lecturers[$i]['teacher_id']) ? $lecturers[$i]['teacher_id'] : ""; ?>
                                <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->apiValue($lecturers[$i], 'teacher_id') ?></td>
                                    <td><?php echo $this->apiValue($lecturers[$i], 'teacher_name') ?></td>
                                    <td><?php echo $this->apiValue($lecturers[$i], 'email') ?></td>
                                    <td><?php echo $this->apiValue($lecturers[$i], 'contact_number') ?></td>
                                    <td>
                                        <a href="<?php echo $this->h($this->url) ?>&page=editApiLecturer&id=<?php echo rawurlencode((string)$teacherId) ?>">
                                            <button type="button" class="btn btn-outline-primary btn-sm">Edit</button>
                                        </a>
                                        <form action="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$teacherId) ?>" method="POST" style="display:inline-block; margin-left:6px;" onsubmit="return confirm('Delete this lecturer from the central API database? Make sure no courses are assigned to this lecturer first.');">
                                            <button type="submit" name="deleteApiLecturer" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php
        }

        public function apiLecturerForm($lecturer, $title, $submitName)
        {
            $isEdit = $lecturer != null;
            $teacherName = $isEdit && isset($lecturer['teacher_name']) ? $this->h($lecturer['teacher_name']) : "";
            $email = $isEdit && isset($lecturer['email']) ? $this->h($lecturer['email']) : "";
            $contactNumber = $isEdit && isset($lecturer['contact_number']) ? $this->h($lecturer['contact_number']) : "";
            ?>
            <h1 style="text-align:center; margin-top:35px; margin-bottom:35px;"><?php echo $this->h($title) ?></h1>
            <form action="" method="POST" style="width:45%; margin: 0 auto;">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="teacher_name" value="<?php echo $teacherName ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $email ?>">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" value="<?php echo $contactNumber ?>">
                </div>
                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>
                <div style="text-align:center; margin-top:20px;">
                    <a href="<?php echo $this->h($this->url) ?>&page=Lecturers">Back to Lecturers</a>
                </div>
            </form>
            <?php
        }

        public function displayApiStudentLinks($links, $localStudents = array())
        {
            $linkedCount = 0;

            for($i = 0; $i < count($links); $i++)
            {
                if($links[$i]->localStudent != null)
                {
                    $linkedCount++;
                }
            }

            echo "<h1 style='text-align:center; margin-top:35px; margin-bottom:15px;'>Student Account Links</h1>";
            echo "<h5 class='text-secondary' style='text-align:center; margin-bottom:40px;'>Linked ".$this->h($linkedCount)." of ".$this->h(count($links))." central API students</h5>";

            if(count($links) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No central API students found.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:95%; margin: 0 auto 35px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>API ID</th>
                                <th>Student Number</th>
                                <th>API Name</th>
                                <th>Course</th>
                                <th>API Email</th>
                                <th>Status</th>
                                <th>Local Account</th>
                                <th>Actions</th>
                            </tr>
                            <?php for($i = 0; $i < count($links); $i++): ?>
                                <?php
                                    $apiStudent = $links[$i]->apiStudent;
                                    $localStudent = $links[$i]->localStudent;
                                    $isExplicit = isset($links[$i]->isExplicit) && $links[$i]->isExplicit;
                                    $apiName = $apiStudent->first_name." ".$apiStudent->second_name;
                                    $course = $apiStudent->course_code." - ".$apiStudent->section_name;
                                ?>
                                <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->h($apiStudent->student_id) ?></td>
                                    <td><?php echo $this->h($apiStudent->student_number) ?></td>
                                    <td><?php echo $this->h($apiName) ?></td>
                                    <td><?php echo $this->h($course) ?></td>
                                    <td><?php echo $this->h($apiStudent->email) ?></td>
                                    <?php if($localStudent == null): ?>
                                        <td class="text-warning"><strong>Not linked</strong></td>
                                        <td>
                                            <form action="" method="POST" class="form-inline justify-content-center">
                                                <input type="hidden" name="apiStudentId" value="<?php echo $this->h($apiStudent->student_id) ?>">
                                                <select class="form-control form-control-sm" name="localStudentId" required style="max-width:260px;">
                                                    <option value="" disabled selected>Select local student</option>
                                                    <?php for($j = 0; $j < count($localStudents); $j++): ?>
                                                        <option value="<?php echo $this->h($localStudents[$j]->id) ?>"><?php echo $this->h($this->localStudentOptionLabel($localStudents[$j])) ?></option>
                                                    <?php endfor ?>
                                                </select>
                                                <button type="submit" class="btn btn-outline-success btn-sm" name="linkApiStudent" style="margin-left:6px;">Link</button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$apiStudent->student_id) ?>&source=apiStudent">
                                                <button type="button" class="btn btn-outline-primary btn-sm">API Profile</button>
                                            </a>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-success"><strong>Linked</strong></td>
                                        <td><?php echo $this->h($localStudent->id." - ".$localStudent->first_name." ".$localStudent->second_name." ".$localStudent->third_name) ?></td>
                                        <td>
                                            <a href="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$apiStudent->student_id) ?>&source=apiStudent">
                                                <button type="button" class="btn btn-outline-primary btn-sm">API Profile</button>
                                            </a>
                                            <a href="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$localStudent->id) ?>">
                                                <button type="button" class="btn btn-outline-info btn-sm">Local Account</button>
                                            </a>
                                            <?php if($isExplicit): ?>
                                                <form action="" method="POST" style="display:inline-block; margin:0;">
                                                    <input type="hidden" name="apiStudentId" value="<?php echo $this->h($apiStudent->student_id) ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" name="unlinkApiStudent" onclick="return confirm('Remove this API/local student link?');">Unlink</button>
                                                </form>
                                            <?php endif ?>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php
        }

        public function displayApiTeacherLinks($links, $localTeachers = array())
        {
            $linkedCount = 0;

            for($i = 0; $i < count($links); $i++)
            {
                if($links[$i]->localTeacher != null)
                {
                    $linkedCount++;
                }
            }

            echo "<h1 style='text-align:center; margin-top:35px; margin-bottom:15px;'>Teacher Account Links</h1>";
            echo "<h5 class='text-secondary' style='text-align:center; margin-bottom:40px;'>Linked ".$this->h($linkedCount)." of ".$this->h(count($links))." central API lecturers</h5>";

            if(count($links) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No central API lecturers found.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:95%; margin: 0 auto 35px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>API ID</th>
                                <th>API Lecturer</th>
                                <th>API Email</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Local Teacher Account</th>
                                <th>Actions</th>
                            </tr>
                            <?php for($i = 0; $i < count($links); $i++): ?>
                                <?php
                                    $apiLecturer = $links[$i]->apiLecturer;
                                    $localTeacher = $links[$i]->localTeacher;
                                    $isExplicit = isset($links[$i]->isExplicit) && $links[$i]->isExplicit;
                                    $apiTeacherId = isset($apiLecturer['teacher_id']) ? $apiLecturer['teacher_id'] : "";
                                ?>
                                <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->h($apiTeacherId) ?></td>
                                    <td><?php echo $this->apiValue($apiLecturer, 'teacher_name') ?></td>
                                    <td><?php echo $this->apiValue($apiLecturer, 'email') ?></td>
                                    <td><?php echo $this->apiValue($apiLecturer, 'contact_number') ?></td>
                                    <?php if($localTeacher == null): ?>
                                        <td class="text-warning"><strong>Not linked</strong></td>
                                        <td>
                                            <form action="" method="POST" class="form-inline justify-content-center">
                                                <input type="hidden" name="apiTeacherId" value="<?php echo $this->h($apiTeacherId) ?>">
                                                <select class="form-control form-control-sm" name="localTeacherId" required style="max-width:280px;">
                                                    <option value="" disabled selected>Select local teacher</option>
                                                    <?php for($j = 0; $j < count($localTeachers); $j++): ?>
                                                        <option value="<?php echo $this->h($localTeachers[$j]->id) ?>"><?php echo $this->h($this->localTeacherOptionLabel($localTeachers[$j])) ?></option>
                                                    <?php endfor ?>
                                                </select>
                                                <button type="submit" class="btn btn-outline-success btn-sm" name="linkApiTeacher" style="margin-left:6px;">Link</button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="<?php echo $this->h($this->url) ?>&page=Lecturers">
                                                <button type="button" class="btn btn-outline-primary btn-sm">Lecturers</button>
                                            </a>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-success"><strong>Linked</strong></td>
                                        <td><?php echo $this->h($this->localTeacherOptionLabel($localTeacher)) ?></td>
                                        <td>
                                            <a href="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$localTeacher->id) ?>">
                                                <button type="button" class="btn btn-outline-info btn-sm">Local Account</button>
                                            </a>
                                            <?php if($isExplicit): ?>
                                                <form action="" method="POST" style="display:inline-block; margin:0;">
                                                    <input type="hidden" name="apiTeacherId" value="<?php echo $this->h($apiTeacherId) ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" name="unlinkApiTeacher" onclick="return confirm('Remove this API/local teacher link?');">Unlink</button>
                                                </form>
                                            <?php endif ?>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php
        }

        public function displayApiParent($parent)
        {
            echo "<h1 style='text-align:center; margin-top:35px; margin-bottom:35px;'>".$this->apiValue($parent, 'first_name')." ".$this->apiValue($parent, 'last_name')."</h1>";
            ?>
                <table class="table" style="width:85%; margin:0 auto;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Parent ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            <th>Linked Student</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $this->apiValue($parent, 'parent_id') ?></td>
                            <td><?php echo $this->apiValue($parent, 'first_name')." ".$this->apiValue($parent, 'last_name') ?></td>
                            <td><?php echo $this->apiValue($parent, 'email') ?></td>
                            <td><?php echo $this->apiValue($parent, 'contact_number') ?></td>
                            <td><?php echo $this->apiValue($parent, 'address') ?></td>
                            <td><?php echo $this->apiValue($parent, 'student_name') ?></td>
                        </tr>
                    </tbody>
                </table>

                <div style="text-align:center; margin-top:30px;">
                    <a href="<?php echo $this->h($this->url) ?>&selected=parent">
                        <button type="button" class="btn btn-outline-dark">Back to Parents</button>
                    </a>
                    <a href="<?php echo $this->h($this->url) ?>&page=editApiParent&id=<?php echo rawurlencode((string)$this->apiValue($parent, 'parent_id')) ?>">
                        <button type="button" class="btn btn-outline-primary">Edit Parent</button>
                    </a>
                    <form action="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$this->apiValue($parent, 'parent_id')) ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this parent from the central API database?');">
                        <button type="submit" name="deleteApiParent" class="btn btn-outline-danger">Delete Parent</button>
                    </form>
                </div>
            <?php
        }

        public function apiParentForm($parent, $students, $title, $submitName)
        {
            $isEdit = $parent != null;
            $firstName = $isEdit && isset($parent['first_name']) ? $this->h($parent['first_name']) : "";
            $lastName = $isEdit && isset($parent['last_name']) ? $this->h($parent['last_name']) : "";
            $email = $isEdit && isset($parent['email']) ? $this->h($parent['email']) : "";
            $contactNumber = $isEdit && isset($parent['contact_number']) ? $this->h($parent['contact_number']) : "";
            $address = $isEdit && isset($parent['address']) ? $this->h($parent['address']) : "";
            $selectedStudentId = $isEdit && isset($parent['student_id']) ? (string)$parent['student_id'] : "";
            ?>
            <h1 style="text-align:center; margin-top:35px; margin-bottom:35px;"><?php echo $this->h($title) ?></h1>
            <form action="" method="POST" style="width:45%; margin: 0 auto;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo $firstName ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo $lastName ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $email ?>">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" value="<?php echo $contactNumber ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address" rows="3"><?php echo $address ?></textarea>
                </div>
                <div class="form-group">
                    <label>Linked Student</label>
                    <select class="form-control" name="student_id">
                        <option value="">No linked student</option>
                        <?php for($i = 0; $i < count($students); $i++): ?>
                            <?php
                                $studentId = isset($students[$i]->student_id) ? (string)$students[$i]->student_id : (isset($students[$i]->id) ? (string)$students[$i]->id : "");
                                $studentNumber = isset($students[$i]->student_number) && $students[$i]->student_number != "" ? $students[$i]->student_number." - " : "";
                                $studentName = trim($students[$i]->first_name." ".$students[$i]->second_name);
                                $selected = $studentId == $selectedStudentId ? "selected" : "";
                            ?>
                            <option value="<?php echo $this->h($studentId) ?>" <?php echo $selected ?>>
                                <?php echo $this->h($studentNumber.$studentName) ?>
                            </option>
                        <?php endfor ?>
                    </select>
                </div>
                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>
                <div style="text-align:center; margin-top:20px;">
                    <a href="<?php echo $this->h($this->url) ?>&selected=parent">Back to Parents</a>
                </div>
            </form>
            <?php
        }

        public function displayApiStudent($student, $attendance = array(), $events = array(), $localStudent = null, $localStudents = array(), $isExplicitLink = false)
        {
            echo "<h1 style='text-align:center;  margin-top:25px; margin-bottom:35px; '>".$this->h($student->first_name)." ".$this->h($student->second_name)."</h1>";
            ?>
                <table class="table">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">Student ID</th>
                    <th scope="col">Student Number</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Course</th>
                    <th scope="col">Section</th>
                    <th scope="col">Year Level</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact Number</th>
                    <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><?php echo $this->h($student->student_id) ?></th>
                        <td><?php echo $this->h($student->student_number) ?></td>
                        <td><?php echo $this->h($student->first_name." ".$student->second_name) ?></td>
                        <td><?php echo $this->h($student->course_code) ?></td>
                        <td><?php echo $this->h($student->section_name) ?></td>
                        <td><?php echo $this->h($student->year_level) ?></td>
                        <td><?php echo $this->h($student->email) ?></td>
                        <td><?php echo $this->h($student->contact_number) ?></td>
                        <td><?php echo $this->h($student->status) ?></td>
                    </tr>
                </tbody>
                </table>

                <div style="text-align:center; margin-top:30px;">
                    <a href="<?php echo $this->h($this->url) ?>&selected=student">
                        <button type="button" class="btn btn-outline-dark">Back to Students</button>
                    </a>
                    <a href="<?php echo $this->h($this->url) ?>&page=editApiStudent&id=<?php echo rawurlencode((string)$student->student_id) ?>">
                        <button type="button" class="btn btn-outline-primary">Edit Student</button>
                    </a>
                    <form action="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$student->student_id) ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this student from the central API database?');">
                        <button type="submit" name="deleteApiStudent" class="btn btn-outline-danger">Delete Student</button>
                    </form>
                </div>

                <h3 style="text-align:center; margin-top:55px; margin-bottom:25px;">Attendance</h3>
                <?php if(count($attendance) == 0): ?>
                    <h5 class="text-secondary" style="text-align:center; margin-bottom:35px;">No attendance records found.</h5>
                <?php else: ?>
                    <div class="table-responsive" style="width:95%; margin: 0 auto 35px auto;">
                        <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                            <tbody>
                                <tr class="text-white bg-info" style="text-align:center;">
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Time In</th>
                                    <th>Remarks</th>
                                </tr>
                                <?php for($i = 0; $i < count($attendance); $i++): ?>
                                    <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                        <td><?php echo $this->apiDate($attendance[$i], 'attendance_date') ?></td>
                                        <td><?php echo $this->apiValue($attendance[$i], 'status') ?></td>
                                        <td><?php echo $this->apiValue($attendance[$i], 'time_in') ?></td>
                                        <td><?php echo $this->apiValue($attendance[$i], 'remarks') ?></td>
                                    </tr>
                                <?php endfor ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>

                <h3 style="text-align:center; margin-top:35px; margin-bottom:25px;">Course Events</h3>
                <?php if(count($events) == 0): ?>
                    <h5 class="text-secondary" style="text-align:center; margin-bottom:35px;">No events found for this course.</h5>
                <?php else: ?>
                    <div class="table-responsive" style="width:95%; margin: 0 auto 35px auto;">
                        <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                            <tbody>
                                <tr class="text-white bg-info" style="text-align:center;">
                                    <th>Event</th>
                                    <th>Course</th>
                                    <th>Room</th>
                                    <th>Date</th>
                                </tr>
                                <?php for($i = 0; $i < count($events); $i++): ?>
                                    <tr style="border-bottom: 1px solid rgb(230, 230, 230); text-align:center;">
                                        <td><?php echo $this->apiValue($events[$i], 'event_name') ?></td>
                                        <td><?php echo $this->apiValue($events[$i], 'course_code') ?></td>
                                        <td><?php echo $this->apiValue($events[$i], 'room') ?></td>
                                        <td><?php echo $this->apiDate($events[$i], 'event_date') ?></td>
                                    </tr>
                                <?php endfor ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            <?php
        }

        public function apiStudentForm($student, $title, $submitName)
        {
            $isEdit = $student != null;
            $studentNumber = $isEdit ? $this->h($student->student_number) : "";
            $firstName = $isEdit ? $this->h($student->first_name) : "";
            $lastName = $isEdit ? $this->h($student->second_name) : "";
            $courseCode = $isEdit ? $this->h($student->course_code) : "";
            $sectionName = $isEdit ? $this->h($student->section_name) : "";
            $yearLevel = $isEdit ? $this->h($student->year_level) : "";
            $email = $isEdit ? $this->h($student->email) : "";
            $contactNumber = $isEdit ? $this->h($student->contact_number) : "";
            $status = $isEdit ? $student->status : "Active";
            ?>
            <h1 style="text-align:center; margin-top:35px; margin-bottom:35px;"><?php echo $this->h($title) ?></h1>
            <form action="" method="POST" style="width:45%; margin: 0 auto;">
                <div class="form-group">
                    <label>Student Number</label>
                    <input type="text" class="form-control" name="student_number" value="<?php echo $studentNumber ?>" required>
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo $firstName ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo $lastName ?>" required>
                </div>
                <div class="form-group">
                    <label>Course Code</label>
                    <input type="text" class="form-control" name="course_code" value="<?php echo $courseCode ?>" placeholder="BSIT" required>
                </div>
                <div class="form-group">
                    <label>Section</label>
                    <input type="text" class="form-control" name="section_name" value="<?php echo $sectionName ?>" placeholder="A" required>
                </div>
                <div class="form-group">
                    <label>Year Level</label>
                    <input type="text" class="form-control" name="year_level" value="<?php echo $yearLevel ?>" placeholder="2nd Year" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $email ?>">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" value="<?php echo $contactNumber ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="Active" <?php echo strtolower($status) == "active" ? "selected" : "" ?>>Active</option>
                        <option value="Inactive" <?php echo strtolower($status) == "inactive" ? "selected" : "" ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>
                <div style="text-align:center; margin-top:20px;">
                    <a href="<?php echo $this->h($this->url) ?>&selected=student">Back to Students</a>
                </div>
            </form>
            <?php
        }

        public function displaySpecificUser($userType)
        {
            echo "<h1 style='text-align:center;  margin-top:25px; margin-bottom:35px; '>$userType->first_name $userType->second_name $userType->third_name</h1>";
            ?>
                <table class="table">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Type</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">DOB</th>
                    <th scope="col">Email</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Date created</th>
                    <th scope="col">Accepted</th>
                    <th scope="col">Application Number</th>
                    <th scope="col">Phone Number(s)</th>
                    <th scope="col">City</th>
                    <th scope="col">State</th>
                    <th scope="col">Zip</th>
                    <th scope="col">Images</th>
                    
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><?php echo $userType->id ?></th>
                            <td><?php echo $userType->user_type == 3 ? 'Student' : ($userType->user_type == 2 ? 'Teacher' : 'Parent')?></td>
                            <td><?php echo $userType->first_name." ".$userType->second_name." ".$userType->third_name?></td>
                            <td><?php echo $userType->dob?></td>
                            <td><?php echo $userType->email?></td>
                            <td><?php echo $userType->gender == 0 ? 'Male' : 'Female'?></td>
                            <td><?php echo $userType->date_created ?></td>
                            <td><?php echo $userType->accepted == 0 ? 'NO' : 'YES' ?></td>
                            <td><?php echo $userType->application_number ?></td>
                            <td><?php echo $userType->phone_number1 ?></td>
                            <td><?php echo $userType->city ?></td>
                            <td><?php echo $userType->state ?></td>
                            <td><?php echo $userType->zip ?></td>
                            <?php
                                $face_image = $this->decryptImage($userType->face_image);
                                echo "<td><a target='_blank' href=$this->imageURL$face_image>Front Face</a></td>";
                            ?>
                    </tr>

                    <tr>
                        <th colspan="9"></th>
                        <td colspan="4"><?php echo $userType->phone_number2 ?></td>
                        <?php
                        $birth_certificate = $this->decryptImage($userType->birth_certificate);
                        $identity_front = $this->decryptImage($userType->identity_front);

                        if($userType->user_type == 3)
                                echo "<td><a target='_blank' href=$this->imageURL$birth_certificate>Birth Certificate</a></td>";
                            else
                                echo "<td><a target='_blank' href=$this->imageURL$identity_front>Identity Front</a></td>";
                        ?>
                    </tr>

                    <tr>
                        <th colspan="13"></th>
                        <?php
                        $identity_back = $this->decryptImage($userType->identity_back);
                        if($userType->user_type != 3)
                                echo "<td><a target='_blank' href=$this->imageURL$identity_back>Identity Back</a></td>";
                        ?>
                    </tr>
                    
                </tbody>
                </table>

                <form action="" method="POST" style="text-align: center;">

                <?php if($userType->accepted == 0 && $userType->isDeleted == 0): ?>
                    <button type="submit" class="btn btn-success" name="accept">Accept user</button>
                <?php elseif($userType->accepted == 1 && $userType->user_type == 3 && $userType->isDeleted == 0):?>
                    <a href="<?php echo $this->url?>&id=<?php echo $userType->id ?>&action=studentregistration" target="_blank">  <button type="button" class="btn btn-outline-dark" name="studentRegister">Student Registration</button></a>
                    <a href="<?php echo $this->url?>&id=<?php echo $userType->id ?>&action=subjectregistrationForStudents" target="_blank">  <button type="button" class="btn btn-outline-dark" name="subjectRegister">Student Registration Details</button></a>
                    <a href="<?php echo $this->url?>&id=<?php echo $userType->id ?>&action=studentcertificate" target="_blank">  <button type="button" class="btn btn-dark" name="generateCertificate">Generate Certificate</button></a>
                <?php elseif($userType->accepted == 1 && $userType->user_type == 2 && $userType->isDeleted == 0):?>
                    <a href="<?php echo $this->url?>&id=<?php echo $userType->id ?>&action=subjectregistrationForTeachers" target="_blank">  <button type="button" class="btn btn-outline-dark" name="subjectRegister">Teacher Subject Registration</button></a>
                <?php elseif($userType->isDeleted == 1):?>
                    <button type="submit" class="btn btn-success" name="reActivate">Re-activate user</button> 
                    
                <?php endif ?>
                <?php if($userType->isDeleted == 0): ?>
                    <button type="submit" class="btn btn-danger" name="delete">Delete user</button> 
                <?php endif ?>
                </form>
            <?php
        }

        public function searchWithName($userType, $nameArray)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Search Results</h1>";
            echo "<div style='width:100%; margin-top:40px;' class='list-group'>";
            for($i = 0; $i < count($userType); $i++)
            {
                $fullName = $userType[$i]->first_name." ".$userType[$i]->second_name." ".$userType[$i]->third_name;
                $firstName = $userType[$i]->first_name;
                $secondName = $userType[$i]->second_name;
                $thirdName = $userType[$i]->third_name;
                $id = $userType[$i]->id;

                if(isset($userType[$i]->student_id) || isset($userType[$i]->student_number))
                {
                    for($k = 0; $k < count($nameArray); $k++)
                    {
                        if(strtolower($nameArray[$k]) == strtolower($firstName) || strtolower($nameArray[$k]) == strtolower($secondName)
                        || strtolower($nameArray[$k]) == strtolower($thirdName))
                        {
                            $displayId = isset($userType[$i]->student_number) && $userType[$i]->student_number != "" ? $userType[$i]->student_number : $id;
                            $apiDetails = "<br> COURSE: ".$this->h($userType[$i]->course_code)." - ".$this->h($userType[$i]->section_name)."<br> YEAR: ".$this->h($userType[$i]->year_level)." | STATUS: ".$this->h($userType[$i]->status);
                            echo "<a target='_blank' href='".$this->h($this->url)."&id=".rawurlencode((string)$userType[$i]->id)."&source=apiStudent' style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>NAME: ".$this->h($fullName)." <br> ID: ".$this->h($displayId).$apiDetails."</strong></button></a>";
                            break;
                        }
                    }
                    continue;
                }

                for($k = 0; $k < count($nameArray); $k++)
                {
                    if(strtolower($nameArray[$k]) == strtolower($firstName) || strtolower($nameArray[$k]) == strtolower($secondName)
                    || strtolower($nameArray[$k]) == strtolower($thirdName))
                    {
                        if($userType[$i]->accepted == 0)
                        {
                            echo "<a target=”_blank” href=$this->url&id=".$userType[$i]->id." style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-secondary list-group-item list-group-item-action'><strong>NAME: $fullName <br> ID: $id</strong></button></a>";
                        }
                        else
                        {
                            echo "<a target=”_blank” href=$this->url&id=".$userType[$i]->id." style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>NAME: $fullName <br> ID: $id</strong></button></a>";
                        }
                    break;
                    }
                }
                   
                
            }
            echo "</div>";
        }

        public function aboutUs($oldContent)
        {
            ?>
            <h1 style="text-align:center;  margin-top:30px; margin-bottom: 30px;">About us editor</h1>
            <form action="#" method="POST">
            <textarea name="content" id="editor" required>
<?php echo $this->h($oldContent) ?>
            </textarea>
                <div style="text-align:center; margin-top:15px;">
                    <button type="submit" style="width:10%;" class="btn btn-outline-dark" name="editAboutUs">Edit</button>
                </div>
            </form>

            <script>
                ClassicEditor
                    .create( document.querySelector( '#editor' ) )
                    .catch( error => {
                        console.error( error );
                    } );
            </script>

            <?php
        }

        public function decryptImage($strng)
        {
            $ciphering = "AES-128-CTR";
            $decryption_iv = '1234567891011121'; 
            $options = 0;
            $decryption_key = "OOpse314*%*"; 
  
            $decryption = openssl_decrypt ($strng, $ciphering, $decryption_key, $options, $decryption_iv);
            return $decryption;
        }

        public function subjectsRegisterPage($whichUser, $studentSemester, $userType, $subjectsArray)
        {
            if($userType->user_type == 3)
                echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Registration Details to Student $userType->first_name</h1>";
            else if ($userType->user_type == 2)
                echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Register Subjects to Teacher $userType->first_name</h1>";


            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                <select id="inputState" required class="form-control" id="selectId" onclick = "document.getElementById('selectedSubject').value=this.options[this.selectedIndex].text" onchange="document.getElementById('selectedSubject').value=this.options[this.selectedIndex].text">
                    <?php
                    if($whichUser == "student")
                    {
                        echo "<option value='' disabled selected>Subjects:</option>";
                        for($i = 0; $i < count($subjectsArray); $i++)
                        {
                            if($subjectsArray[$i]->semesterId == $studentSemester)
                            {
                                echo "<option>".$subjectsArray[$i]->id." - ".$subjectsArray[$i]->Name. " - ". $subjectsArray[$i]->Code." - ".$subjectsArray[$i]->semesterName."</option>";
                            }
                        }
                    }
                    else if($whichUser == "teacher")
                    {
                        echo "<option value='' disabled selected>Subjects:</option>";
                        for($i = 0; $i < count($subjectsArray); $i++)
                        {
                            echo "<option>".$subjectsArray[$i]->id." - ".$subjectsArray[$i]->Name. " - ". $subjectsArray[$i]->Code." - ".$subjectsArray[$i]->semesterName."</option>";
                            
                        }
                    }
                    ?>
                </select>
                <input type="hidden" name="selectedSubject" id="selectedSubject" value="" />
            </div>


                <button type="submit" name="addSubject" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
                </form>
                <?php
        }

        public function studentsRegisterPage($userType, $semestersArray)
        {
            echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Registration to $userType->first_name</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST">
            <div class="form-group">

                <small>Select Semester:</small>
                <select id="inputState" required class="form-control" onclick="document.getElementById('selectedSemester').value=this.options[this.selectedIndex].text" onchange="document.getElementById('selectedSemester').value=this.options[this.selectedIndex].text">
                    <?php
                    echo "<option value='' disabled selected>Semesters:</option>";
                    for($i = 0; $i < count($semestersArray); $i++)
                    {
                      echo "<option>".$semestersArray[$i]->name."</option>";
                    }
                    ?>
                </select>
                <input type="hidden" name="selectedSemester" id="selectedSemester" value="1" />
            </div>
            <div class="form-row">

                <div class="col-md-12 form-group" style="margin-top:0px;">
                    <input type="number" class="form-control"  placeholder="Registration Fees" name="regFees" required>
                </div>
            </div>

                <button type="submit" name="registerStudent" class="btn btn-outline-dark" style="width: 100%;" >Register</button>
            </form>
            <?php
        }

        public function displayApiSubjects($subjects)
        {
            echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>Central Subjects</h1>";
            $this->apiSubjectForm(null, "Add API Subject", "addApiSubject", false);

            if(count($subjects) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:45px;'>No subjects found in S3.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:75%; margin: 40px auto 35px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Subject ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                            <?php for($i = 0; $i < count($subjects); $i++): ?>
                                <tr style="border-bottom:1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->apiValue($subjects[$i], 'subject_id') ?></td>
                                    <td><?php echo $this->apiValue($subjects[$i], 'subject_code') ?></td>
                                    <td><?php echo $this->apiValue($subjects[$i], 'subject_name') ?></td>
                                    <td>
                                        <a href="<?php echo $this->h($this->url) ?>&page=editApiSubject&id=<?php echo rawurlencode((string)$subjects[$i]['subject_id']) ?>">
                                            <button type="button" class="btn btn-outline-primary btn-sm">Edit</button>
                                        </a>
                                        <form action="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$subjects[$i]['subject_id']) ?>" method="POST" style="display:inline-block; margin:0 0 0 5px;">
                                            <button type="submit" name="deleteApiSubject" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this subject from the central API database?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php
        }

        public function apiSubjectForm($subject, $title, $submitName, $showBackLink = true)
        {
            $isEdit = $subject != null;
            $subjectCode = $isEdit && isset($subject['subject_code']) ? $this->h($subject['subject_code']) : "";
            $subjectName = $isEdit && isset($subject['subject_name']) ? $this->h($subject['subject_name']) : "";
            ?>
            <form action="" style="width:35%; margin: 20px auto;" method="POST">
                <h4 style="text-align:center; margin-bottom:20px;"><?php echo $this->h($title) ?></h4>

                <div class="form-row">
                    <div class="col-md-12 form-group" style="margin-top:4px;">
                        <input type="text" class="form-control" placeholder="Subject Code" name="subject_code" value="<?php echo $subjectCode ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12 form-group" style="margin-top:4px;">
                        <input type="text" class="form-control" placeholder="Subject Name" name="subject_name" value="<?php echo $subjectName ?>" required>
                    </div>
                </div>

                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>

                <?php if($showBackLink): ?>
                    <div style="text-align:center; margin-top:20px;">
                        <a href="<?php echo $this->h($this->url) ?>&page=addsubjects">Back to Subjects</a>
                    </div>
                <?php endif ?>
            </form>
            <?php
        }

        public function addSubjects($semestersArray)
        {
            echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Add Subjects to School</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST">

            <div class="form-group">
                <small>Select Semester:</small>
                <select id="inputState" required class="form-control" onclick="document.getElementById('selectedSemester').value=this.options[this.selectedIndex].text" onchange="document.getElementById('selectedSemester').value=this.options[this.selectedIndex].text">
                    <?php
                    echo "<option value='' disabled selected>Semesters:</option>";
                    for($i = 0; $i < count($semestersArray); $i++)
                    {
                      echo "<option>".$semestersArray[$i]->name."</option>";
                    }
                    ?>
                </select>
                <input type="hidden" name="selectedSemester" id="selectedSemester" value="1" />
            </div>
            
            <div class="form-row">
                <div class="col-md-12 form-group" style="margin-top:4px;">
                    <input type="text" class="form-control"  placeholder="Subject Code" name="subjectCode" required>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-12 form-group" style="margin-top:4px;">
                    <input type="text" class="form-control"  placeholder="Subject Name" name="subjectName" required>
                </div>
            </div>

                <button type="submit" name="addSubjectToSystem" class="btn btn-outline-dark" style="width: 100%;" >Add to the system</button>
            </form>
            <?php
        }

        public function displayStudentCertificate($certificate)
        {
            if($certificate->first_name != "")
                {
            echo "<h1 style='text-align:left;  margin-left:30px; margin-top:40px; margin-bottom: 60px; '>$certificate->first_name $certificate->second_name $certificate->third_name's Certificate - $certificate->semesterName</h1>";
            ?>
                <table class="table table-bordered" style="margin: 0 auto;  width:600px">
                <tbody>
                    <tr>
                        <th>Courses' Name</th>
                        <th>Courses' Code</th>
                        <th>Marks</th>
                        <th>Out of</th>
                    </tr>
                    <?php
                        $passed = true;
                     for($i = 0; $i < count($certificate->subjectsArray); $i++){ ?>
                    <tr>
                        <td><?php echo $certificate->subjectsArray[$i]->Name ?></td>
                        <td><?php echo $certificate->subjectsArray[$i]->Code ?></td>
                        <td><?php echo $certificate->subjectsArray[$i]->studentMarks ?></td>
                        <td><?php echo $certificate->subjectsArray[$i]->subjectMarks ?></td>
                        <td><?php if($certificate->subjectsArray[$i]->studentMarks >= ($certificate->subjectsArray[$i]->subjectMarks / 2))
                        {
                            echo "<div class='text-success'>Passed</div>";
                        }
                        else
                        {
                            echo "<div class='text-danger'>Failed</div>";
                            $passed = false;
                        }
                         ?>
                         </td>
                    </tr>
                    <?php }?>
                </tbody>
                </table>
                <?php
                
                    $percentage = ($certificate->netStudentGrade / $certificate->netSubjectsMarks) * 100;
                    $percentage = round($percentage, 2);
                    echo "<h2 style='text-align:center;  margin-top:40px; margin-bottom: 45px; '>Total Percentage: $percentage% </h2>";
                    if(!$passed && $percentage >= 50)
                    {
                        echo "<h4 class='text-danger' style='text-align:center; margin-top:30px; margin-bottom: 60px; '>This student cannot be transferred to the next semester </h4>";
                    }
                    else if($passed && $percentage < 50 )
                    {
                        echo "<h4 class='text-danger' style='text-align:center; margin-top:30px; margin-bottom: 60px; '>This student cannot be transferred to the next semester </h4>";
                    }
                    else if(!$passed && $percentage < 50)
                    {
                        echo "<h4 class='text-danger' style='text-align:center; margin-top:30px; margin-bottom: 60px; '>This student cannot be transferred to the next semester </h4>";
                    }
                    else if($passed && $percentage >= 50)
                    {
                        echo "<h4 class='text-success' style='text-align:center; margin-top:30px; margin-bottom: 30px; '>This student can be transferred to the next semester </h4>";
                        ?>
                            <form method="POST" action="" style="text-align:center;">
                            <input type="submit" name="transferStudent" class="btn btn-outline-primary" value="Transfer" style="width: 7%;" >
                            </form>
                        <?php
                    }
                }
                else
                {
                    echo "<h4 class='text-danger' style='text-align:center; margin-top:30px; margin-bottom: 60px; '>This Student has not registered in subjects or has not graded yet </h4>";
                }


               
        }

        /*public function SearchBillbyid($userType, $id)
        {
            echo "<h2 class='text-secondary' style='text-align:center;  margin-top:80px; '>Search Results</h2>";
            echo "<div style='width:100%; margin-top:40px;' class='list-group'>";
            for($i = 0; $i < count($userType); $i++)
            {
                if($userType[$i]->id == $id)
                {
                    $Name=$userType[$i]->first_name." ".$userType[$i]->second_name." ".$userType[$i]->third_name;
                        echo "<a target=”_blank” href=$this->url&StudentidBill=".$userType[$i]->id." style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-secondary list-group-item list-group-item-action'><strong>NAME: $Name  <br> ID: $id </strong></button></a>";
                         break;
                }
            }
            echo "</div>";
        }*/

        public function showbill($bill, $total)
        {
            if($bill && isset($bill))
            {
                 $studentName = $bill[0][0]->studentName;
                echo "<h2 class='text-primary' style='text-align:left; margin-left:20px; margin-bottom:10px; margin-top:60px;'>$studentName's Bill(s)</h2>";

          ?>

          <?php for($j = 0; $j < count($bill); $j++){ ?>
              <table class="table table-bordered table-hover" style="margin: 15px 20px;  width:800px; border: 1px solid rgb(224,224,224); border-radius:25px;">
              <caption>Bill <?php echo $j + 1 ?> for <?php echo $studentName ?></caption>
              <tbody>
                  <tr>
                    <th>Bill-id</th>
                    <td><?php echo $bill[$j][0]->id ?></td>
                  </tr>
                  
                  <tr>
                    <th>Student-id</th>
                    <td><?php echo $bill[$j][0]->userId ?></td>
                  </tr>

                  <tr>
                    <th>Name</th>
                    <td><?php echo $bill[$j][0]->studentName ?></td>
                  </tr>

                  <tr>
                    <th>Item(s)</th>
                    <td>
                        <?php
                      for($i = 0; $i < count($bill[$j]); $i++)
                      {
                        echo $bill[$j][$i]->item_name;
                        echo " (".$bill[$j][$i]->netamount.")";
                        if($i != count($bill[$j]) - 1)
                            echo ", ";
                      }
                      ?>
                    </td>
                  </tr>

                  <tr>
                    <th>Semester</th>
                    <td><?php echo $bill[$j][0]->semester_id ?></td>
                  </tr>

                  <tr>
                    <th>Date of Bill</th>
                    <td><?php echo $bill[$j][0]->date ?></td>
                  </tr>

                  <tr>
                    <th>Total items' Prices</th>
                    <td>
                        <?php 
                        $fixNetAmount = 0;
                        for($i = 0; $i < count($bill[$j]); $i++)
                        {
                            $fixNetAmount +=$bill[$j][$i]->netamount;
                        } 
                        echo $fixNetAmount;
                        ?>
                        
                    </td>
                  </tr>

              </tbody>
              </table>
              <br>
              
              <?php
          }
            }
            else
            {
                echo "<h4 class='text-danger' style='text-align:center; margin-top:30px; margin-bottom: 60px; '>This Student has no bills yet </h4>";
            }

        }

        public function showApiBills($bills)
        {
            if(count($bills) == 0)
            {
                echo "<h4 class='text-danger' style='text-align:center; margin-top:30px; margin-bottom:60px;'>This student has no bills yet.</h4>";
                return;
            }

            $studentName = isset($bills[0]['student_name']) ? $this->h($bills[0]['student_name']) : "Student";
            echo "<h2 class='text-primary' style='text-align:left; margin-left:20px; margin-bottom:10px; margin-top:60px;'>".$studentName."'s Bill(s)</h2>";

            for($j = 0; $j < count($bills); $j++)
            {
                $itemsText = "";
                $items = isset($bills[$j]['items']) && is_array($bills[$j]['items']) ? $bills[$j]['items'] : array();

                for($i = 0; $i < count($items); $i++)
                {
                    $itemsText .= $this->h($items[$i]['item_name'])." (".$this->h($items[$i]['amount']).")";
                    if($i != count($items) - 1)
                    {
                        $itemsText .= ", ";
                    }
                }
                ?>
                <table class="table table-bordered table-hover" style="margin: 15px 20px; width:800px; border: 1px solid rgb(224,224,224); border-radius:25px;">
                    <caption>Bill <?php echo $j + 1 ?> for <?php echo $studentName ?></caption>
                    <tbody>
                        <tr>
                            <th>Bill ID</th>
                            <td><?php echo $this->apiValue($bills[$j], 'bill_id') ?></td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td><?php echo $this->apiValue($bills[$j], 'student_id') ?></td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td><?php echo $this->apiValue($bills[$j], 'student_name') ?></td>
                        </tr>
                        <tr>
                            <th>Item(s)</th>
                            <td><?php echo $itemsText ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?php echo $this->apiValue($bills[$j], 'status') ?></td>
                        </tr>
                        <tr>
                            <th>Date of Bill</th>
                            <td><?php echo $this->apiDate($bills[$j], 'bill_date') ?></td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td><?php echo $this->apiValue($bills[$j], 'total_amount') ?></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <?php
            }
        }

        public function createItem($items = array())
        {
              echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Add Items</h1>";
          ?>
          <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
            <div class="form-group">
              <input type="text" class="form-control"  name="itemName" placeholder="Item Name" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control"  name="itemValue" placeholder="Item Price" required>
            </div>
            <button type="submit" name="itemadd" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
            </form>

            <?php if(count($items) > 0): ?>
                <div class="table-responsive" style="width:60%; margin: 30px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Item ID</th>
                                <th>Item Name</th>
                                <th>Amount</th>
                            </tr>
                            <?php for($i = 0; $i < count($items); $i++): ?>
                                <tr style="text-align:center; border-bottom:1px solid rgb(230, 230, 230);">
                                    <td><?php echo $this->apiValue($items[$i], 'item_id') ?></td>
                                    <td><?php echo $this->apiValue($items[$i], 'item_name') ?></td>
                                    <td><?php echo $this->apiValue($items[$i], 'amount') ?></td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>

            <?php
        }

        public function createBill($itemsObjs, $students)
        {
            echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Create Bill</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
            <div class="form-group">
                <select id="inputState" class="form-control" id="selectionId" onchange="document.getElementById('selectedStudentId1').value=this.options[this.selectedIndex].text" required>
                    <?php
                    echo "<option value='' disabled selected>Students:</option>";
                        for($i = 0; $i < count($students); $i++)
                        {
                            if($students[$i]->isDeleted == 0)
                            {
                                $fullName = $students[$i]->first_name." ".$students[$i]->second_name." ".$students[$i]->third_name;
                                echo "<option>".$students[$i]->id." - ".$fullName."</option>";
                            }
                        }
                    ?>
                </select>
                <input type="hidden" name="selectedStudentId1" id="selectedStudentId1" value="" />
            </div>
            <div class="form-group">
            <?php

            for($i=0;$i<count($itemsObjs);$i++)
            {
                $name = is_array($itemsObjs[$i]) && isset($itemsObjs[$i]['item_name']) ? $this->h($itemsObjs[$i]['item_name']) : $this->h($itemsObjs[$i]->name);
                $amount = is_array($itemsObjs[$i]) && isset($itemsObjs[$i]['amount']) ? $this->h($itemsObjs[$i]['amount']) : $this->h($itemsObjs[$i]->price);
                $id = is_array($itemsObjs[$i]) && isset($itemsObjs[$i]['item_id']) ? $this->h($itemsObjs[$i]['item_id']) : $this->h($itemsObjs[$i]->id);
                echo "<label style='margin:7px; font-size:18px'>$name ($amount)</label>";
                echo "<input type='checkbox' value='$id' name='itemscheckbox[]' style='margin-right:20px;'>";
            }
            //$countItems = count($itemsObj);
            //echo "<input type='hidden' value='$countItems'>";
            ?>
            </div>
            <button type="submit" name="createbill" class="btn btn-outline-dark" style="width: 100%;" >Create</button>
            </form>
            <?php
        }

        public function SearchBills($students)
        {
          echo "<h1 style='text-align:center;  margin-top:35px; '>View Bills</h1>";
          ?>
          <div style="margin-left:20px;">
              <form action="" method="POST" style="margin-top:40px;">
              <div class="form-row">
                <div class="col-md-4">
                    <select id="inputState" class="form-control" id="selectionId" onchange="document.getElementById('selectedStudentId2').value=this.options[this.selectedIndex].text" required>
                        <?php
                        echo "<option value='' disabled selected>Students:</option>";
                            for($i = 0; $i < count($students); $i++)
                            {
                                if($students[$i]->isDeleted == 0)
                                {
                                    $fullName = $students[$i]->first_name." ".$students[$i]->second_name." ".$students[$i]->third_name;
                                    echo "<option>".$students[$i]->id." - ".$fullName."</option>";
                                }
                            }
                        ?>
                    </select>
                    <input type="hidden" name="selectedStudentId2" id="selectedStudentId2" value="" />
                </div>
                  <div class="col-md-6">
                      <button type="submit" name="displayStudentBill" class="btn btn-outline-dark">Display</button>
                  </div>
              </div>
              </form>
            </div>

          <?php

        }

        public function configGradesPage($existGrades)
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add new Grading Method</h1>";
            ?>
            <div style="text-align:center; margin-top:25px;">
                <a href="<?php echo $this->url ?>&page=GradeRecords">
                    <button type="button" class="btn btn-outline-info">View S3 Grade Records</button>
                </a>
            </div>
            <form action="" style="width: 35%; margin: 0px auto; margin-top: 50px;" method="POST">
              <div class="form-group">
                <input type="text" class="form-control"  name="gradeType" placeholder="Name" required>
              </div>
              <div class="form-group">
                <input type="text" class="form-control"  name="typeMarks" placeholder="Marks" required>
              </div>
              <button type="submit" name="submitGradeType" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              </form>

              <form action="" style="width: 35%; margin: 20px auto;" method="POST" >
                    <?php
                        for($i = 0; $i < count($existGrades); $i++)
                        {
                            $existGradeName = isset($existGrades[$i]['category_name']) ? $this->apiValue($existGrades[$i], 'category_name') : $this->h($existGrades[$i]['name']);
                            $existGradeMarks = isset($existGrades[$i]['max_score']) ? $this->apiValue($existGrades[$i], 'max_score') : (isset($existGrades[$i]['marks']) ? $this->h($existGrades[$i]['marks']) : "");
                            $existGradeId = isset($existGrades[$i]['category_id']) ? $this->apiValue($existGrades[$i], 'category_id') : $this->h($existGrades[$i]['id']);
                            echo "<div style='margin-bottom: 5px;'>
                            <i class='ion-android-remove-circle' style='font-size:20px; color:red'></i>
                            <a class='text-danger' style='cursor:pointer;' onclick='xmlUpdate($existGradeId)'>$existGradeName / $existGradeMarks</a>
                            </div>";
                        }
                    ?>
              </form>

              <?php
        }

        public function displayApiGradeRecords($grades)
        {
            echo "<h1 style='text-align:center; margin-top:35px; margin-bottom:35px;'>S3 Grade Records</h1>";

            if(count($grades) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:60px;'>No central grade records found yet.</h4>";
                return;
            }

            ?>
            <div class="table-responsive" style="width:95%; margin:0 auto 60px auto;">
                <table class="table table-borderless table-hover" style="box-shadow:0px 4px 8px rgb(235,235,235);">
                    <tbody>
                        <tr class="text-white bg-dark" style="text-align:center;">
                            <th>Grade ID</th>
                            <th>Student ID</th>
                            <th>Student</th>
                            <th>Course / Subject</th>
                            <th>Category</th>
                            <th>Score</th>
                            <th>Lecturer</th>
                            <th>Updated</th>
                        </tr>
                        <?php for($i = 0; $i < count($grades); $i++): ?>
                            <tr style="text-align:center; border-bottom:1px solid rgb(230,230,230);">
                                <td><?php echo $this->apiValue($grades[$i], 'grade_id') ?></td>
                                <td><?php echo $this->apiValue($grades[$i], 'student_id') ?></td>
                                <td><?php echo $this->apiValue($grades[$i], 'student_name') ?></td>
                                <td><?php echo $this->apiValue($grades[$i], 'subject_code')." - ".$this->apiValue($grades[$i], 'subject_name') ?></td>
                                <td><?php echo ucfirst($this->apiValue($grades[$i], 'category_name')) ?></td>
                                <td><?php echo $this->apiValue($grades[$i], 'grade_score')." / ".$this->apiValue($grades[$i], 'max_score') ?></td>
                                <td><?php echo $this->apiValue($grades[$i], 'teacher_name') ?></td>
                                <td><?php echo $this->apiDate($grades[$i], 'updated_at') ?></td>
                            </tr>
                        <?php endfor ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        public function addNewSemesterPage()
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add new Semester</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
              <div class="form-group">
                <input type="text" class="form-control"  name="semesterName" placeholder="Semester Name" required>
              </div>
              <div class="form-group">
                <input type="number" class="form-control"  name="semesterFees" placeholder="Semester Fees" required>
              </div>
              <button type="submit" name="submitSemester" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              </form>
  
              <?php
        }

        public function displayApiSemesters($semesters)
        {
            echo "<h1 style='text-align:center; margin-top:40px; margin-bottom:35px;'>Central Semesters</h1>";
            $this->apiSemesterForm(null, "Add API Semester", "addApiSemester", false);

            if(count($semesters) == 0)
            {
                echo "<h4 class='text-secondary' style='text-align:center; margin-top:45px;'>No semesters found in S3.</h4>";
                return;
            }
            ?>
                <div class="table-responsive" style="width:75%; margin: 40px auto 35px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Semester ID</th>
                                <th>Name</th>
                                <th>Fees</th>
                                <th>Actions</th>
                            </tr>
                            <?php for($i = 0; $i < count($semesters); $i++): ?>
                                <tr style="border-bottom:1px solid rgb(230, 230, 230); text-align:center;">
                                    <td><?php echo $this->apiValue($semesters[$i], 'semester_id') ?></td>
                                    <td><?php echo $this->apiValue($semesters[$i], 'semester_name') ?></td>
                                    <td><?php echo $this->apiValue($semesters[$i], 'fees') ?></td>
                                    <td>
                                        <a href="<?php echo $this->h($this->url) ?>&page=editApiSemester&id=<?php echo rawurlencode((string)$semesters[$i]['semester_id']) ?>">
                                            <button type="button" class="btn btn-outline-primary btn-sm">Edit</button>
                                        </a>
                                        <form action="<?php echo $this->h($this->url) ?>&id=<?php echo rawurlencode((string)$semesters[$i]['semester_id']) ?>" method="POST" style="display:inline-block; margin:0 0 0 5px;">
                                            <button type="submit" name="deleteApiSemester" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this semester from the central API database?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php
        }

        public function apiSemesterForm($semester, $title, $submitName, $showBackLink = true)
        {
            $isEdit = $semester != null;
            $semesterName = $isEdit && isset($semester['semester_name']) ? $this->h($semester['semester_name']) : "";
            $fees = $isEdit && isset($semester['fees']) ? $this->h($semester['fees']) : "";
            ?>
            <form action="" style="width:35%; margin: 20px auto;" method="POST">
                <h4 style="text-align:center; margin-bottom:20px;"><?php echo $this->h($title) ?></h4>

                <div class="form-group">
                    <input type="text" class="form-control" name="semester_name" placeholder="Semester Name" value="<?php echo $semesterName ?>" required>
                </div>

                <div class="form-group">
                    <input type="number" class="form-control" name="fees" placeholder="Semester Fees" value="<?php echo $fees ?>" min="0" step="0.01" required>
                </div>

                <button type="submit" name="<?php echo $this->h($submitName) ?>" class="btn btn-outline-dark" style="width:100%;">Save</button>

                <?php if($showBackLink): ?>
                    <div style="text-align:center; margin-top:20px;">
                        <a href="<?php echo $this->h($this->url) ?>&page=AddNewSemester">Back to Semesters</a>
                    </div>
                <?php endif ?>
            </form>
            <?php
        }

        public function systemMessagesPage($messages = array())
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add new Message to the System</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
              <div class="form-group">
                <input type="text" class="form-control"  name="messageType" placeholder="Type" required>
              </div>
              <div class="form-group">
                <input type="text" class="form-control"  name="messageContent" placeholder="Message" required>
              </div>
              <button type="submit" name="submitSystemMessage" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              </form>

              <?php if(count($messages) > 0): ?>
                <h3 style="text-align:center; margin-top:35px;">S3 System Messages</h3>
                <div class="table-responsive" style="width:80%; margin:20px auto 60px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow:0px 4px 8px rgb(235,235,235);">
                        <tbody>
                            <tr class="text-white bg-dark" style="text-align:center;">
                                <th>ID</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                            <?php for($i = 0; $i < count($messages); $i++): ?>
                                <tr style="text-align:center; border-bottom:1px solid rgb(230,230,230);">
                                    <td><?php echo $this->apiValue($messages[$i], 'message_id') ?></td>
                                    <td><?php echo $this->apiValue($messages[$i], 'message_type') ?></td>
                                    <td><?php echo $this->apiValue($messages[$i], 'message_content') ?></td>
                                    <td><?php echo $this->apiDate($messages[$i], 'created_at') ?></td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
              <?php else: ?>
                <h4 class="text-secondary" style="text-align:center; margin-top:35px;">No S3 system messages found.</h4>
              <?php endif ?>

              <?php
        }

        public function paymentEavPage()
        {
            echo "<h1 style='text-align:center;  margin-bottom:100px; margin-top:60px; '>Payment EAV</h1>";
            ?>
            <div style="width:75%; margin: 0 auto;">
                <div class="d-flex justify-content-center flex-wrap" style="margin: 0 auto;">
                <div class="col-sm-2" style="text-align:center;">
                    <a href="<?php echo $this->url ?>&page=PaymentMethods">
                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                    <i class="ion-ios-circle-outline" style="font-size:50px;"></i>
                    <p class="card-text" style="padding-top:10px">Payment Methods</p>
                    </button>
                    </a>
                </div>

                <div class="col-sm-2" style="text-align:center;">
                    <a href="<?php echo $this->url ?>&page=PaymentOption">
                    <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                    <i class="ion-ios-circle-filled" style="font-size:50px;"></i>
                    <p class="card-text"  style="padding-top:10px">Payment Options</p>
                    </button>
                    </a>
                </div>
                </div>
            </div>
            <?php
            
        }

        public function paymentMethodPage($methods = array())
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add new Payment Method</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
              <div class="form-group">
                <input type="text" class="form-control"  name="methodName" placeholder="Method Name" required>
              </div>
              <button type="submit" name="submitPaymentMethod" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              </form>

              <?php if(count($methods) > 0): ?>
                <div class="table-responsive" style="width:50%; margin: 30px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Method ID</th>
                                <th>Method Name</th>
                            </tr>
                            <?php for($i = 0; $i < count($methods); $i++): ?>
                                <tr style="text-align:center; border-bottom:1px solid rgb(230, 230, 230);">
                                    <td><?php echo $this->apiValue($methods[$i], 'method_id') ?></td>
                                    <td><?php echo $this->apiValue($methods[$i], 'method_name') ?></td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
              <?php endif ?>
  
              <?php
        }

        public function paymentOptionPage($methods, $options = array())
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add new Payment Option</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
              <div class="input-group mb-3">
                <input type="text" class="form-control"  name="optionName" placeholder="Option Name" required>
                    <div class="input-group-append">
                        <select class="custom-select" onchange="document.getElementById('optionType').value=this.options[this.selectedIndex].text" required>
                            <option value='' disabled selected>Option Type:</option>
                            <option value="1">text</option>
                            <option value="2">number</option>
                            <option value="2">email</option>
                        </select>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group">
                        <select class="custom-select" onchange="document.getElementById('methodNameV2').value=this.options[this.selectedIndex].text" required>
                            
                            <?php
                        echo "<option value='' disabled selected>Select from Methods:</option>";
                            for($i = 0; $i < count($methods); $i++)
                            {
                                    $methodName = is_array($methods[$i]) && isset($methods[$i]['method_name']) ? $this->h($methods[$i]['method_name']) : $this->h($methods[$i]->name);
                                    echo "<option>".$methodName."</option>";
                            }
                        ?>
                        </select>
                    </div>
                </div>
              <button type="submit" name="submitPaymentOption" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              <input type="hidden" id="optionType" name="optionType" value="">
              <input type="hidden" id="methodNameV2" name="methodNameV2" value="">
              </form>

              <?php if(count($options) > 0): ?>
                <div class="table-responsive" style="width:70%; margin: 30px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow: 0px 4px 8px rgb(235, 235, 235);">
                        <tbody>
                            <tr class="text-white bg-info" style="text-align:center;">
                                <th>Option ID</th>
                                <th>Method</th>
                                <th>Option Name</th>
                                <th>Type</th>
                            </tr>
                            <?php for($i = 0; $i < count($options); $i++): ?>
                                <tr style="text-align:center; border-bottom:1px solid rgb(230, 230, 230);">
                                    <td><?php echo $this->apiValue($options[$i], 'option_id') ?></td>
                                    <td><?php echo $this->apiValue($options[$i], 'method_name') ?></td>
                                    <td><?php echo $this->apiValue($options[$i], 'option_name') ?></td>
                                    <td><?php echo $this->apiValue($options[$i], 'option_type') ?></td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
              <?php endif ?>
  
              <?php
        }

        public function QrLinkPage($currentLink = "")
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add new QR-Link</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
              <div class="form-group">
                <input type="text" class="form-control"  name="qrLink" placeholder="QR Link" value="<?php echo $this->h($currentLink) ?>" required>
              </div>
              <button type="submit" name="updateQR" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              </form>
              <?php if($currentLink != ""): ?>
                <div style="text-align:center; margin-top:20px;">
                    <strong>Current S3 QR Link:</strong>
                    <br>
                    <a href="<?php echo $this->h($currentLink) ?>" target="_blank"><?php echo $this->h($currentLink) ?></a>
                </div>
              <?php endif ?>
  
              <?php
        }

        public function busPage($buses = array(), $routes = array())
        {
            echo "<h1 style='font-size:50px; text-align:center;  margin-top:35px;'>Buses Management</h1>";
            echo "<div style='margin-top:30px; text-align:center;'>";
            echo "<div><a href=$this->url&page=AddNewRoute><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'>Add New Route</button></a></div>";
            echo "<div><a href=$this->url&page=AddNewBus><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'>Add New Bus</button></a></div>";
            echo "</div>";

            if(count($routes) > 0)
            {
                echo "<h4 style='width:85%; margin:35px auto 15px;'>Central API Routes</h4>";
                echo "<table class='table table-borderless' style='border-radius:30px; margin:0 auto 35px; width:85%; box-shadow:-2px 6px 10px rgb(235,235,235);'>";
                echo "<tr class='text-white bg-dark' style='text-align:center;'><th>ID</th><th>Route</th><th>Fees</th></tr>";
                for($i = 0; $i < count($routes); $i++)
                {
                    echo "<tr style='text-align:center;'>";
                    echo "<td>".$this->apiValue($routes[$i], 'route_id')."</td>";
                    echo "<td>".$this->apiValue($routes[$i], 'route_name')."</td>";
                    echo "<td>".$this->apiValue($routes[$i], 'route_fees')."</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            if(count($buses) == 0)
            {
                echo "<h4 class='text-danger' style='text-align:center; margin-top:30px;'>No buses found in central API database.</h4>";
                return;
            }

            echo "<h4 style='width:95%; margin:35px auto 15px;'>Central API Buses</h4>";
            echo "<table class='table table-borderless' style='border-radius:30px; margin:0 auto 60px; width:95%; box-shadow:-2px 6px 10px rgb(235,235,235);'>";
            echo "<tr class='text-white bg-dark' style='text-align:center;'><th>ID</th><th>Code</th><th>Route</th><th>Meeting Point</th><th>Driver</th><th>Supervisor</th><th>Supervisor Phone</th><th>Seats Left</th><th>First Time</th><th>Second Time</th><th>Fees</th></tr>";
            for($i = 0; $i < count($buses); $i++)
            {
                echo "<tr style='text-align:center;'>";
                echo "<td>".$this->apiValue($buses[$i], 'bus_id')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'bus_code')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'route_name')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'meet_at')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'driver_name')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'supervisor_name')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'supervisor_phone_number')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'seats_left')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'time_move')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'time_arrive')."</td>";
                echo "<td>".$this->apiValue($buses[$i], 'route_fees')."</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        public function addNewRoute()
        {
            echo "<h1 style='text-align:center;  margin-top:35px; '>Add New Bus Route</h1>";
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST" >
              <div class="form-group">
                <input type="text" class="form-control"  name="routeName" placeholder="Route Name" required>
              </div>
              <div class="form-group">
                <input type="text" class="form-control"  name="routeFees" placeholder="Route Fees" required>
              </div>
              <button type="submit" name="submitRoute" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
              </form>
  
              <?php
        }

        public function addNewBusPage($routes)
        {
            echo "<h1 style='font-size:50px; text-align:center;  margin-top:35px;'>Add New Bus info</h1>";
            ?>
                <form action="" style="width: 35%; margin: 50px auto;" method="POST">
                <div class="form-group">
                    <div class="input-group">
                            <select class="custom-select" name="route_id" required>
                                
                                <?php
                            echo "<option value='' disabled selected>Select Route:</option>";
                                for($i = 0; $i < count($routes); $i++)
                                {
                                        $routeId = $this->apiValue($routes[$i], 'route_id');
                                        $routeName = ucfirst($this->apiValue($routes[$i], 'route_name'));
                                        echo "<option value='$routeId'>$routeName</option>";
                                }
                            ?>
                            </select>
                    </div>
                </div>

                <div class="form-group">
                        <input type="text" class="form-control"  name="meetAt" placeholder="Meet at" required>
                </div>

                    <div class="form-group">
                        <input type="text" class="form-control"  name="busCode" placeholder="Bus Code" required>
                    </div>

                <div class="form-group">
                        <input type="text" class="form-control"  name="driverName" placeholder="Driver's Name" required>
                </div>

                <div class="form-group">
                        <input type="number" class="form-control"  name="busSeats" placeholder="Bus's Seats" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control"  name="supervisorName" placeholder="Supervisor's Name" required>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control"  name="supervisorPhoneNumber" placeholder="Supervisor's Phone Number" required>
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control"  name="timeMove" placeholder="First Time 00:00am" pattern="[0-9][0-9]:[0-9][0-9](a|A)(m|M)" required>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control"  name="timeArrive" placeholder="Second Time 00:00pm" pattern="[0-9][0-9]:[0-9][0-9](p|P)(m|M)" required>
                    </div>
                </div>
                
                <button type="submit" name="submitBus" class="btn btn-outline-dark" style="width: 100%;" >Add</button>
                </form>
            <?php
        }

        public function specificSearchPage($reports)
        {
            echo "<h1 style='font-size:50px; text-align:center;  margin-top:35px;'>Specific Search</h1>";
            
            ?>
            <form action="" style="width: 35%; margin: 50px auto;" method="POST">
            <div class="form-group">
                    <div class="input-group mb-4">
                <select id="inputState" class="form-control" onchange="document.getElementById('selectedReport').value=this.options[this.selectedIndex].text">
                    <?php
                    echo "<option value='' disabled selected>Reports:</option>";
                        for($i = 0; $i < count($reports); $i++)
                        {
                            echo "<option>".$reports[$i]->report_name."</option>";
                        }
                    ?>
                </select>
                    </div>
            
            
            <?php
            ?>
                <div class="form-row">
                <div class="form-group col-md-4">
                        <select id="inputState" class="form-control" onchange="document.getElementById('condition').value=this.options[this.selectedIndex].text">
                        <option value='' disabled selected>Condition:</option>
                        <option>more than</option>
                        <option>less than</option>
                        <option>equals</option>
                    </select>
                    </div>

                    <div class="form-group col-md-8">
                        <input type="number" class="form-control"  name="whereGrade" placeholder="Grade">
                    </div>
                    
                </div>
                <button type="submit" name="submitBus" class="btn btn-outline-info" style="width: 100%;" >Search</button>
                <input type="hidden" id="condition" name = "condition" value="">
                <input type="hidden" id="selectedReport" name = "selectedReport" value="">
                </div>
                </form>
            <?php
        }

        public function specificSearchResultsView($results)
        {
            if(isset($results))
            {
                echo "<div style='width:100%; margin-top:40px;' class='list-group'>";
                for($i = 0; $i < count($results); $i++)
                {
                    $fullName = $results[$i][0]." ".$results[$i][1]." ".$results[$i][2];
                    $Id = $results[$i][3];

                    if($results[$i][5] == 0)
                    {
                        echo "<button type='button' class='text-primary list-group-item list-group-item-action'><strong>NAME: $fullName <br> ID: $Id</strong></button>";
                    }
                    else
                    {
                        if($results[$i][5] == 0)
                            echo "<button type='button' class='text-primary list-group-item list-group-item-action'><strong>NAME: $fullName <br> ID: $Id</strong></button>";
                        else
                            echo "<button type='button' class='text-danger list-group-item list-group-item-action'><strong>NAME: $fullName [DELETED]<br> ID: $Id</strong></button>";
                    }
                }
                echo "</div>";
            }
            else
            {
                echo "<h3 style='text-align:center;  margin-top:35px;' class='text-danger'>No Results</h3>";
            }
        }

        public function displayApiReportsSummary($summary, $grades = array())
        {
            echo "<h1 style='font-size:50px; text-align:center; margin-top:35px;'>S3 Summary Report</h1>";
            echo "<h6 class='text-primary' style='text-align:center; margin-bottom:35px;'>Lightweight API report for integration defense.</h6>";

            $cards = array(
                "Students" => "total_students",
                "Lecturers" => "total_lecturers",
                "Courses" => "total_courses",
                "Events" => "total_events",
                "Attendance" => "total_attendance",
                "Grade Records" => "total_grade_records",
                "Bills" => "total_bills",
                "Buses" => "total_buses",
                "Bus Registrations" => "total_bus_registrations",
                "System Messages" => "total_system_messages"
            );
            ?>
            <div style="width:85%; margin:0 auto 40px auto;">
                <div class="d-flex justify-content-center flex-wrap">
                    <?php foreach($cards as $label => $key): ?>
                        <div style="width:210px; margin:10px; padding:18px; border:1px solid rgb(220,220,220); box-shadow:0px 4px 8px rgb(235,235,235); text-align:center;">
                            <h3><?php echo isset($summary[$key]) ? $this->h($summary[$key]) : "0" ?></h3>
                            <div><?php echo $this->h($label) ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <h3 style="text-align:center; margin-top:35px;">Recent S3 Grade Records</h3>
            <?php if(count($grades) == 0): ?>
                <h4 class="text-secondary" style="text-align:center; margin-top:25px;">No central grade records found yet.</h4>
            <?php else: ?>
                <div class="table-responsive" style="width:90%; margin:20px auto 60px auto;">
                    <table class="table table-borderless table-hover" style="box-shadow:0px 4px 8px rgb(235,235,235);">
                        <tbody>
                            <tr class="text-white bg-dark" style="text-align:center;">
                                <th>Student</th>
                                <th>Course / Subject</th>
                                <th>Category</th>
                                <th>Score</th>
                                <th>Lecturer</th>
                            </tr>
                            <?php for($i = 0; $i < count($grades) && $i < 10; $i++): ?>
                                <tr style="text-align:center; border-bottom:1px solid rgb(230,230,230);">
                                    <td><?php echo $this->apiValue($grades[$i], 'student_name') ?></td>
                                    <td><?php echo $this->apiValue($grades[$i], 'subject_code')." - ".$this->apiValue($grades[$i], 'subject_name') ?></td>
                                    <td><?php echo ucfirst($this->apiValue($grades[$i], 'category_name')) ?></td>
                                    <td><?php echo $this->apiValue($grades[$i], 'grade_score')." / ".$this->apiValue($grades[$i], 'max_score') ?></td>
                                    <td><?php echo $this->apiValue($grades[$i], 'teacher_name') ?></td>
                                </tr>
                            <?php endfor ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
            <?php
        }

    }
?>

<script>
    function xmlUpdate(gradeId){
          var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //alert(this.responseText);
            } else {
                //alert(this.status + " " + this.readyState);
            }
        };
        xmlhttp.open("GET", "delete_grading_method.php?gid=" + gradeId, true);
        xmlhttp.send();
        location.reload();
    }
  </script>

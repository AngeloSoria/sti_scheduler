<?php
ob_start();

require_once __DIR__ . '/../../../../../config/dbConnection.php';


$db = new Database();
$conn = $db->getConnection();

function getPreferredSubjectsByFaculty($facultyId)
{
    global $conn;
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT c.CurriculumID, c.SubjectName FROM preferredsubjects p JOIN curriculums c ON p.CurriculumID = c.CurriculumID WHERE p.FacultyID = ? ORDER BY c.SubjectName ASC");
        $stmt->execute([$facultyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getRooms()
{
    global $conn;
    try {
        $db = new Database();
        $dbConn = $db->getConnection();
        $stmt = $dbConn->prepare("SELECT * FROM rooms ORDER BY RoomName ASC");
        $stmt->execute();
        return $stmt->fetchAll($dbConn::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // New function to check schedule conflicts
    function checkScheduleConflict($roomID, $days, $startTime, $endTime, $excludeScheduleID = null)
    {
        $db = new Database();
        $conn = $db->getConnection();
        if (empty($days)) {
            return false;
        }
        $placeholders = implode(',', array_fill(0, count($days), '?'));
        // Correct time overlap condition: (StartTime < endTime) AND (EndTime > startTime)
        $sql = "SELECT COUNT(*) FROM schedules WHERE RoomID = ? AND Day IN ($placeholders) AND (StartTime < ? AND EndTime > ?)";
        $params = array_merge([$roomID], $days, [$endTime, $startTime]);
        if ($excludeScheduleID !== null) {
            $sql .= " AND ScheduleID != ?";
            $params[] = $excludeScheduleID;
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    // New function to check all conflicts for schedules and return conflict info
    function getScheduleConflicts()
    {
        $db = new Database();
        $conn = $db->getConnection();

        // Fetch all schedules
        $sql = "SELECT s.ScheduleID, s.SectionID, s.RoomID, s.Day, s.StartTime, s.EndTime, c.SubjectName
                FROM schedules s
                JOIN curriculums c ON s.CurriculumID = c.CurriculumID";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conflicts = [];

        // Helper function to check time overlap
        function timesOverlap($start1, $end1, $start2, $end2)
        {
            return ($start1 < $end2) && ($start2 < $end1);
        }

        // Check conflicts
        for ($i = 0; $i < count($schedules); $i++) {
            for ($j = $i + 1; $j < count($schedules); $j++) {
                $s1 = $schedules[$i];
                $s2 = $schedules[$j];

                // Check if same day
                if ($s1['Day'] !== $s2['Day']) {
                    continue;
                }

                // Check time overlap
                if (!timesOverlap($s1['StartTime'], $s1['EndTime'], $s2['StartTime'], $s2['EndTime'])) {
                    continue;
                }

                // Conflict conditions:

                // 1. Room conflict: same room overlapping time
                if ($s1['RoomID'] === $s2['RoomID']) {
                    $conflicts[$s1['ScheduleID']][] = 'Room conflict with schedule ID ' . $s2['ScheduleID'];
                    $conflicts[$s2['ScheduleID']][] = 'Room conflict with schedule ID ' . $s1['ScheduleID'];
                }

                // 2. Section multiple subjects overlapping time
                if ($s1['SectionID'] === $s2['SectionID'] && $s1['SubjectName'] !== $s2['SubjectName']) {
                    $conflicts[$s1['ScheduleID']][] = 'Section has multiple subjects overlapping with schedule ID ' . $s2['ScheduleID'];
                    $conflicts[$s2['ScheduleID']][] = 'Section has multiple subjects overlapping with schedule ID ' . $s1['ScheduleID'];
                }

                // 3. Section multiple rooms overlapping time (same section, different rooms)
                if ($s1['SectionID'] === $s2['SectionID'] && $s1['RoomID'] !== $s2['RoomID']) {
                    $conflicts[$s1['ScheduleID']][] = 'Section has multiple rooms scheduled overlapping with schedule ID ' . $s2['ScheduleID'];
                    $conflicts[$s2['ScheduleID']][] = 'Section has multiple rooms scheduled overlapping with schedule ID ' . $s1['ScheduleID'];
                }
            }
        }

        return $conflicts;
    }

    switch ($_POST['context']) {
        case 'checkScheduleConflict':
            $roomID = isset($_POST['roomID']) ? intval($_POST['roomID']) : 0;
            $days = isset($_POST['days']) ? $_POST['days'] : [];
            $startTime = isset($_POST['startTime']) ? trim($_POST['startTime']) : '';
            $endTime = isset($_POST['endTime']) ? trim($_POST['endTime']) : '';

            $conflict = checkScheduleConflict($roomID, $days, $startTime, $endTime);
            header('Content-Type: application/json');
            echo json_encode(['conflict' => $conflict]);
            exit();

        case 'addSchedule':
    // Validate and sanitize input
    $facultyID = isset($_POST['addFacultyID']) ? intval($_POST['addFacultyID']) : 0;
    $curriculumID = isset($_POST['addCurriculumID']) ? intval($_POST['addCurriculumID']) : 0;
    $days = isset($_POST['addDays']) ? $_POST['addDays'] : [];
    $roomID = isset($_POST['addRoomID']) ? intval($_POST['addRoomID']) : 0;
    $sectionID = isset($_POST['addSectionID']) ? intval($_POST['addSectionID']) : 0;
    $startTime = isset($_POST['addStartTime']) ? trim($_POST['addStartTime']) : '';
    $endTime = isset($_POST['addEndTime']) ? trim($_POST['addEndTime']) : '';

    if ($facultyID > 0 && $curriculumID > 0 && !empty($days) && $roomID > 0 && $sectionID > 0 && !empty($startTime) && !empty($endTime)) {

        // Check for conflicts for each day
        $hasConflict = false;
        foreach ($days as $day) {
            if (checkScheduleConflict($roomID, [$day], $startTime, $endTime)) {
                $hasConflict = true;
                break;
            }
        }

        if ($hasConflict) {
            echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    Schedule conflict detected. Please choose a different time or room.
                  </div>
                  <script>
                    setTimeout(function() {
                        var alertElem = document.getElementById("alert-message");
                        if (alertElem) {
                            alertElem.style.display = "none";
                        }
                    }, 5000);
                  </script>';
        } else {
            try {
                $insertSql = "INSERT INTO schedules (FacultyID, CurriculumID, Day, RoomID, SectionID, StartTime, EndTime) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($insertSql);

                foreach ($days as $day) {
                    $stmtInsert->execute([$facultyID, $curriculumID, $day, $roomID, $sectionID, $startTime, $endTime]);
                }

                // Redirect to refresh
                header("Location: dashboard?" . http_build_query($_GET));
                exit();
            } catch (PDOException $e) {
                echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        Error adding schedule.
                      </div>
                      <script>
                        setTimeout(function() {
                            var alertElem = document.getElementById("alert-message");
                            if (alertElem) {
                                alertElem.style.display = "none";
                            }
                        }, 5000);
                      </script>';
            }
        }
    } else {
        echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                Please fill in all required fields.
              </div>
              <script>
                setTimeout(function() {
                    var alertElem = document.getElementById("alert-message");
                    if (alertElem) {
                        alertElem.style.display = "none";
                    }
                }, 5000);
              </script>';
    }
    break;

        case 'editSchedule':
            // Validate and sanitize input
            $scheduleID = $_POST['editScheduleID'];
            $day = isset($_POST['editDay']) ? trim($_POST['editDay']) : '';
            $roomID = isset($_POST['editRoomID']) ? intval($_POST['editRoomID']) : 0;
            $sectionID = isset($_POST['editSectionID']) ? intval($_POST['editSectionID']) : 0;
            $startTime = isset($_POST['editStartTime']) ? trim($_POST['editStartTime']) : '';
            $endTime = isset($_POST['editEndTime']) ? trim($_POST['editEndTime']) : '';

            if ($scheduleID > 0 && !empty($day) && $roomID > 0 && $sectionID > 0 && !empty($startTime) && !empty($endTime)) {
                // Convert day string to array for conflict check
                $daysArray = [$day];
                // Check for schedule conflict excluding current scheduleID
                $conflict = checkScheduleConflict($roomID, $daysArray, $startTime, $endTime, $scheduleID);
                if ($conflict) {
                    echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Schedule conflict detected. Please choose a different time or room.</div>
                    <script>
                        setTimeout(function() {
                            var alertElem = document.getElementById("alert-message");
                            if (alertElem) {
                                alertElem.style.display = "none";
                            }
                        }, 5000);
                    </script>';
                } else {
                    try {
                        $updateSql = "UPDATE schedules SET Day = ?, RoomID = ?, SectionID = ?, StartTime = ?, EndTime = ? WHERE ScheduleID = ?";
                        $stmtUpdate = $conn->prepare($updateSql);
                        $stmtUpdate->execute([$day, $roomID, $sectionID, $startTime, $endTime, $scheduleID]);

                        // Redirect to schedules.php with current query parameters to refresh the list
                        $queryParams = [];
                        if (isset($_GET['faculty'])) {
                            $queryParams['faculty'] = $_GET['faculty'];
                        }
                        if (isset($_GET['day'])) {
                            $queryParams['day'] = $_GET['day'];
                        }
                        if (isset($_GET['section'])) {
                            $queryParams['section'] = $_GET['section'];
                        }
                        if (isset($_GET['search'])) {
                            $queryParams['search'] = $_GET['search'];
                        }
                        if (isset($_GET['page'])) {
                            $queryParams['page'] = $_GET['page'];
                        }
                        if (isset($_GET['rowsPerPage'])) {
                            $queryParams['rowsPerPage'] = $_GET['rowsPerPage'];
                        }

                        $queryString = http_build_query($queryParams);
                    } catch (PDOException $e) {
                        // Handle error (optional: log error)
                        echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error updating schedule.</div>
                        <script>
                            setTimeout(function() {
                                var alertElem = document.getElementById("alert-message");
                                if (alertElem) {
                                    alertElem.style.display = "none";
                                }
                            }, 5000);
                        </script>';
                    }
                }
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Please fill in all required fields.</div>';
            }
            break;
        case 'deleteSchedule':
            // Validate and sanitize input
            $scheduleID = isset($_POST['deleteScheduleID']) ? intval($_POST['deleteScheduleID']) : 0;
            if (isset($scheduleID)) {
                try {
                    $deleteSql = "DELETE FROM schedules WHERE ScheduleID = ?";
                    $stmtDelete = $conn->prepare($deleteSql);
                    $stmtDelete->execute([$scheduleID]);
                } catch (PDOException $e) {
                    // Handle error (optional: log error)
                    echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Error deleting schedule.</div>
<script>
    setTimeout(function() {
        var alertElem = document.getElementById("alert-message");
        if (alertElem) {
            alertElem.style.display = "none";
        }
    }, 5000);
</script>';
                }
            } else {
                echo '<div id="alert-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">Invalid schedule ID.</div>
<script>
    setTimeout(function() {
        var alertElem = document.getElementById("alert-message");
        if (alertElem) {
            alertElem.style.display = "none";
        }
    }, 5000);
</script>';
            }
            break;
    }

}

// Load Data for Filters and Page Load
$data = [];
$totalRows = 0;
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);
$facultyFilter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$dayFilter = isset($_GET['day']) ? $_GET['day'] : '';
$sectionFilter = isset($_GET['section']) ? $_GET['section'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$whereClauses = [];
$queryParams = [];

if (!empty($facultyFilter)) {
    $whereClauses[] = "s.FacultyID = ?";
    $queryParams[] = $facultyFilter;
}

if (!empty($dayFilter)) {
    $whereClauses[] = "s.Day = ?";
    $queryParams[] = $dayFilter;
}

if (!empty($sectionFilter)) {
    $whereClauses[] = "s.SectionID = ?";
    $queryParams[] = $sectionFilter;
}

if (!empty($search)) {
    $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($searchKeywords)) {
        $keywordClauses = [];
        foreach ($searchKeywords as $keyword) {
            $keyword = strtolower($keyword);
            $keywordClauses[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(CONCAT(u.FirstName, ' ', u.LastName)) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(s.Day) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(r.RoomName) LIKE LOWER(?)";
            $keywordClauses[] = "LOWER(sec.SectionName) LIKE LOWER(?)";
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
            $queryParams[] = '%' . $keyword . '%';
        }
        $whereClauses[] = '(' . implode(' OR ', $keywordClauses) . ')';
    }
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

$sql = "SELECT COUNT(*) AS total
        FROM schedules s
        JOIN curriculums c ON s.CurriculumID = c.CurriculumID
        JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
        JOIN users u ON fm.UserID = u.UserID
        JOIN rooms r ON s.RoomID = r.RoomID
        JOIN sections sec ON s.SectionID = sec.SectionID
        $whereString";

$stmtCount = $conn->prepare($sql);
$stmtCount->execute($queryParams);
$row = $stmtCount->fetch(PDO::FETCH_ASSOC);
$totalRows = $row['total'];
$totalPages = ceil($totalRows / $rowsPerPage);

if ($totalRows > 0) {
    $sqlData = "SELECT s.ScheduleID, c.SubjectName, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, s.Day, s.StartTime, s.EndTime, r.RoomName, sec.SectionName
                FROM schedules s
                JOIN curriculums c ON s.CurriculumID = c.CurriculumID
                JOIN facultymembers fm ON s.FacultyID = fm.FacultyID
                JOIN users u ON fm.UserID = u.UserID
                JOIN rooms r ON s.RoomID = r.RoomID
                JOIN sections sec ON s.SectionID = sec.SectionID
                $whereString
                ORDER BY FIELD(s.Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), s.StartTime ASC
                LIMIT ?, ?";

    $stmtData = $conn->prepare($sqlData);
    $paramIndex = 1;
    if (!empty($queryParams)) {
        foreach ($queryParams as $param) {
            $stmtData->bindValue($paramIndex++, $param);
        }
    }
    $stmtData->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmtData->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
    $stmtData->execute();
    $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);
} else {
    $data = [];
}

// Fetch faculties for filter and modal dropdown
$stmtFaculties = $conn->prepare("SELECT fm.FacultyID, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, u.FirstName, u.LastName FROM facultymembers fm JOIN users u ON fm.UserID = u.UserID ORDER BY u.FirstName, u.LastName");
$stmtFaculties->execute();
$faculties = $stmtFaculties->fetchAll(PDO::FETCH_ASSOC);

// Fetch sections for filter and modal dropdown
$stmtSections = $conn->prepare("SELECT SectionID, SectionName FROM sections ORDER BY SectionName");
$stmtSections->execute();
$sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);

// Days array for filter
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Get filters from GET
$facultyFilter = isset($_GET['faculty']) ? $_GET['faculty'] : '';
$dayFilter = isset($_GET['day']) ? $_GET['day'] : '';
$sectionFilter = isset($_GET['section']) ? $_GET['section'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);

// Build where clauses and params
$whereClauses = [];
$queryParams = [];

if (!empty($facultyFilter)) {
    $whereClauses[] = "s.FacultyID = ?";
    $queryParams[] = $facultyFilter;
}

if (!empty($dayFilter)) {
    $whereClauses[] = "s.Day = ?";
    $queryParams[] = $dayFilter;
}

if (!empty($sectionFilter)) {
    $whereClauses[] = "s.SectionID = ?";
    $queryParams[] = $sectionFilter;
}

if (!empty($search)) {
    $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($searchKeywords)) {
        $keywordClauses = [];
        foreach ($searchKeywords as $keyword) {
            $keywordClause = [];
            $keyword = strtolower($keyword);
            $keywordClause[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(u.FirstName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(u.LastName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(r.RoomName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(se.SectionName) LIKE LOWER(?)";
            $keywordClauses[] = '(' . implode(' OR ', $keywordClause) . ')';
            $likeKeyword = '%' . $keyword . '%';
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
        }
        $whereClauses[] = '(' . implode(' AND ', $keywordClauses) . ')';
    }
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Count total rows
$countSql = "SELECT COUNT(*) FROM schedules s
    JOIN curriculums c ON s.CurriculumID = c.CurriculumID
    JOIN facultymembers f ON s.FacultyID = f.FacultyID
    JOIN users u ON f.UserID = u.UserID
    JOIN rooms r ON s.RoomID = r.RoomID
    JOIN sections se ON s.SectionID = se.SectionID
    $whereString";

$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($queryParams);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $rowsPerPage);

// Fetch data with joins
$sql = "SELECT s.ScheduleID, c.SubjectName, CONCAT(u.FirstName, ' ', u.LastName) AS FacultyName, s.Day, s.StartTime, s.EndTime, r.RoomName, se.SectionName, se.SectionID, r.RoomID
    FROM schedules s
    JOIN curriculums c ON s.CurriculumID = c.CurriculumID
    JOIN facultymembers f ON s.FacultyID = f.FacultyID
    JOIN users u ON f.UserID = u.UserID
    JOIN rooms r ON s.RoomID = r.RoomID
    JOIN sections se ON s.SectionID = se.SectionID
    $whereString
    ORDER BY s.ScheduleID ASC
    LIMIT ?, ?";

$stmt = $conn->prepare($sql);

try {
    $paramIndex = 1;
    if (!empty($queryParams)) {
        foreach ($queryParams as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
    }
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data = [];
    $totalRows = 0;
    $totalPages = 0;
}
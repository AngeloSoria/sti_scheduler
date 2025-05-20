<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

$addSuccess = null;
$addErrors = [];

// Handle Add School Year Semester
if (isset($_POST['btnAdd'])) {
    $startYear = trim($_POST['addStartYear']);
    $endYear = trim($_POST['addEndYear']);
    $semester = trim($_POST['addSemester']);
    $isActive = isset($_POST['addIsActive']) ? 1 : 0;

    // Validate years: numeric, no spaces, 4 digits
    if (!preg_match('/^\d{4}$/', $startYear) || !preg_match('/^\d{4}$/', $endYear)) {
        $addSuccess = false;
        $addErrors[] = "School year must be 4 digit numbers without spaces.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">School year must be 4 digit numbers without spaces.</span>
            </div>';
    } elseif ($startYear >= $endYear) {
        $addSuccess = false;
        $addErrors[] = "Start year must be less than end year.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Start year must be less than end year.</span>
            </div>';
    } elseif ($semester !== '1' && $semester !== '2') {
        $addSuccess = false;
        $addErrors[] = "Invalid semester selected.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Invalid semester selected.</span>
            </div>';
    } else {
        $schoolYear = $startYear . '-' . $endYear;

        // Check if record already exists
        $stmtCheck = $conn->prepare("SELECT ID FROM school_year_semesters WHERE SchoolYear = ? AND Semester = ?");
        $stmtCheck->execute([$schoolYear, $semester]);
        if ($stmtCheck->fetch()) {
            $addSuccess = false;
            $addErrors[] = "School Year Semester already exists.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">School Year Semester already exists.</span>
                </div>';
        } else {
            // If isActive is set, set all others to inactive
            if ($isActive) {
                $conn->exec("UPDATE school_year_semesters SET IsActive = 0");
            }

            $stmtInsert = $conn->prepare("INSERT INTO school_year_semesters (SchoolYear, Semester, IsActive) VALUES (?, ?, ?)");
            if ($stmtInsert->execute([$schoolYear, $semester, $isActive])) {
                $addSuccess = true;
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">School Year Semester added successfully!</span>
                    </div>';
            } else {
                $addSuccess = false;
                $addErrors[] = "Error adding School Year Semester.";
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add School Year Semester. Please try again.</span>
                    </div>';
            }
        }
    }
}

// Handle Edit School Year Semester
if (isset($_POST['btnEdit'])) {
    $editID = trim($_POST['editID']);
    $startYear = trim($_POST['editStartYear']);
    $endYear = trim($_POST['editEndYear']);
    $semester = trim($_POST['editSemester']);
    $isActive = isset($_POST['editIsActive']) ? 1 : 0;

    if (!preg_match('/^\d{4}$/', $startYear) || !preg_match('/^\d{4}$/', $endYear)) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">School year must be 4 digit numbers without spaces.</span>
            </div>';
    } elseif ($semester !== '1' && $semester !== '2') {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Invalid semester selected.</span>
            </div>';
    } else {
        $schoolYear = $startYear . '-' . $endYear;

        // Check if record exists with same school year and semester but different ID
        $stmtCheck = $conn->prepare("SELECT ID FROM school_year_semesters WHERE SchoolYear = ? AND Semester = ? AND ID != ?");
        $stmtCheck->execute([$schoolYear, $semester, $editID]);
        if ($stmtCheck->fetch()) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">School Year Semester already exists.</span>
                </div>';
        } else {
            if ($isActive) {
                $conn->exec("UPDATE school_year_semesters SET IsActive = 0");
            }

            $stmtUpdate = $conn->prepare("UPDATE school_year_semesters SET SchoolYear = ?, Semester = ?, IsActive = ? WHERE ID = ?");
            if ($stmtUpdate->execute([$schoolYear, $semester, $isActive, $editID])) {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">School Year Semester updated successfully!</span>
                    </div>';
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to update School Year Semester. Please try again.</span>
                    </div>';
            }
        }
    }
}

// Handle Delete School Year Semester
if (isset($_POST['deleteID'])) {
    $deleteID = $_POST['deleteID'];

    $stmtDelete = $conn->prepare("DELETE FROM school_year_semesters WHERE ID = ?");
    if ($stmtDelete->execute([$deleteID])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">School Year Semester deleted successfully!</span>
              </div>';
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Failed to delete School Year Semester. Please try again.</span>
              </div>';
    }
    exit;
}

// Pagination and search
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereClauses = [];
$queryParams = [];

if (!empty($search)) {
    $whereClauses[] = "SchoolYear LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $sql = "SELECT * FROM school_year_semesters
            $whereString
            ORDER BY ID DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $paramIndex = 1;
    foreach ($queryParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $schoolYearSemesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM school_year_semesters $whereString";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($queryParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $rowsPerPage);

} catch (PDOException $e) {
    $schoolYearSemesters = [];
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Failed to load School Year Semesters.</span>
          </div>';
}

<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

$addSuccess = null;
$addErrors = [];

// Handle Manual Add
if (isset($_POST['btnAdd'])) {
    $addProgramName = trim($_POST['addProgramName']);

    // Check if program already exists
    $stmtCheck = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmtCheck->execute([$addProgramName]);
    if ($stmtCheck->fetch()) {
        $addSuccess = false;
        $addErrors[] = "Program '" . htmlspecialchars($addProgramName) . "' already exists.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to add program. Program "' . htmlspecialchars($addProgramName) . '" already exists.</span>
                </div>';
    } else {
        $stmtInsert = $conn->prepare("INSERT INTO programs (ProgramName) VALUES (?)");
        if ($stmtInsert->execute([$addProgramName])) {
            $addSuccess = true;
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Program "' . htmlspecialchars($addProgramName) . '" added successfully!</span>
                    </div>';
        } else {
            $addSuccess = false;
            $addErrors[] = "Error adding program '" . htmlspecialchars($addProgramName) . "'.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add program. Please try again.</span>
                    </div>';
        }
    }
}

// Handle Edit Program
if (isset($_POST['btnEdit'])) {
    $editProgramID = trim($_POST['editProgramID']);
    $editProgramName = trim($_POST['editProgramName']);

    // Check if program name already exists for another ID
    $stmtCheck = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ? AND ProgramID != ?");
    $stmtCheck->execute([$editProgramName, $editProgramID]);
    if ($stmtCheck->fetch()) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Program name "' . htmlspecialchars($editProgramName) . '" already exists.</span>
            </div>';
    } else {
        $stmtUpdate = $conn->prepare("UPDATE programs SET ProgramName = ? WHERE ProgramID = ?");
        if ($stmtUpdate->execute([$editProgramName, $editProgramID])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Program updated successfully!</span>
                </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to update program. Please try again.</span>
                </div>';
        }
    }
}

// Handle Delete Program
if (isset($_POST['deleteProgramID'])) {
    $deleteProgramID = $_POST['deleteProgramID'];

    // Check for related records in curriculums
    $stmtCheckCurriculums = $conn->prepare("SELECT COUNT(*) FROM curriculums WHERE ProgramID = ?");
    $stmtCheckCurriculums->execute([$deleteProgramID]);
    $countCurriculums = $stmtCheckCurriculums->fetchColumn();

    if ($countCurriculums > 0) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Cannot delete program because it has related curriculums.</span>
              </div>';
        exit;
    }

    $stmtDelete = $conn->prepare("DELETE FROM programs WHERE ProgramID = ?");
    if ($stmtDelete->execute([$deleteProgramID])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Program deleted successfully!</span>
              </div>';
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Failed to delete program. Please try again.</span>
              </div>';
    }
    exit;
}

$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$whereClauses = [];
$queryParams = [];

if (!empty($search)) {
    $whereClauses[] = "ProgramName LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $sql = "SELECT ProgramID, ProgramName FROM programs $whereString ORDER BY ProgramID ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $paramIndex = 1;
    foreach ($queryParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM programs $whereString";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($queryParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $rowsPerPage);

} catch (PDOException $e) {
    $programs = [];
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Failed to load programs.</span>
          </div>';
}

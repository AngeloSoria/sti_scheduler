<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

$addSuccess = null;
$addErrors = [];

// Handle Manual Add
if (isset($_POST['btnAdd'])) {
    $addDepartmentName = trim($_POST['addDepartmentName']);

    // Prevent empty department name
    if (empty($addDepartmentName)) {
        $addSuccess = false;
        $addErrors[] = "Department name cannot be empty.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Department name cannot be empty.</span>
              </div>';
    } else {
        // Check if department already exists
        $stmtCheck = $conn->prepare("SELECT DepartmentID FROM departments WHERE DepartmentName = ?");
        $stmtCheck->execute([$addDepartmentName]);
        if ($stmtCheck->fetch()) {
            $addSuccess = false;
            $addErrors[] = "Department '" . htmlspecialchars($addDepartmentName) . "' already exists.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add department. Department "' . htmlspecialchars($addDepartmentName) . '" already exists.</span>
                    </div>';
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO departments (DepartmentName) VALUES (?)");
            if ($stmtInsert->execute([$addDepartmentName])) {
                $addSuccess = true;
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">Department "' . htmlspecialchars($addDepartmentName) . '" added successfully!</span>
                        </div>';
            } else {
                $addSuccess = false;
                $addErrors[] = "Error adding department '" . htmlspecialchars($addDepartmentName) . "'.";
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">Failed to add department. Please try again.</span>
                        </div>';
            }
        }
    }
}

// Handle Edit Department
if (isset($_POST['btnEdit'])) {
    $editDepartmentID = trim($_POST['editDepartmentID']);
    $editDepartmentName = trim($_POST['editDepartmentName']);

    // Check if department name already exists for another ID
    $stmtCheck = $conn->prepare("SELECT DepartmentID FROM departments WHERE DepartmentName = ? AND DepartmentID != ?");
    $stmtCheck->execute([$editDepartmentName, $editDepartmentID]);
    if ($stmtCheck->fetch()) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Department name "' . htmlspecialchars($editDepartmentName) . '" already exists.</span>
            </div>';
    } else {
        $stmtUpdate = $conn->prepare("UPDATE departments SET DepartmentName = ? WHERE DepartmentID = ?");
        if ($stmtUpdate->execute([$editDepartmentName, $editDepartmentID])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Department updated successfully!</span>
                </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to update department. Please try again.</span>
                </div>';
        }
    }
}

// Handle Delete Department
if (isset($_POST['deleteDepartmentID'])) {
    $deleteDepartmentID = $_POST['deleteDepartmentID'];

    // Check for related records if needed (e.g., schedules)
    // For now, assume no related records or add similar checks as needed

    $stmtDelete = $conn->prepare("DELETE FROM departments WHERE DepartmentID = ?");
    if ($stmtDelete->execute([$deleteDepartmentID])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Department deleted successfully!</span>
              </div>';
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Failed to delete department. Please try again.</span>
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
    $whereClauses[] = "DepartmentName LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $sql = "SELECT DepartmentID, DepartmentName FROM departments $whereString ORDER BY DepartmentID ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $paramIndex = 1;
    foreach ($queryParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM departments $whereString";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($queryParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $rowsPerPage);

} catch (PDOException $e) {
    $departments = [];
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Failed to load departments.</span>
          </div>';
}

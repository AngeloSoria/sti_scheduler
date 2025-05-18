<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

$addSuccess = null;
$addErrors = [];

// Handle Manual Add
if (isset($_POST['btnAdd'])) {
    $addSectionName = trim($_POST['addSectionName']);
    $addProgramID = isset($_POST['addProgramID']) ? trim($_POST['addProgramID']) : '';

    // Prevent empty or whitespace-only section names or missing program
    if ($addSectionName === '' || $addProgramID === '') {
        $addSuccess = false;
        $addErrors[] = "Section name and program are required.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Section name and program are required.</span>
            </div>';
    } else {
        // Check if section already exists for the same program
        $stmtCheck = $conn->prepare("SELECT SectionID FROM sections WHERE SectionName = ? AND ProgramID = ?");
        $stmtCheck->execute([$addSectionName, $addProgramID]);
        if ($stmtCheck->fetch()) {
            $addSuccess = false;
            $addErrors[] = "Section '" . htmlspecialchars($addSectionName) . "' already exists for this program.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add section. Section "' . htmlspecialchars($addSectionName) . '" already exists for this program.</span>
                    </div>';
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO sections (SectionName, ProgramID) VALUES (?, ?)");
            if ($stmtInsert->execute([$addSectionName, $addProgramID])) {
                $addSuccess = true;
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">Section "' . htmlspecialchars($addSectionName) . '" added successfully!</span>
                        </div>';
            } else {
                $addSuccess = false;
                $addErrors[] = "Error adding section '" . htmlspecialchars($addSectionName) . "'.";
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">Failed to add section. Please try again.</span>
                        </div>';
            }
        }
    }
}

// Handle Edit Section
if (isset($_POST['btnEdit'])) {
    $editSectionID = trim($_POST['editSectionID']);
    $editSectionName = trim($_POST['editSectionName']);
    $editProgramID = isset($_POST['editProgramID']) ? trim($_POST['editProgramID']) : '';

    // Check for empty fields
    if ($editSectionName === '' || $editProgramID === '') {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Section name and program are required.</span>
            </div>';
    } else {
        // Check if section name already exists for another ID and same program
        $stmtCheck = $conn->prepare("SELECT SectionID FROM sections WHERE SectionName = ? AND ProgramID = ? AND SectionID != ?");
        $stmtCheck->execute([$editSectionName, $editProgramID, $editSectionID]);
        if ($stmtCheck->fetch()) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Section name "' . htmlspecialchars($editSectionName) . '" already exists for this program.</span>
                </div>';
        } else {
            $stmtUpdate = $conn->prepare("UPDATE sections SET SectionName = ?, ProgramID = ? WHERE SectionID = ?");
            if ($stmtUpdate->execute([$editSectionName, $editProgramID, $editSectionID])) {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Section updated successfully!</span>
                    </div>';
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to update section. Please try again.</span>
                    </div>';
            }
        }
    }
}

// Handle Delete Section
if (isset($_POST['deleteSectionID'])) {
    $deleteSectionID = $_POST['deleteSectionID'];

    // Check for related records if needed (e.g., schedules)
    // For now, assume no related records or add similar checks as needed

    $stmtDelete = $conn->prepare("DELETE FROM sections WHERE SectionID = ?");
    if ($stmtDelete->execute([$deleteSectionID])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Section deleted successfully!</span>
              </div>';
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Failed to delete section. Please try again.</span>
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
    $whereClauses[] = "SectionName LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $sql = "SELECT s.SectionID, s.SectionName, s.ProgramID, p.ProgramName
            FROM sections s
            LEFT JOIN programs p ON s.ProgramID = p.ProgramID
            $whereString
            ORDER BY s.SectionID ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $paramIndex = 1;
    foreach ($queryParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM sections $whereString";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($queryParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $rowsPerPage);

} catch (PDOException $e) {
    $sections = [];
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Failed to load sections.</span>
          </div>';
}

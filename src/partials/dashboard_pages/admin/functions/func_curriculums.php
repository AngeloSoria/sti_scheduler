<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

$importSuccess = null;
$importErrors = [];
$addSuccess = null;
$addErrors = [];

// Handle CSV Import
if (isset($_POST['btnImport']) && isset($_FILES['csvFile'])) {
    $file = fopen($_FILES['csvFile']['tmp_name'], 'r');
    $isHeader = true;
    $importSuccess = false;
    $importErrors = [];

    while (($row = fgetcsv($file, 1000, ',')) !== false) {
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        $courseID = trim($row[0]);
        $subjectArea = trim($row[1]);
        $catalogNo = trim($row[2]);
        $subjectName = trim($row[3]);
        $units = trim($row[4]);
        $programName = trim($row[5]);
        $yearLevel = trim($row[6]);
        $semester = trim($row[7]);

        // Lookup ProgramID using ProgramName from CSV
        $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
        $stmt->execute([$programName]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($program) {
            $programID = $program['ProgramID'];

            // Insert into curriculum
            $stmtInsert = $conn->prepare("INSERT INTO curriculums (CourseID, SubjectArea, CatalogNo, SubjectName, Units, ProgramID, YearLevel, Semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmtInsert->execute([$courseID, $subjectArea, $catalogNo, $subjectName, $units, $programID, $yearLevel, $semester])) {
                $importSuccess = false;
                $importErrors[] = "Error inserting subject '$subjectName' for program '$programName'.";
                break;
            } else {
                $importSuccess = true;
            }
        } else {
            $importSuccess = false;
            $importErrors[] = "Program '$programName' not found in the database.";
            break;
        }
    }

    fclose($file);

    if ($importSuccess && empty($importErrors)) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">CSV data imported successfully!</span>
                </div>';
    } elseif ($importSuccess === false) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Import failed. ' . (!empty($importErrors) ? implode('<br>', $importErrors) : 'Invalid data.') . '</span>
                </div>';
    }
}

// Handle Manual Add
if (isset($_POST['btnAdd'])) {
    $addSubjectName = trim($_POST['addSubjectName']);
    $addUnits = trim($_POST['addUnits']);
    $addCourseID = isset($_POST['addCourseID']) ? trim($_POST['addCourseID']) : null;
    $addSubjectArea = isset($_POST['addSubjectArea']) ? trim($_POST['addSubjectArea']) : null;
    $addCatalogNo = isset($_POST['addCatalogNo']) ? trim($_POST['addCatalogNo']) : null;
    $addUnits = isset($_POST['addUnits']) ? trim($_POST['addUnits']) : null;
    $addProgramName = trim($_POST['addProgramName']);
    $addYearLevel = trim($_POST['addYearLevel']);
    $addSemester = isset($_POST['addSemester']) ? trim($_POST['addSemester']) : null;

    // Lookup ProgramID
    $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmt->execute([$addProgramName]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        $programID = $program['ProgramID'];

        // Check for duplicate curriculum with same subject, year, unit, and program
        $stmtCheck = $conn->prepare("SELECT CurriculumID FROM curriculums WHERE SubjectName = ? AND YearLevel = ? AND Units = ? AND ProgramID = ?");
        $stmtCheck->execute([$addSubjectName, $addYearLevel, $addUnits, $programID]);
        if ($stmtCheck->fetch()) {
            $addSuccess = false;
            $addErrors[] = "Curriculum with the same subject, year, unit, and program already exists.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add subject. Curriculum with the same subject, year, unit, and program already exists.</span>
                    </div>';
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO curriculums (CourseID, SubjectArea, CatalogNo, SubjectName, Units, ProgramID, YearLevel, Semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmtInsert->execute([$addCourseID, $addSubjectArea, $addCatalogNo, $addSubjectName, $addUnits, $programID, $addYearLevel, $addSemester])) {
                $addSuccess = true;
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">Subject "' . htmlspecialchars($addSubjectName) . '" added successfully!</span>
                        </div>';
            } else {
                $addSuccess = false;
                $addErrors[] = "Error adding subject '" . htmlspecialchars($addSubjectName) . "'.";
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">Failed to add subject. ' . (!empty($addErrors) ? implode('<br>', $addErrors) : 'Please try again.') . '</span>
                        </div>';
            }
        }
    } else {
        $addSuccess = false;
        $addErrors[] = "Program '" . htmlspecialchars($addProgramName) . "' not found.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to add subject. Program "' . htmlspecialchars($addProgramName) . '" not found.</span>
                </div>';
    }
}

// Handle Edit Curriculum
if (isset($_POST['btnEdit'])) {
    $editCurriculumID = trim($_POST['editCurriculumID']);
    $editSubjectName = trim($_POST['editSubjectName']);
    $editUnits = trim($_POST['editUnits']);
    $editCourseID = isset($_POST['editCourseID']) ? trim($_POST['editCourseID']) : null;
    $editSubjectArea = isset($_POST['editSubjectArea']) ? trim($_POST['editSubjectArea']) : null;
    $editCatalogNo = isset($_POST['editCatalogNo']) ? trim($_POST['editCatalogNo']) : null;
    $editUnits = isset($_POST['editUnits']) ? trim($_POST['editUnits']) : null;
    $editProgramName = trim($_POST['editProgramName']);
    $editYearLevel = trim($_POST['editYearLevel']);
    $editSemester = isset($_POST['editSemester']) ? trim($_POST['editSemester']) : null;

    // Lookup ProgramID
    $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmt->execute([$editProgramName]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        $programID = $program['ProgramID'];

        // Check for duplicate curriculum with same subject, year, credit unit, and program excluding current record
        $stmtCheck = $conn->prepare("SELECT CurriculumID FROM curriculums WHERE SubjectName = ? AND YearLevel = ? AND Units = ? AND ProgramID = ? AND CurriculumID != ?");
        $stmtCheck->execute([$editSubjectName, $editYearLevel, $editUnits, $programID, $editCurriculumID]);
        if ($stmtCheck->fetch()) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Curriculum with the same subject, year, credit unit, and program already exists.</span>
                </div>';
        } else {
            $stmtUpdate = $conn->prepare("UPDATE curriculums SET CourseID = ?, SubjectArea = ?, CatalogNo = ?, SubjectName = ?, Units = ?, ProgramID = ?, YearLevel = ?, Semester = ? WHERE CurriculumID = ?");
            if ($stmtUpdate->execute([$editCourseID, $editSubjectArea, $editCatalogNo, $editSubjectName, $editUnits, $programID, $editYearLevel, $editSemester, $editCurriculumID])) {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Curriculum updated successfully!</span>
                    </div>';
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to update curriculum. Please try again.</span>
                    </div>';
            }
        }
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Program "' . htmlspecialchars($editProgramName) . '" not found.</span>
            </div>';
    }
}

// Handle Delete Curriculum
if (isset($_POST['deleteCurriculumID'])) {
    $deleteCurriculumID = $_POST['deleteCurriculumID'];
    error_log("Delete request received for CurriculumID: " . $deleteCurriculumID);

    // Check for related records in schedules
    $stmtCheckSchedules = $conn->prepare("SELECT COUNT(*) FROM schedules WHERE CurriculumID = ?");
    $stmtCheckSchedules->execute([$deleteCurriculumID]);
    $countSchedules = $stmtCheckSchedules->fetchColumn();

    // Check for related records in preferredsubjects
    $stmtCheckPreferred = $conn->prepare("SELECT COUNT(*) FROM preferredsubjects WHERE CurriculumID = ?");
    $stmtCheckPreferred->execute([$deleteCurriculumID]);
    $countPreferred = $stmtCheckPreferred->fetchColumn();

    try {
        // Delete related records in schedules
        $stmtDeleteSchedules = $conn->prepare("DELETE FROM schedules WHERE CurriculumID = ?");
        $stmtDeleteSchedules->execute([$deleteCurriculumID]);

        // Delete related records in preferredsubjects
        $stmtDeletePreferred = $conn->prepare("DELETE FROM preferredsubjects WHERE CurriculumID = ?");
        $stmtDeletePreferred->execute([$deleteCurriculumID]);

        // Delete curriculum
        $stmtDelete = $conn->prepare("DELETE FROM curriculums WHERE CurriculumID = ?");
        if ($stmtDelete->execute([$deleteCurriculumID])) {
            error_log("Delete query executed successfully for CurriculumID: " . $deleteCurriculumID);
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">Curriculum deleted successfully!</span>
          </div>';
        } else {
            error_log("Delete query failed for CurriculumID: " . $deleteCurriculumID);
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Failed to delete curriculum. Please try again.</span>
          </div>';
        }
    } catch (PDOException $e) {
        error_log("Delete query exception for CurriculumID: " . $deleteCurriculumID . " - " . $e->getMessage());
        echo 'error: Exception occurred during deletion.';
    }
    exit;
}

// Handle AJAX request for filtered data
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    // AJAX request for filtered data, return HTML table rows only
    $programFilter = isset($_GET['program']) ? $_GET['program'] : '';
    $yearFilter = isset($_GET['year']) ? $_GET['year'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $rowsPerPageOptions = [5, 10, 20, 50, 100];
    $rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = max(0, ($currentPage - 1) * $rowsPerPage);

    $whereClauses = [];
    $queryParams = [];
    $programIDFilter = null;

    if (!empty($programFilter)) {
        $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
        $stmt->execute([$programFilter]);
        $programRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($programRow) {
            $programIDFilter = $programRow['ProgramID'];
            $whereClauses[] = "c.ProgramID = ?";
            $queryParams[] = $programIDFilter;
        } else {
            echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found.</td></tr>']);
            exit;
        }
    }

    if (!empty($yearFilter)) {
        $whereClauses[] = "c.YearLevel = ?";
        $queryParams[] = $yearFilter;
    }

    if (!empty($search)) {
        $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        if (!empty($searchKeywords)) {
            $keywordClauses = [];
            foreach ($searchKeywords as $keyword) {
                $keywordClause = [];
                $keyword = strtolower($keyword); // pre-lowercase to reduce repeated calls
                $keywordClause[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
                $keywordClause[] = "LOWER(c.Units) LIKE LOWER(?)";
                $keywordClause[] = "LOWER(c.YearLevel) LIKE LOWER(?)";
                $keywordClause[] = "LOWER(p.ProgramName) LIKE LOWER(?)";
                // Group OR conditions for this keyword
                $keywordClauses[] = '(' . implode(' OR ', $keywordClause) . ')';

                // Add parameters (with wildcards)
                $likeKeyword = '%' . $keyword . '%';
                $queryParams[] = $likeKeyword;
                $queryParams[] = $likeKeyword;
                $queryParams[] = $likeKeyword;
                $queryParams[] = $likeKeyword;
            }
            // Combine all keyword groups with AND (all keywords must match somewhere)
            $whereClauses[] = '(' . implode(' AND ', $keywordClauses) . ')';
        }
    }

    $whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";


    if (!empty($programFilter) || !empty($yearFilter)) {
        // Use existing filtering logic with program and year filters
        if ($programIDFilter !== null || !empty($search)) {
            $sql = "SELECT c.CurriculumID, c.SubjectName, c.Units, c.YearLevel, p.ProgramName
            FROM curriculums c
            JOIN programs p ON c.ProgramID = p.ProgramID
            $whereString
            ORDER BY c.CurriculumID ASC
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

                if (empty($data)) {
                    // add sql in the string for debug testing.
                    echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found. SQL: ' . $sql . '</td></tr>']);
                    // echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found.</td></tr>']);
                    exit;
                }

                $html = '';
                foreach ($data as $curriculum) {
                    $html .= '<tr>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['SubjectName']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['Units']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['YearLevel']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['Semester']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['ProgramName']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap text-center space-x-2">';
                    $html .= '<button class="text-blue-600 hover:text-blue-900 edit-btn" data-subject="' . htmlspecialchars($curriculum['SubjectName']) . '">Edit</button>';
                    $html .= '<button class="text-red-600 hover:text-red-900 delete-btn" data-subject="' . htmlspecialchars($curriculum['SubjectName']) . '">Delete</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }

                echo json_encode(['html' => $html]);
                exit;

            } catch (PDOException $e) {
                echo json_encode(['html' => '<tr><td colspan="5" class="text-center">Error executing query.</td></tr>']);
                exit;
            }
        } else {
            echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found.</td></tr>']);
            exit;
        }
    } elseif (!empty($search)) {
        // Separate query for search only (no program or year filter)
        $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        if (!empty($searchKeywords)) {
            $likeClauses = [];
            $queryParamsSearch = [];
            foreach ($searchKeywords as $keyword) {
                $likeClauses[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
                $likeClauses[] = "LOWER(c.Units) LIKE LOWER(?)";
                $likeClauses[] = "LOWER(c.Year) LIKE LOWER(?)";
                $likeClauses[] = "LOWER(p.ProgramName) LIKE LOWER(?)";
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
            }
            $whereStringSearch = '(' . implode(' OR ', $likeClauses) . ')';

            $sqlSearch = "SELECT c.CurriculumID, c.SubjectName, c.Unit, c.Year, p.ProgramName
                      FROM curriculums c
                      JOIN programs p ON c.ProgramID = p.ProgramID
                      WHERE " . $whereStringSearch . "
                      ORDER BY c.CurriculumID ASC
                      LIMIT ?, ?";
            $stmtSearch = $conn->prepare($sqlSearch);

            try {
                $paramIndex = 1;
                foreach ($queryParamsSearch as $param) {
                    $stmtSearch->bindValue($paramIndex++, $param);
                }
                $stmtSearch->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
                $stmtSearch->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);

                $stmtSearch->execute();
                $data = $stmtSearch->fetchAll(PDO::FETCH_ASSOC);

                if (empty($data)) {
                    echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found.</td></tr>']);
                    exit;
                }

                $html = '';
                foreach ($data as $curriculum) {
                    $html .= '<tr>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['SubjectName']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['Units']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['YearLevel']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['ProgramName']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap text-center space-x-2">';
                    $html .= '<button class="text-blue-600 hover:text-blue-900 edit-btn" data-subject="' . htmlspecialchars($curriculum['SubjectName']) . '">Edit</button>';
                    $html .= '<button class="text-red-600 hover:text-red-900 delete-btn" data-subject="' . htmlspecialchars($curriculum['SubjectName']) . '">Delete</button>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }

                echo json_encode(['html' => $html]);
                exit;

            } catch (PDOException $e) {
                echo json_encode(['html' => '<tr><td colspan="5" class="text-center">Error executing query.</td></tr>']);
                exit;
            }
        } else {
            echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found.</td></tr>']);
            exit;
        }
    } else {
        echo json_encode(['html' => '<tr><td colspan="5" class="text-center">No curriculums found.</td></tr>']);
        exit;
    }
}

// Load Data for Selected Program with Pagination and Search
$data = [];
$totalRows = 0;
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);
$programFilter = isset($_GET['program']) ? $_GET['program'] : '';
$yearFilter = isset($_GET['year']) ? $_GET['year'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$semesterFilter = isset($_GET['semester']) ? $_GET['semester'] : '';
$unitsFilter = isset($_GET['units']) ? $_GET['units'] : '';

$whereClauses = [];
$queryParams = [];
$programIDFilter = null;

if (!empty($programFilter)) {
    $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmt->execute([$programFilter]);
    $programRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($programRow) {
        $programIDFilter = $programRow['ProgramID'];
        $whereClauses[] = "c.ProgramID = ?";
        $queryParams[] = $programIDFilter;
    } else {
        $totalRows = 0;
        $totalPages = 0;
        $data = [];
    }
}

if (!empty($yearFilter)) {
    $whereClauses[] = "c.YearLevel = ?";
    $queryParams[] = $yearFilter;
}

if (!empty($semesterFilter)) {
    $whereClauses[] = "c.Semester = ?";
    $queryParams[] = $semesterFilter;
}

if (!empty($unitsFilter)) {
    $whereClauses[] = "c.Units = ?";
    $queryParams[] = $unitsFilter;
}

if (!empty($search)) {
    $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($searchKeywords)) {
        $keywordClauses = [];
        foreach ($searchKeywords as $keyword) {
            $keywordClause = [];
            $keyword = strtolower($keyword); // pre-lowercase to reduce repeated calls
            $keywordClause[] = "LOWER(c.SubjectName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(c.Units) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(c.YearLevel) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(p.ProgramName) LIKE LOWER(?)";
            // Group OR conditions for this keyword
            $keywordClauses[] = '(' . implode(' OR ', $keywordClause) . ')';

            // Add parameters (with wildcards)
            $likeKeyword = '%' . $keyword . '%';
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
        }
        // Combine all keyword groups with AND (all keywords must match somewhere)
        $whereClauses[] = '(' . implode(' AND ', $keywordClauses) . ')';
    }
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

if (empty($programFilter) || $programIDFilter !== null) {
    $sql = "SELECT c.CurriculumID, c.SubjectName, c.Units, c.YearLevel, c.Semester, p.ProgramName
        FROM curriculums c
        JOIN programs p ON c.ProgramID = p.ProgramID
        " . $whereString . "
        ORDER BY c.CurriculumID ASC
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

        $countQuery = "SELECT COUNT(*) AS total
                       FROM curriculums c
                       JOIN programs p ON c.ProgramID = p.ProgramID
                       " . $whereString;
        $stmtCount = $conn->prepare($countQuery);
        $stmtCount->execute($queryParams);
        $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $totalRows = $row['total'];
        $totalPages = ceil($totalRows / $rowsPerPage);

    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "<br>";
        echo "SQL: " . $sql . "<br>";
        $totalRows = 0;
        $totalPages = 0;
        $data = [];
    }

} else {
    $totalPages = 0;
}

// Fetch all unique year levels for the filter
$stmtYears = $conn->prepare("SELECT DISTINCT YearLevel FROM curriculums ORDER BY YearLevel");
$stmtYears->execute();
$yearLevels = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

// Fetch all programs for the program filter and add modal dropdown
$stmtPrograms = $conn->prepare("SELECT ProgramName FROM programs ORDER BY ProgramName");
$stmtPrograms->execute();
$programs = $stmtPrograms->fetchAll(PDO::FETCH_COLUMN);

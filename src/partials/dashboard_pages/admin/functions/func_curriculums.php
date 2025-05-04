<?php

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

        $subjectName = trim($row[0]);
        $creditUnit = trim($row[1]);
        $programName = trim($row[2]);
        $yearLevel = trim($row[3]);

        // Lookup ProgramID using PDO
        $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
        $stmt->execute([$programName]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($program) {
            $programID = $program['ProgramID'];

            // Insert into curriculum
            $stmtInsert = $conn->prepare("INSERT INTO curriculums (SubjectName, CreditUnit, ProgramID, Year) VALUES (?, ?, ?, ?)");
            if (!$stmtInsert->execute([$subjectName, $creditUnit, $programID, $yearLevel])) {
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
    $addCreditUnit = trim($_POST['addCreditUnit']);
    $addProgramName = trim($_POST['addProgramName']);
    $addYearLevel = trim($_POST['addYearLevel']);

    // Lookup ProgramID
    $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmt->execute([$addProgramName]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        $programID = $program['ProgramID'];
        $stmtInsert = $conn->prepare("INSERT INTO curriculums (SubjectName, CreditUnit, ProgramID, Year) VALUES (?, ?, ?, ?)");
        if ($stmtInsert->execute([$addSubjectName, $addCreditUnit, $programID, $addYearLevel])) {
            $addSuccess = true;
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Subject "' . htmlspecialchars($addSubjectName) . '" added successfully!</span>
                    </div>';
        } else {
            $addSuccess = false;
            $addErrors[] = "Error adding subject '" . htmlspecialchars($addSubjectName) . "'.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add subject. ' . (!empty($addErrors) ? implode('<br>', $addErrors) : 'Please try again.') . '</span>
                    </div>';
        }
    } else {
        $addSuccess = false;
        $addErrors[] = "Program '" . htmlspecialchars($addProgramName) . "' not found.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to add subject. Program "' . htmlspecialchars($addProgramName) . '" not found.</span>
                </div>';
    }
}

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
        $whereClauses[] = "c.Year = ?";
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
                $keywordClause[] = "LOWER(c.CreditUnit) LIKE LOWER(?)";
                $keywordClause[] = "LOWER(c.Year) LIKE LOWER(?)";
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
            $sql = "SELECT c.CurriculumID, c.SubjectName, c.CreditUnit, c.Year, p.ProgramName
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
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['CreditUnit']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['Year']) . '</td>';
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
                $likeClauses[] = "LOWER(c.CreditUnit) LIKE LOWER(?)";
                $likeClauses[] = "LOWER(c.Year) LIKE LOWER(?)";
                $likeClauses[] = "LOWER(p.ProgramName) LIKE LOWER(?)";
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
                $queryParamsSearch[] = '%' . strtolower($keyword) . '%';
            }
            $whereStringSearch = '(' . implode(' OR ', $likeClauses) . ')';

            $sqlSearch = "SELECT c.CurriculumID, c.SubjectName, c.CreditUnit, c.Year, p.ProgramName
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
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['CreditUnit']) . '</td>';
                    $html .= '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['Year']) . '</td>';
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
    $whereClauses[] = "c.Year = ?";
    $queryParams[] = $yearFilter;
}

if (!empty($search)) {
    $whereClauses[] = "c.SubjectName LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

if (empty($programFilter) || $programIDFilter !== null) {
    $sql = "SELECT c.CurriculumID, c.SubjectName, c.CreditUnit, c.Year, p.ProgramName
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
$stmtYears = $conn->prepare("SELECT DISTINCT Year FROM curriculums ORDER BY Year");
$stmtYears->execute();
$yearLevels = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

// Fetch all programs for the program filter and add modal dropdown
$stmtPrograms = $conn->prepare("SELECT ProgramName FROM programs ORDER BY ProgramName");
$stmtPrograms->execute();
$programs = $stmtPrograms->fetchAll(PDO::FETCH_COLUMN);
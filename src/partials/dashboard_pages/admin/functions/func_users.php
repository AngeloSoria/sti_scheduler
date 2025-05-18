<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

function getDepartments()
{
    $db = new Database();
    $conn = $db->getConnection();
    try {
        $stmt = $conn->prepare("SELECT * FROM departments ORDER BY DepartmentName ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getPrograms()
{
    $db = new Database();
    $conn = $db->getConnection();
    try {
        $stmt = $conn->prepare("SELECT * FROM programs ORDER BY ProgramName ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getCurriculumSubjectsByProgram($programId)
{
    $db = new Database();
    $conn = $db->getConnection();
    try {
        $stmt = $conn->prepare("SELECT * FROM curriculums WHERE ProgramID = ? ORDER BY SubjectName ASC");
        $stmt->execute([$programId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'getUserDetails' && isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    try {
        // Get departmentID and programID from facultymembers
        $stmtFaculty = $conn->prepare("SELECT DepartmentID, ProgramID, FacultyID FROM facultymembers WHERE UserID = ?");
        $stmtFaculty->execute([$userID]);
        $faculty = $stmtFaculty->fetch(PDO::FETCH_ASSOC);

        $departmentID = null;
        $programID = null;
        $facultyID = null;
        if ($faculty) {
            $departmentID = $faculty['DepartmentID'];
            $programID = $faculty['ProgramID'];
            $facultyID = $faculty['FacultyID'];
        }

        // Get preferred subjects for the faculty
        $preferredSubjects = [];
        if ($facultyID) {
            $stmtPrefSubj = $conn->prepare("SELECT CurriculumID FROM preferredsubjects WHERE FacultyID = ?");
            $stmtPrefSubj->execute([$facultyID]);
            $preferredSubjects = $stmtPrefSubj->fetchAll(PDO::FETCH_COLUMN);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'departmentID' => $departmentID,
            'programID' => $programID,
            'preferredSubjects' => $preferredSubjects
        ]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}


if (isset($_POST['action'])) {
    session_start();
    switch ($_POST['action']) {
        case 'addUser':
            $username = trim($_POST['addUsername']);
            $password = $_POST['addPassword'];
            $firstName = trim($_POST['addFirstName']);
            $middleName = trim($_POST['addMiddleName']);
            $lastName = trim($_POST['addLastName']);
            $role = $_POST['addRoleSelect'];
            $department = $_POST['addDepartment'] ?? null;
            $program = $_POST['addProgram'] ?? null;
            $preferredSubjects = $_POST['addPreferredSubjects'] ?? [];
            $profilePic = $_FILES['addProfilePic'] ?? null;
            $type = $_POST['type'] ?? '';

            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

            try {
                // Check if username already exists
                $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE Username = ?");
                $stmtCheck->execute([$username]);
                $count = $stmtCheck->fetchColumn();

                if ($count > 0) {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => "Username already exists. Please choose another."]);
                        exit;
                    } else {
                        $_SESSION['errorMessage'] = "Username already exists. Please choose another.";
                    }
                } else {
                    if ($role === 'faculty' && (empty($department) || empty($program))) {
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => "Department and Program are required for faculty role."]);
                            exit;
                        } else {
                            $_SESSION['errorMessage'] = "Department and Program are required for faculty role.";
                            header("Location: /dashboard?view=users&type=" . urlencode($type));
                            exit;
                        }
                    }

                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $sql = "INSERT INTO users (Username, Password, FirstName, MiddleName, LastName, Role) VALUES (?, ?, ?, ?, ?, ?)";
                    $params = [$username, $hashedPassword, $firstName, $middleName, $lastName, $role];

                    $stmtInsert = $conn->prepare($sql);
                    if ($stmtInsert->execute($params)) {
                        $newUserID = $conn->lastInsertId();

                        // Handle profile picture upload using FileUploader
                        if ($profilePic && $profilePic['error'] === UPLOAD_ERR_OK) {
                            require_once(__DIR__ . '/../../../../functions/FileUploader.php');
                            $uploader = new FileUploader(__DIR__ . '/../../../../uploads/profile_pic');
                            $uploadResult = $uploader->uploadProfilePic($profilePic, $newUserID);
                            if (isset($uploadResult['error'])) {
                                if ($isAjax) {
                                    header('Content-Type: application/json');
                                    echo json_encode(['success' => false, 'message' => $uploadResult['error']]);
                                    exit;
                                } else {
                                    $_SESSION['errorMessage'] = $uploadResult['error'];
                                    header("Location: /dashboard?view=users&type=" . urlencode($type));
                                    exit;
                                }
                            }
                        }

                        if ($role === 'faculty') {
                            // Insert into facultymembers
                            $stmtFaculty = $conn->prepare("INSERT INTO facultymembers (UserID, DepartmentID, ProgramID) VALUES (?, ?, ?)");
                            $stmtFaculty->execute([$newUserID, $department, $program]);

                            $facultyID = $conn->lastInsertId();

                            // Insert preferred subjects
                            if (!empty($preferredSubjects)) {
                                $stmtPrefSubj = $conn->prepare("INSERT INTO preferredsubjects (FacultyID, CurriculumID) VALUES (?, ?)");
                                foreach ($preferredSubjects as $curriculumID) {
                                    $stmtPrefSubj->execute([$facultyID, $curriculumID]);
                                }
                            }
                        }

                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'message' => "User added successfully!"]);
                            exit;
                        } else {
                            $_SESSION['successMessage'] = "User added successfully!";
                        }
                    } else {
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => "Failed to add user. Please try again."]);
                            exit;
                        } else {
                            $_SESSION['errorMessage'] = "Failed to add user. Please try again.";
                        }
                    }
                }
            } catch (PDOException $e) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => "Exception occurred during user addition. " . $e->getMessage()]);
                    exit;
                } else {
                    $_SESSION['errorMessage'] = "Exception occurred during user addition. " . $e->getMessage();
                }
            }
            if (!$isAjax) {
                header("Location: /dashboard?view=users&type=" . urlencode($type));
                exit;
            }

        case 'deleteUser':
            $deleteUserID = $_POST['deleteUserID'];

            try {
                // Find FacultyID for the user
                $stmtFaculty = $conn->prepare("SELECT FacultyID FROM facultymembers WHERE UserID = ?");
                $stmtFaculty->execute([$deleteUserID]);
                $faculty = $stmtFaculty->fetch(PDO::FETCH_ASSOC);

                if ($faculty) {
                    $facultyID = $faculty['FacultyID'];

                    // Delete related records in preferredsubjects
                    $stmtDeletePreferred = $conn->prepare("DELETE FROM preferredsubjects WHERE FacultyID = ?");
                    $stmtDeletePreferred->execute([$facultyID]);

                    // Delete related records in schedules
                    $stmtDeleteSchedules = $conn->prepare("DELETE FROM schedules WHERE FacultyID = ?");
                    $stmtDeleteSchedules->execute([$facultyID]);

                    // Delete from facultymembers
                    $stmtDeleteFaculty = $conn->prepare("DELETE FROM facultymembers WHERE UserID = ?");
                    $stmtDeleteFaculty->execute([$deleteUserID]);
                }

                // Delete user
                $stmtDeleteUser = $conn->prepare("DELETE FROM users WHERE UserID = ?");
                if ($stmtDeleteUser->execute([$deleteUserID])) {
                    $deleteSuccess = true;
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">User deleted successfully!</span>
                          </div>';
                } else {
                    $deleteSuccess = false;
                    $deleteErrors[] = "Failed to delete user.";
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">Failed to delete user. Please try again.</span>
                          </div>';
                }
            } catch (PDOException $e) {
                $deleteSuccess = false;
                $deleteErrors[] = "Exception: " . $e->getMessage();
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Exception occurred during deletion.</span>
                      </div>';
            }
            exit;

        case 'editUser':
            $editUserID = trim($_POST['editUserID']);
            $editFirstName = trim($_POST['editFirstName']);
            $editMiddleName = trim($_POST['editMiddleName']);
            $editLastName = trim($_POST['editLastName']);
            $editUsername = trim($_POST['editUsername']);
            $editPassword = $_POST['editPassword'] ?? '';
            $profilePic = $_FILES['editProfilePic'] ?? null;
            $editDepartment = $_POST['editDepartment'] ?? null;
            $editProgram = $_POST['editProgram'] ?? null;
            $editPreferredSubjects = $_POST['editPreferredSubjects'] ?? [];

            try {
                // Check if username is unique except for current user
                $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM users WHERE Username = ? AND UserID != ?");
                $stmtCheck->execute([$editUsername, $editUserID]);
                $count = $stmtCheck->fetchColumn();

                if ($count > 0) {
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">Username already exists. Please choose another.</span>
                          </div>';
                } else {
                    $fieldsToUpdate = "FirstName = ?, MiddleName = ?, LastName = ?";
                    $params = [$editFirstName, $editMiddleName, $editLastName];

                    // Handle password update if provided
                    if (!empty($editPassword)) {
                        $hashedPassword = password_hash($editPassword, PASSWORD_DEFAULT);
                        $fieldsToUpdate .= ", Password = ?";
                        $params[] = $hashedPassword;
                    }

                    // Handle profile picture upload
                    if ($profilePic && $profilePic['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $profilePic['tmp_name'];
                        $fileExt = strtolower(pathinfo($profilePic['name'], PATHINFO_EXTENSION));
                        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

                        if (in_array($fileExt, $allowedExts)) {
                            $profilePicData = file_get_contents($fileTmpPath);
                            if ($profilePicData === false) {
                                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                        <strong class="font-bold">Error!</strong>
                                        <span class="block sm:inline">Failed to read profile picture data.</span>
                                      </div>';
                                exit;
                            }
                            $fieldsToUpdate .= ", ProfilePic = ?";
                            $params[] = $profilePicData;
                        } else {
                            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                    <strong class="font-bold">Error!</strong>
                                    <span class="block sm:inline">Invalid profile picture format. Allowed: jpg, jpeg, png, gif.</span>
                                  </div>';
                            exit;
                        }
                    }

                    if (strpos($editUsername, '*') === false) {
                        $fieldsToUpdate .= ", Username = ?";
                        $params[] = $editUsername;
                    }

                    $params[] = $editUserID;

                    $sql = "UPDATE users SET $fieldsToUpdate WHERE UserID = ?";
                    $stmtUpdate = $conn->prepare($sql);

                    if ($stmtUpdate->execute($params)) {
                        // Update facultymembers and preferredsubjects if faculty role
                        $stmtRole = $conn->prepare("SELECT Role FROM users WHERE UserID = ?");
                        $stmtRole->execute([$editUserID]);
                        $role = $stmtRole->fetchColumn();

                        if ($role === 'faculty') {
                            // Update facultymembers
                            $stmtFacultyCheck = $conn->prepare("SELECT FacultyID FROM facultymembers WHERE UserID = ?");
                            $stmtFacultyCheck->execute([$editUserID]);
                            $faculty = $stmtFacultyCheck->fetch(PDO::FETCH_ASSOC);

                            if ($faculty) {
                                $facultyID = $faculty['FacultyID'];
                                $stmtUpdateFaculty = $conn->prepare("UPDATE facultymembers SET DepartmentID = ?, ProgramID = ? WHERE UserID = ?");
                                $stmtUpdateFaculty->execute([$editDepartment, $editProgram, $editUserID]);

                                // Delete existing preferred subjects
                                $stmtDeletePrefSubj = $conn->prepare("DELETE FROM preferredsubjects WHERE FacultyID = ?");
                                $stmtDeletePrefSubj->execute([$facultyID]);

                                // Insert new preferred subjects
                                if (!empty($editPreferredSubjects)) {
                                    $stmtInsertPrefSubj = $conn->prepare("INSERT INTO preferredsubjects (FacultyID, CurriculumID) VALUES (?, ?)");
                                    foreach ($editPreferredSubjects as $curriculumID) {
                                        $stmtInsertPrefSubj->execute([$facultyID, $curriculumID]);
                                    }
                                }
                            } else {
                                // Insert new facultymembers record if not exists
                                $stmtInsertFaculty = $conn->prepare("INSERT INTO facultymembers (UserID, DepartmentID, ProgramID) VALUES (?, ?, ?)");
                                $stmtInsertFaculty->execute([$editUserID, $editDepartment, $editProgram]);
                                $facultyID = $conn->lastInsertId();

                                // Insert preferred subjects
                                if (!empty($editPreferredSubjects)) {
                                    $stmtInsertPrefSubj = $conn->prepare("INSERT INTO preferredsubjects (FacultyID, CurriculumID) VALUES (?, ?)");
                                    foreach ($editPreferredSubjects as $curriculumID) {
                                        $stmtInsertPrefSubj->execute([$facultyID, $curriculumID]);
                                    }
                                }
                            }
                        }

                        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">User updated successfully!</span>
                              </div>';
                    } else {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline">Failed to update user. Please try again.</span>
                              </div>';
                    }
                }
            } catch (PDOException $e) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Exception occurred during update.</span>
                      </div>';
            }
            exit;

        default:
            // Unknown action
            break;
    }
}

// Fetch users with pagination and search
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);

$whereClauses = [];
$queryParams = [];

if (!empty($typeFilter)) {
    $whereClauses[] = "Role = ?";
    $queryParams[] = $typeFilter;
}

if (!empty($search)) {
    $searchKeywords = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    if (!empty($searchKeywords)) {
        $keywordClauses = [];
        foreach ($searchKeywords as $keyword) {
            $keywordClause = [];
            $keyword = strtolower($keyword);
            $keywordClause[] = "LOWER(FirstName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(LastName) LIKE LOWER(?)";
            $keywordClause[] = "LOWER(Username) LIKE LOWER(?)";
            $keywordClauses[] = '(' . implode(' OR ', $keywordClause) . ')';
            $likeKeyword = '%' . $keyword . '%';
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
            $queryParams[] = $likeKeyword;
        }
        $whereClauses[] = '(' . implode(' AND ', $keywordClauses) . ')';
    }
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

$sql = "SELECT * FROM users $whereString ORDER BY UserID ASC LIMIT ?, ?";
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

    $countQuery = "SELECT COUNT(*) AS total FROM users $whereString";
    $stmtCount = $conn->prepare($countQuery);
    $stmtCount->execute($queryParams);
    $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $totalRows = $row['total'];
    $totalPages = ceil($totalRows / $rowsPerPage);

} catch (PDOException $e) {
    echo "Error executing query: " . $e->getMessage() . "<br>";
    $totalRows = 0;
    $totalPages = 0;
    $data = [];
}
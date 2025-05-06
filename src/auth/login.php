<?php

declare(strict_types=1);

$db = new Database();
$conn = $db->getConnection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form is submitted for registration
    if (isset($_POST['context'])) {
        switch ($_POST['context']) {
            case 'login':
                $username = isset($_POST['username']) ? trim($_POST['username']) : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';


                // Basic validation
                if (empty($username) || empty($password)) {
                    $errorMessage = 'Please fill in all required fields.';
                } else {
                    try {
                        $sql = "SELECT * FROM users WHERE Username = :username";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':username', $username);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user && password_verify($password, $user['Password'])) {
                            echo 'FOO';
                            $_SESSION['user'] = [
                                'id' => $user['UserID'],
                                'username' => $user['Username'],
                                'role' => $user['Role'],
                                'profilepic' => $user['ProfilePic']
                                    ? "data:image/jpeg;base64," . base64_encode($user['ProfilePic'])
                                    : "/assets/img/default-profile.png",
                            ];
                            header("Location: /dashboard");
                            exit();
                        } else {
                            $errorMessage = 'Invalid username or password.';
                        }
                    } catch (PDOException $e) {
                        $errorMessage = 'Error during login: ' . $e->getMessage();
                    }
                }
                break;
            case 'register':
                $successMessage = '';
                $errorMessage = '';
                // Validate and sanitize inputs
                $username = isset($_POST['username']) ? trim($_POST['username']) : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
                $middleName = isset($_POST['middleName']) ? trim($_POST['middleName']) : '';
                $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
                $role = isset($_POST['role']) ? trim($_POST['role']) : '';
                $department = isset($_POST['department']) ? trim($_POST['department']) : null;
                $program = isset($_POST['program']) ? trim($_POST['program']) : null;

                // Basic validation
                if (empty($username) || empty($password) || empty($firstName) || empty($lastName) || empty($role)) {
                    $errorMessage = 'Please fill in all required fields.';
                } else {
                    // Hash the password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Handle profile picture upload if provided
                    $profilePicData = null;
                    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['profilePic']['tmp_name'];
                        $profilePicData = file_get_contents($fileTmpPath);
                        if ($profilePicData === false) {
                            $errorMessage = 'Failed to read profile picture data.';
                        }
                    }

                    if (empty($errorMessage)) {
                        try {
                            $sql = "INSERT INTO users (Username, Password, FirstName, MiddleName, LastName, Role, ProfilePic) VALUES (:username, :password, :firstName, :middleName, :lastName, :role, :profilePic)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':password', $hashedPassword);
                            $stmt->bindParam(':firstName', $firstName);
                            $stmt->bindParam(':middleName', $middleName);
                            $stmt->bindParam(':lastName', $lastName);
                            $stmt->bindParam(':role', $role);
                            if ($profilePicData !== null) {
                                $stmt->bindParam(':profilePic', $profilePicData, PDO::PARAM_LOB);
                            } else {
                                $null = null;
                                $stmt->bindParam(':profilePic', $null, PDO::PARAM_NULL);
                            }

                            $stmt->execute();
                            $lastUserId = $conn->lastInsertId();

                            if ($role === 'faculty') {
                                if (empty($department) || empty($program)) {
                                    $errorMessage = 'Please select both Department and Program for faculty role.';
                                } else {
                                    try {
                                        $facultyInsertSql = "INSERT INTO FacultyMembers (DepartmentID, ProgramID, UserID) VALUES (:department, :program, :userId)";
                                        $facultyStmt = $conn->prepare($facultyInsertSql);
                                        $facultyStmt->bindParam(':department', $department);
                                        $facultyStmt->bindParam(':program', $program);
                                        $facultyStmt->bindParam(':userId', $lastUserId);
                                        $facultyStmt->execute();
                                    } catch (PDOException $e) {
                                        $errorMessage = 'Error during faculty registration: ' . $e->getMessage();
                                    }
                                }
                            }

                            if (empty($errorMessage)) {
                                $successMessage = 'Registration successful!';
                                // Redirect to the same page to prevent form resubmission
                                header("Location: " . $_SERVER['REQUEST_URI']);
                                exit();
                            }
                        } catch (PDOException $e) {
                            $errorMessage = 'Error during registration: ' . $e->getMessage();
                        }
                    }
                }
                break;
            default:
                $errorMessage = 'Invalid form submission.';
                break;
        }
    }

}

if (isset($_SESSION['user'])) {
    header("Location: /dashboard");
    exit();
}
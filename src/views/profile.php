<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

$userId = $_SESSION['user']['id']; // Ensure $userId is always from the session

$success = null;
$error = null;
$editMode = isset($_POST['editProfile']) || isset($_SESSION['edit_profile_error']); // Track if edit mode should be active
unset($_SESSION['edit_profile_error']); // Clear the session error

// Initialize $user with default empty values to avoid undefined variable warnings
$user = [
    'Username' => '',
    'Role' => '',
    'FirstName' => '',
    'MiddleName' => '',
    'LastName' => '',
    'ProfilePic' => null,
];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProfile'])) {
    $firstName = trim($_POST['firstName']);
    $middleName = trim($_POST['middleName']);
    $lastName = trim($_POST['lastName']);
    $username = trim($_POST['username']);

    // Validate inputs (basic example)
    if (empty($firstName) || empty($lastName) || empty($username)) {
        $error = "First Name, Last Name, and Username are required.";
        $editMode = true;
    } else {
        // Check if username is taken by another user
        $stmtCheck = $conn->prepare("SELECT UserID FROM users WHERE Username = :username AND UserID != :userId");
        $stmtCheck->bindParam(':username', $username);
        $stmtCheck->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtCheck->execute();
        if ($stmtCheck->fetch()) {
            // Username is taken, set error message and redirect to refresh page and reset form
            $_SESSION['error'] = "Username is already taken.";
            $_SESSION['edit_profile_error'] = true; // Set session to re-enter edit mode
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            require_once __DIR__ . '/../functions/FileUploader.php';

            $fileUploader = new FileUploader('src/uploads/profile_pic/');

            $profilePicPath = null;
            if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $fileUploader->uploadProfilePic($_FILES['profilePic'], $userId);
                if (isset($uploadResult['error'])) {
                    $error = $uploadResult['error'];
                    $editMode = true;
                } else {
                    $profilePicPath = $uploadResult['file_path'];
                }
            }

            try {
                if ($profilePicPath !== null) {
                    $stmtUpdate = $conn->prepare("UPDATE users SET FirstName = :firstName, MiddleName = :middleName, LastName = :lastName, Username = :username, ProfilePic = :profilePic WHERE UserID = :userId");
                    $stmtUpdate->bindParam(':profilePic', $profilePicPath);
                } else {
                    $stmtUpdate = $conn->prepare("UPDATE users SET FirstName = :firstName, MiddleName = :middleName, LastName = :lastName, Username = :username WHERE UserID = :userId");
                }
                $stmtUpdate->bindParam(':firstName', $firstName);
                $stmtUpdate->bindParam(':middleName', $middleName);
                $stmtUpdate->bindParam(':lastName', $lastName);
                $stmtUpdate->bindParam(':username', $username);
                $stmtUpdate->bindParam(':userId', $userId, PDO::PARAM_INT);

                if ($stmtUpdate->execute()) {
                    $success = "Profile updated successfully.";
                    // Refresh user data from the database
                    $stmt = $conn->prepare("SELECT Username, Role, FirstName, MiddleName, LastName, ProfilePic FROM users WHERE UserID = :userId");
                    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    // Do not redirect to keep success message and stay in view mode
                } else {
                    $error = "Failed to update profile. Please try again.";
                    $editMode = true;
                }
            } catch (Exception $e) {
                $error = "Failed to update profile: " . $e->getMessage();
                $editMode = true;
            }
        }
    }
} else {
    try {
        $stmt = $conn->prepare("SELECT Username, Role, FirstName, MiddleName, LastName, ProfilePic FROM users WHERE UserID = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            throw new Exception("User not found.");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $user = [
            'Username' => '',
            'Role' => '',
            'FirstName' => '',
            'MiddleName' => '',
            'LastName' => '',
            'ProfilePic' => null,
        ];
    }
}

function getFullName($user)
{
    $fullName = $user['FirstName'];
    if (!empty($user['MiddleName'])) {
        $fullName .= ' ' . $user['MiddleName'];
    }
    $fullName .= ' ' . $user['LastName'];
    return $fullName;
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include_once __DIR__ . '../../partials/head.php'; ?>

<style>
    .gaming-cover-bg {
        background: linear-gradient(135deg, #1e3a8a, #2563eb, #1e40af);
        position: relative;
        overflow: hidden;
        z-index: 0;
    }

    .profile-pic-container {
        position: relative;
        width: 8rem;
        max-width: 100%;
        height: 8rem;
        max-height: 8rem;
        border-radius: 9999px;
        overflow: hidden;
        border: 4px solid white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        margin: 0 auto;
        cursor: pointer;
        /* Add pointer cursor to indicate clickable */
    }

    .upload-overlay {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 9999px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .profile-pic-container:hover .upload-overlay,
    .upload-overlay.visible {
        opacity: 1;
    }

    .gaming-cover-bg::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: repeating-radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0, rgba(255, 255, 255, 0.1) 10px, transparent 10px, transparent 20px);
        animation: pulse 10s linear infinite;
        z-index: 0;
    }

    @keyframes pulse {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<body
    class="bg-gradient-to-r from-lapis-lazuli-4 via-lapis-lazuli-3 to-lapis-lazuli-4 font-sans min-h-screen flex flex-col">

    <?php include __DIR__ . '../../partials/header.php'; ?>

    <main class="flex-grow w-full max-w-full px-6 py-8">
        <?php
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>
        <?php if ($error): ?>
            <div id="errorAlert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($success): ?>
            <div id="successAlert"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="relative w-full max-w-7xl mx-auto">
            <div class="h-48 rounded-t-xl shadow-inner gaming-cover-bg relative z-0"></div>
            <div class="relative z-10" style="margin-top: -60px;">
                <div class="profile-pic-container mx-auto md:mx-0 relative" title="Change Photo">
                    <?php if (!empty($user['ProfilePic'])): ?>
                        <img id="profilePicDisplay" src="<?= htmlspecialchars($user['ProfilePic']) ?>" alt="Profile Picture"
                            class="object-cover w-full h-full rounded-full" />
                    <?php else: ?>
                        <div id="profilePicDisplay"
                            class="flex items-center justify-center w-full h-full bg-gray-200 text-gray-500 text-6xl font-bold rounded-full">
                            <?= strtoupper(substr($user['Username'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($editMode): ?>
                        <label for="profilePic" id="profilePicLabel" title="Change Profile Picture"
                            class="absolute inset-0 rounded-full flex items-center justify-center bg-black bg-opacity-0 transition-opacity cursor-pointer opacity-0 hover:opacity-50">
                        </label>
                        <input type="file" name="profilePic" id="profilePic" accept="image/*" class="hidden" />
                    <?php else: ?>
                        <div
                            class="absolute inset-0 rounded-full flex items-center justify-center bg-black bg-opacity-0 transition-opacity cursor-pointer">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="profileView"
                class="flex flex-col md:flex-row items-center w-full px-6 md:px-0 mt-0 max-w-7xl mx-auto <?= $editMode ? 'hidden' : '' ?>">
                <div class="flex flex-col justify-start flex-grow text-center md:text-left">
                    <h1 class="text-3xl font-extrabold text-lapis-lazuli-3">
                        <?= htmlspecialchars(getFullName($user)) ?>
                    </h1>
                    <p class="text-lg text-gray-700 mt-1">@<?= htmlspecialchars($user['Username']) ?></p>
                </div>
                <div class="mt-4 md:mt-0 md:ml-auto">
                    <form method="POST" action="">
                        <button type="submit" name="editProfile" id="editProfileBtn"
                            class="flex items-center px-6 py-3 bg-lapis-lazuli-3 text-white rounded-lg shadow hover:bg-lapis-lazuli-4 focus:outline-none focus:ring-4 focus:ring-lapis-lazuli-5 transition transform hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.232 5.232l3.536 3.536M9 11l6 6L4 21l1-7 7-7z" />
                            </svg>
                            Edit Profile
                        </button>
                    </form>
                </div>
            </div>

            <form id="profileEditForm" method="POST" action="" enctype="multipart/form-data"
                class="space-y-6 max-w-7xl mx-auto mt-12 px-6 <?= $editMode ? '' : 'hidden' ?>">
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:space-x-6">
                        <div class="flex flex-col w-full md:w-1/3">
                            <label for="firstName" class="block text-gray-700 font-semibold mb-1">First Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="firstName" id="firstName"
                                value="<?= htmlspecialchars($user['FirstName']) ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lapis-lazuli-3"
                                required />
                        </div>
                        <div class="flex flex-col w-full md:w-1/3">
                            <label for="middleName" class="block text-gray-700 font-semibold mb-1">Middle Name</label>
                            <input type="text" name="middleName" id="middleName"
                                value="<?= htmlspecialchars($user['MiddleName']) ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lapis-lazuli-3" />
                        </div>
                        <div class="flex flex-col w-full md:w-1/3">
                            <label for="lastName" class="block text-gray-700 font-semibold mb-1">Last Name <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="lastName" id="lastName"
                                value="<?= htmlspecialchars($user['LastName']) ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lapis-lazuli-3"
                                required />
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:space-x-6">
                        <div class="flex flex-col w-full md:w-1/2">
                            <label for="username" class="block text-gray-700 font-semibold mb-1">Username <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="username" id="username"
                                value="<?= htmlspecialchars($user['Username']) ?>"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lapis-lazuli-3"
                                required />
                            <span id="usernameError" class="text-red-600 text-sm mt-1 hidden">Username is already
                                taken.</span>
                        </div>
                        <div class="flex flex-col w-full md:w-1/2">
                            <label for="profilePic" class="block text-gray-700 font-semibold mb-1">Profile
                                Picture</label>
                            <input type="file" name="profilePic" id="profilePic" accept="image/*"
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lapis-lazuli-3" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" id="cancelEditBtn"
                        class="px-6 py-3 border border-gray-300 rounded-lg shadow hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-lapis-lazuli-5 transition transform hover:scale-105">
                        Cancel
                    </button>
                    <button type="submit" name="updateProfile"
                        class="px-6 py-3 bg-lapis-lazuli-3 text-white rounded-lg shadow hover:bg-lapis-lazuli-4 focus:outline-none focus:ring-4 focus:ring-lapis-lazuli-5 transition transform hover:scale-105">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php
    include __DIR__ . '../../partials/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Profile picture elements
            const profilePicInput = document.getElementById('profilePic');
            const profilePicDisplay = document.getElementById('profilePicDisplay');
            const profilePicContainer = document.querySelector('.profile-pic-container');

            // Form elements
            const editProfileBtn = document.getElementById('editProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profileView = document.getElementById('profileView');
            const profileEditForm = document.getElementById('profileEditForm');

            // Alert elements
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            // Username validation elements
            const usernameInput = document.getElementById('username');
            const usernameError = document.getElementById('usernameError');

            let isEditModeActive = <?= $editMode ? 'true' : 'false' ?>; // Track edit mode in JS

            // Function to update profile picture preview
            const updateProfilePicPreview = (file) => {
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        profilePicDisplay.src = e.target.result;
                        // Remove the default text/icon if a picture is loaded
                        profilePicDisplay.textContent = '';
                        profilePicDisplay.classList.remove('flex', 'items-center', 'justify-center', 'bg-gray-200', 'text-gray-500', 'text-6xl', 'font-bold');
                        profilePicDisplay.classList.add('object-cover', 'w-full', 'h-full');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Revert to the initial display
                    <?php if (!empty($user['ProfilePic'])): ?>
                        profilePicDisplay.src = 'data:image/jpeg;base64,<?= base64_encode($user['ProfilePic']) ?>';
                        profilePicDisplay.classList.add('object-cover', 'w-full', 'h-full');
                        profilePicDisplay.classList.remove('flex', 'items-center', 'justify-center', 'bg-gray-200', 'text-gray-500', 'text-6xl', 'font-bold');
                    <?php else: ?>
                        profilePicDisplay.textContent = '<?= strtoupper(substr($user['Username'], 0, 1)) ?>';
                        profilePicDisplay.className = 'flex items-center justify-center w-full h-full bg-gray-200 text-gray-500 text-6xl font-bold rounded-full';
                    <?php endif; ?>
                }
            };

            // Event listener for profile picture input change (for preview)
            profilePicInput.addEventListener('change', function () {
                const file = this.files[0];
                updateProfilePicPreview(file);
            });

            // Hide success and error alerts after 3 seconds
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 3000);
            }
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.display = 'none';
                }, 3000);
            }

            // Username validation simulation (you should handle this server-side as well)
            if (usernameInput && usernameError) {
                usernameInput.addEventListener('input', () => {
                    const takenUsernames = ['admin', 'user', 'test']; // example taken usernames
                    if (takenUsernames.includes(usernameInput.value.trim().toLowerCase())) {
                        usernameError.classList.remove('hidden');
                    } else {
                        usernameError.classList.add('hidden');
                    }
                });
            }

            // Toggle between view and edit modes
            if (editProfileBtn && cancelEditBtn && profileView && profileEditForm) {
                editProfileBtn.addEventListener('click', () => {
                    profileView.classList.add('hidden');
                    profileEditForm.classList.remove('hidden');
                    isEditModeActive = true;
                    profilePicContainer.style.cursor = 'pointer';
                });

                cancelEditBtn.addEventListener('click', () => {
                    profileEditForm.classList.add('hidden');
                    profileView.classList.remove('hidden');
                    isEditModeActive = false;
                    profilePicContainer.style.cursor = 'default';
                    profilePicInput.value = ''; // Clear the file input
                    // Reset the preview
                    <?php if (!empty($user['ProfilePic'])): ?>
                        profilePicDisplay.src = 'data:image/jpeg;base64,<?= base64_encode($user['ProfilePic']) ?>';
                        profilePicDisplay.classList.add('object-cover', 'w-full', 'h-full');
                        profilePicDisplay.classList.remove('flex', 'items-center', 'justify-center', 'bg-gray-200', 'text-gray-500', 'text-6xl', 'font-bold');
                    <?php else: ?>
                        profilePicDisplay.textContent = '<?= strtoupper(substr($user['Username'], 0, 1)) ?>';
                        profilePicDisplay.className = 'flex items-center justify-center w-full h-full bg-gray-200 text-gray-500 text-6xl font-bold rounded-full';
                    <?php endif; ?>
                });
            }

            // Control click on profile picture to open file explorer only in edit mode
            if (profilePicContainer && profilePicInput) {
                profilePicContainer.addEventListener('click', () => {
                    if (isEditModeActive) {
                        profilePicInput.click();
                    }
                });
            }

            // Form submission validation
            if (profileEditForm && usernameError) {
                profileEditForm.addEventListener('submit', (e) => {
                    if (!usernameError.classList.contains('hidden')) {
                        e.preventDefault();
                        alert('Please provide a unique username before saving changes.');
                    }
                });
            }

            // Initially set the edit form visibility based on PHP $editMode
            if (isEditModeActive) {
                profileView.classList.add('hidden');
                profileEditForm.classList.remove('hidden');
                profilePicContainer.style.cursor = 'pointer';
            }
        });
    </script>

</body>

</html>
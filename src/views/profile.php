<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit();
}

require_once __DIR__ . '/../../config/dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

$userId = $userid ?? $_SESSION['user']['id'];

try {
    $stmt = $conn->prepare("SELECT Username, Role, FirstName, MiddleName, LastName, ProfilePic FROM Users WHERE UserID = :userId");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        throw new Exception("User not found.");
    }
} catch (Exception $e) {
    $error = $e->getMessage();
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

<body class="bg-gray-100 font-sans">

    <div class="h-screen flex flex-col">

        <div class="flex-grow">
            <?php include __DIR__ . '../../partials/header.php'; ?>

            <section class="bg-white py-12 max-w-4xl mx-auto rounded-lg shadow-md mt-10">
                <?php if (isset($error)): ?>
                <div class="text-red-600 text-center py-4">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php else: ?>
                <div class="flex flex-col items-center p-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-lapis-lazuli-3 shadow-md">
                        <?php if (!empty($user['ProfilePic'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($user['ProfilePic']) ?>"
                            alt="Profile Picture" class="object-cover w-full h-full" />
                        <?php else: ?>
                        <div
                            class="flex items-center justify-center w-full h-full bg-gray-200 text-gray-500 text-6xl font-bold">
                            <?= strtoupper(substr($user['Username'], 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-3xl font-bold mt-4 text-lapis-lazuli-3"><?= htmlspecialchars(getFullName($user)) ?>
                    </h1>
                    <p class="text-gray-600 mt-1">@<?= htmlspecialchars($user['Username']) ?></p>
                    <p class="text-gray-500 mt-2 uppercase tracking-wide text-sm"><?= htmlspecialchars($user['Role']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </section>
        </div>

        <?php include __DIR__ . '../../partials/footer.php'; ?>

    </div>

</body>

</html>
<?php

require_once(__DIR__ . "../../../../../../config/dbConnection.php");

$db = new Database();
$conn = $db->getConnection();

$addSuccess = null;
$addErrors = [];

// Handle Manual Add
if (isset($_POST['btnAdd'])) {
    $addRoomName = trim($_POST['addRoomName']);

    // Check if room already exists
    $stmtCheck = $conn->prepare("SELECT RoomID FROM rooms WHERE RoomName = ?");
    $stmtCheck->execute([$addRoomName]);
    if ($stmtCheck->fetch()) {
        $addSuccess = false;
        $addErrors[] = "Room '" . htmlspecialchars($addRoomName) . "' already exists.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to add room. Room "' . htmlspecialchars($addRoomName) . '" already exists.</span>
                </div>';
    } else {
        $stmtInsert = $conn->prepare("INSERT INTO rooms (RoomName) VALUES (?)");
        if ($stmtInsert->execute([$addRoomName])) {
            $addSuccess = true;
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Room "' . htmlspecialchars($addRoomName) . '" added successfully!</span>
                    </div>';
        } else {
            $addSuccess = false;
            $addErrors[] = "Error adding room '" . htmlspecialchars($addRoomName) . "'.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add room. Please try again.</span>
                    </div>';
        }
    }
}

// Handle Edit Room
if (isset($_POST['btnEdit'])) {
    $editRoomID = trim($_POST['editRoomID']);
    $editRoomName = trim($_POST['editRoomName']);

    // Check if room name already exists for another ID
    $stmtCheck = $conn->prepare("SELECT RoomID FROM rooms WHERE RoomName = ? AND RoomID != ?");
    $stmtCheck->execute([$editRoomName, $editRoomID]);
    if ($stmtCheck->fetch()) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Room name "' . htmlspecialchars($editRoomName) . '" already exists.</span>
            </div>';
    } else {
        $stmtUpdate = $conn->prepare("UPDATE rooms SET RoomName = ? WHERE RoomID = ?");
        if ($stmtUpdate->execute([$editRoomName, $editRoomID])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">Room updated successfully!</span>
                </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert" id="message">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to update room. Please try again.</span>
                </div>';
        }
    }
}

// Handle Delete Room
if (isset($_POST['deleteRoomID'])) {
    $deleteRoomID = $_POST['deleteRoomID'];

    // Check for related records if needed (e.g., schedules)
    // For now, assume no related records or add similar checks as needed

    $stmtDelete = $conn->prepare("DELETE FROM rooms WHERE RoomID = ?");
    if ($stmtDelete->execute([$deleteRoomID])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Room deleted successfully!</span>
              </div>';
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Failed to delete room. Please try again.</span>
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
    $whereClauses[] = "RoomName LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

try {
    $sql = "SELECT RoomID, RoomName FROM rooms $whereString ORDER BY RoomID ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $paramIndex = 1;
    foreach ($queryParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM rooms $whereString";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->execute($queryParams);
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $rowsPerPage);

} catch (PDOException $e) {
    $rooms = [];
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Failed to load rooms.</span>
          </div>';
}

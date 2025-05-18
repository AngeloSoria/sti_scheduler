<?php
require_once(__DIR__ . "../../../../../../config/dbConnection.php");

header('Content-Type: application/json');

if (!isset($_GET['curriculumID'])) {
    echo json_encode(['error' => 'Missing curriculumID parameter']);
    exit;
}

$curriculumID = $_GET['curriculumID'];

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("SELECT c.CurriculumID, c.CourseID, c.SubjectArea, c.CatalogNo, c.SubjectName, c.Units, c.YearLevel, c.Semester, p.ProgramName
                            FROM curriculums c
                            JOIN programs p ON c.ProgramID = p.ProgramID
                            WHERE c.CurriculumID = ?");
    $stmt->execute([$curriculumID]);
    $curriculum = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($curriculum) {
        echo json_encode($curriculum);
    } else {
        echo json_encode(['error' => 'Curriculum not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
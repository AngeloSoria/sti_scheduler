<?php
require_once __DIR__ . '/functions/func_schedules.php';

if (isset($_GET['action']) && $_GET['action'] === 'getPreferredSubjects' && isset($_GET['facultyId'])) {
    header('Content-Type: application/json');
    $facultyId = $_GET['facultyId'];
    $semesterId = isset($_GET['semesterId']) ? $_GET['semesterId'] : null;
    $preferredSubjects = getPreferredSubjectsByFaculty($facultyId, $semesterId);
    echo json_encode($preferredSubjects);
    exit;
}

// You can add other AJAX handlers here if needed
?>
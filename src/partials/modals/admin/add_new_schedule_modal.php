<?php

require_once __DIR__ . '/../../dashboard_pages/admin/functions/func_schedules.php';

// Handle AJAX request for preferred subjects by FacultyID and Semester
if (isset($_GET['action']) && $_GET['action'] === 'getPreferredSubjects' && isset($_GET['facultyId'])) {
    header('Content-Type: application/json');
    $facultyId = $_GET['facultyId'];
    $semester = isset($_GET['semester']) ? $_GET['semester'] : getActiveSemesterValue();
    $preferredSubjects = getPreferredSubjectsByFaculty($facultyId, $semester);
    echo json_encode($preferredSubjects);
    exit;
}
$rooms = getRooms();
?>

<div id="addNewScheduleModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-md max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Add New Schedule
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="addNewScheduleModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <form method="POST" id="addNewScheduleForm">
                    <input type="hidden" name="context" value="addSchedule">
                    <div id="conflictError"
                        class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert"></div>
                    <div class="mb-4">
                        <label for="addSectionID" class="block text-gray-700 text-sm font-bold mb-2">
                            Section:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="addSectionID" id="addSectionID" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a Section</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?php echo htmlspecialchars($section['SectionID']); ?>"
                                    data-programid="<?php echo htmlspecialchars($section['ProgramID'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($section['SectionName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="addFacultyID" class="block text-gray-700 text-sm font-bold mb-2">
                            Faculty:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="addFacultyID" id="addFacultyID" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a Faculty</option>
                            <?php foreach ($faculties as $faculty): ?>
                                <option value="<?php echo htmlspecialchars($faculty['FacultyID']); ?>"
                                    data-programid="<?php echo htmlspecialchars($faculty['ProgramID'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($faculty['FirstName'] . ' ' . $faculty['LastName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="addCurriculumID" class="block text-gray-700 text-sm font-bold mb-2">
                            Subject:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="addCurriculumID" id="addCurriculumID" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a Subject</option>
                            <!-- Options to be populated dynamically based on selected faculty -->
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="addRoomID" class="block text-gray-700 text-sm font-bold mb-2">
                            Room:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="addRoomID" id="addRoomID" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a Room</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?php echo htmlspecialchars($room['RoomID']); ?>">
                                    <?php echo htmlspecialchars($room['RoomName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Days:
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach ($days as $day):
                                ?>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="addDays[]" value="<?php echo $day; ?>"
                                        class="form-checkbox">
                                    <span class="ml-2"><?php echo $day; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mb-6 flex gap-4">
                        <div class="flex-1">
                            <label for="addStartTime" class="block text-gray-700 text-sm font-bold mb-2">
                                Start Time:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="addStartTime" id="addStartTime" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                        </div>
                        <div class="flex-1">
                            <label for="addEndTime" class="block text-gray-700 text-sm font-bold mb-2">
                                End Time:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="addEndTime" id="addEndTime" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="addNewScheduleModal">
                            Cancel
                        </button>
                        <button type="submit" name="btnAdd"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Add Schedule
                        </button>
                    </div>
                </form>

                <script>
                    // Fetch and populate preferred subjects based on selected faculty and active semester
                    const activeSemesterID = <?php echo json_encode(getActiveSchoolYearSemesterID()); ?>;
                    document.getElementById('addFacultyID').addEventListener('change', function () {
                        const facultyId = this.value;
                        const curriculumSelect = document.getElementById('addCurriculumID');
                        curriculumSelect.innerHTML = '<option value="" disabled selected>Loading subjects...</option>';
                        if (!facultyId) {
                            curriculumSelect.innerHTML = '<option value="" disabled selected>Select a Subject</option>';
                            return;
                        }

                        fetch('/src/partials/modals/admin/add_new_schedule_modal.php?action=getPreferredSubjects&facultyId=' + facultyId + '&semester=' + activeSemesterID)
                            .then(response => response.json())
                            .then(data => {
                                curriculumSelect.innerHTML = '';
                                if (data.length === 0) {
                                    curriculumSelect.innerHTML = '<option value="" disabled selected>No preferred subjects found</option>';
                                    return;
                                }
                                data.forEach(subject => {
                                    const option = document.createElement('option');
                                    option.value = subject.CurriculumID;
                                    option.textContent = subject.SubjectName;
                                    curriculumSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error fetching preferred subjects:', error);
                                curriculumSelect.innerHTML = '<option value="" disabled selected>Error loading subjects</option>';
                            });
                    });

                    // Conflict check before form submission
                    document.getElementById('addNewScheduleForm').addEventListener('submit', function (event) {
                        event.preventDefault();

                        const conflictErrorDiv = document.getElementById('conflictError');
                        conflictErrorDiv.classList.add('hidden');
                        conflictErrorDiv.textContent = '';

                        const formData = new FormData(this);
                        const days = formData.getAll('addDays[]');
                        if (days.length === 0) {
                            conflictErrorDiv.textContent = 'Please select at least one day.';
                            conflictErrorDiv.classList.remove('hidden');
                            return;
                        }

                        const roomID = formData.get('addRoomID');
                        const startTime = formData.get('addStartTime');
                        const endTime = formData.get('addEndTime');

                        if (!roomID || !startTime || !endTime) {
                            conflictErrorDiv.textContent = 'Please fill in all required fields.';
                            conflictErrorDiv.classList.remove('hidden');
                            return;
                        }

                        fetch('src/partials/dashboard_pages/admin/functions/func_schedules.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                context: 'isScheduleConflicting',
                                roomID: roomID,
                                startTime: startTime,
                                endTime: endTime,
                                // Append days as multiple parameters
                                ...days.reduce((acc, day) => {
                                    acc.append('days[]', day);
                                    return acc;
                                }, new URLSearchParams())
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.conflict) {
                                    conflictErrorDiv.textContent = 'Schedule conflict detected. Please choose a different time or room.';
                                    conflictErrorDiv.classList.remove('hidden');
                                } else {
                                    // No conflict, submit the form
                                    this.submit();
                                }
                            })
                            .catch(error => {
                                conflictErrorDiv.textContent = 'Error checking schedule conflict. Please try again.';
                                conflictErrorDiv.classList.remove('hidden');
                                console.error('Error:', error);
                            });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<script>
    // Fetch and populate preferred subjects based on selected faculty
    document.getElementById('addFacultyID').addEventListener('change', function () {
        const facultyId = this.value;
        const curriculumSelect = document.getElementById('addCurriculumID');
        curriculumSelect.innerHTML = '<option value="" disabled selected>Loading subjects...</option>';
        if (!facultyId) {
            curriculumSelect.innerHTML = '<option value="" disabled selected>Select a Subject</option>';
            return;
        }

        fetch('/src/partials/modals/admin/add_new_schedule_modal.php?action=getPreferredSubjects&facultyId=' + facultyId)
            .then(response => response.json())
            .then(data => {
                curriculumSelect.innerHTML = '';
                if (data.length === 0) {
                    curriculumSelect.innerHTML = '<option value="" disabled selected>No preferred subjects found</option>';
                    return;
                }
                data.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.CurriculumID;
                    option.textContent = subject.SubjectName;
                    curriculumSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching preferred subjects:', error);
                curriculumSelect.innerHTML = '<option value="" disabled selected>Error loading subjects</option>';
            });
    });

    // Filter faculty options based on selected section's ProgramID
    document.getElementById('addSectionID').addEventListener('change', function () {
        const selectedSection = this.options[this.selectedIndex];
        const programId = selectedSection.getAttribute('data-programid');
        const facultySelect = document.getElementById('addFacultyID');

        for (let i = 0; i < facultySelect.options.length; i++) {
            const option = facultySelect.options[i];
            const optionProgramId = option.getAttribute('data-programid');
            if (!programId || option.value === "" || optionProgramId === programId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }

        // Reset faculty selection if current selection is not valid
        if (facultySelect.selectedIndex !== -1) {
            const selectedFacultyOption = facultySelect.options[facultySelect.selectedIndex];
            if (selectedFacultyOption.style.display === 'none') {
                facultySelect.selectedIndex = 0; // Select the placeholder option
                // Trigger change event to clear subjects
                facultySelect.dispatchEvent(new Event('change'));
            }
        }
    });

    // Trigger change event on page load to apply initial filtering if needed
    document.addEventListener('DOMContentLoaded', function () {
        const sectionSelect = document.getElementById('addSectionID');
        if (sectionSelect.value) {
            sectionSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.5s ease-in-out;
    }
</style>
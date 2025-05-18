<?php
require_once(__DIR__ . "../../../../../config/dbConnection.php");
$db = new Database();
$conn = $db->getConnection();

$programs = [];
try {
    $stmt = $conn->query("SELECT ProgramID, ProgramName FROM programs ORDER BY ProgramName ASC");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $programs = [];
}
?>

<div id="addNewSectionModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-md max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Add New Section
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="addNewSectionModal">
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
                <form id="addSectionForm" method="POST" action="src/partials/dashboard_pages/admin/functions/func_sections.php" class="space-y-4">
                    <div>
                        <label for="addSectionName" class="block text-gray-700 text-sm font-bold mb-2">
                            Section Name: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="addSectionName" name="addSectionName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="e.g., BSIT 1A" pattern="^[a-zA-Z0-9\s\-]{2,}$"
                            title="Section name must be at least 2 characters and can contain letters, numbers, spaces, and hyphens.">
                        <p class="text-gray-500 text-xs italic">Enter the section name (letters, numbers, spaces, and hyphens allowed).</p>
                    </div>
                    <div>
                        <label for="addProgramID" class="block text-gray-700 text-sm font-bold mb-2">
                            Program: <span class="text-red-500">*</span>
                        </label>
                        <select id="addProgramID" name="addProgramID" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= htmlspecialchars($program['ProgramID']) ?>">
                                    <?= htmlspecialchars($program['ProgramName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-gray-500 text-xs italic">Select the program for this section.</p>
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="addNewSectionModal">
                            Cancel
                        </button>
                        <button type="submit" name="btnAdd"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Add Section
                        </button>
                    </div>
                </form>
                <script>
                    document.getElementById('addSectionForm').addEventListener('submit', function (event) {
                        const sectionNameInput = document.getElementById('addSectionName');
                        const sectionNameValue = sectionNameInput.value.trim();
                        const sectionNameRegex = /^[a-zA-Z0-9\s\-]{2,}$/;
                        const programSelect = document.getElementById('addProgramID');
                        if (!programSelect.value) {
                            event.preventDefault();
                            alert('Please select a program.');
                            programSelect.focus();
                            return;
                        }
                        if (sectionNameValue.length === 0) {
                            event.preventDefault();
                            alert('Section name cannot be empty.');
                            sectionNameInput.focus();
                            return;
                        }
                        if (!sectionNameRegex.test(sectionNameValue)) {
                            event.preventDefault();
                            alert('Section name must be at least 2 characters and can contain letters, numbers, spaces, and hyphens.');
                            sectionNameInput.focus();
                            return;
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>
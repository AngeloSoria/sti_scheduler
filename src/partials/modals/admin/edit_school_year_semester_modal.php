<?php
require_once(__DIR__ . "../../../../../config/dbConnection.php");
$db = new Database();
$conn = $db->getConnection();
?>
<div id="editSchoolYearSemesterModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-md max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Edit School Year Semester
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="editSchoolYearSemesterModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-6 pt-0 space-y-6">
                <form id="editSchoolYearSemesterForm" method="POST"
                    action="src/partials/dashboard_pages/admin/functions/func_school_year_semester.php"
                    class="space-y-4">
                    <input type="hidden" id="editID" name="editID">
                    <div class="flex space-x-2">
                        <div class="flex-1">
                            <label for="editStartYear" class="block text-gray-700 text-sm font-bold mb-2">
                                Start Year: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editStartYear" name="editStartYear" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="e.g., 2023" pattern="^\d{4}$" title="Start year must be a 4-digit number.">
                        </div>
                        <div class="flex-1">
                            <label for="editEndYear" class="block text-gray-700 text-sm font-bold mb-2">
                                End Year: <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editEndYear" name="editEndYear" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="e.g., 2024" pattern="^\d{4}$" title="End year must be a 4-digit number.">
                        </div>
                    </div>
                    <div>
                        <label for="editSemester" class="block text-gray-700 text-sm font-bold mb-2">
                            Semester: <span class="text-red-500">*</span>
                        </label>
                        <select id="editSemester" name="editSemester" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>Select a semester</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="editIsActive" name="editIsActive"
                            class="form-checkbox h-4 w-4 text-indigo-600">
                        <label for="editIsActive" class="text-gray-700 text-sm font-bold">Set as Active</label>
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="editSchoolYearSemesterModal">
                            Cancel
                        </button>
                        <button type="submit" name="btnEdit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Save
                        </button>
                    </div>
                </form>
                <script>
                    document.getElementById('editSchoolYearSemesterForm').addEventListener('submit', function (event) {
                        const startYearInput = document.getElementById('editStartYear');
                        const endYearInput = document.getElementById('editEndYear');
                        const semesterSelect = document.getElementById('editSemester');
                        const yearPattern = /^\d{4}$/;

                        const startYear = startYearInput.value.trim();
                        const endYear = endYearInput.value.trim();
                        const semester = semesterSelect.value;

                        if (startYear === '' || !yearPattern.test(startYear)) {
                            event.preventDefault();
                            alert('Start year must be a 4-digit number.');
                            startYearInput.focus();
                            return;
                        }
                        if (endYear === '' || !yearPattern.test(endYear)) {
                            event.preventDefault();
                            alert('End year must be a 4-digit number.');
                            endYearInput.focus();
                            return;
                        }
                        if (parseInt(startYear) >= parseInt(endYear)) {
                            event.preventDefault();
                            alert('Start year must be less than End year.');
                            startYearInput.focus();
                            return;
                        }
                        if (semester === '') {
                            event.preventDefault();
                            alert('Please select a semester.');
                            semesterSelect.focus();
                            return;
                        }
                    });

                    function setEditSchoolYearSemesterModal(data) {
                        document.getElementById('editID').value = data.ID;
                        const [startYear, endYear] = data.SchoolYear.split('-');
                        document.getElementById('editStartYear').value = startYear;
                        document.getElementById('editEndYear').value = endYear;
                        document.getElementById('editSemester').value = data.Semester;
                        document.getElementById('editIsActive').checked = data.IsActive == 1;
                    }
                </script>
            </div>
        </div>
    </div>
</div>
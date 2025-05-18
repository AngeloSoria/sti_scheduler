<div id="addNewCurriculumModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-4xl max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Add New Curriculum
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="addNewCurriculumModal">
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
                <form method="POST" action="" id="addNewCurriculumForm" class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="addCourseID" class="block text-gray-700 text-sm font-bold mb-2">
                            Course ID: <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="addCourseID" id="addCourseID" required min="1" step="1" pattern="\d*"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline no-spinner"
                            placeholder="e.g., 101" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <p class="text-gray-500 text-xs italic">Enter the course ID (integer only).</p>
                    </div>
                    <div>
                        <label for="addSubjectArea" class="block text-gray-700 text-sm font-bold mb-2">
                            Subject Area: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="addSubjectArea" id="addSubjectArea" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="GEDC" pattern="^[a-zA-Z]{2,}$"
                            title="Subject area must be at least 2 letters and contain only letters.">
                        <p class="text-gray-500 text-xs italic">Enter the subject area (letters only, no spaces or
                            numbers).</p>
                    </div>
                    <div>
                        <label for="addCatalogNo" class="block text-gray-700 text-sm font-bold mb-2">
                            Catalog Number: <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="addCatalogNo" id="addCatalogNo" required min="1" step="1"
                            pattern="\d*"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline no-spinner"
                            placeholder="e.g., 201" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <p class="text-gray-500 text-xs italic">Enter the catalog number (integer only).</p>
                    </div>
                    <div class="col-span-2">
                        <label for="addSubjectName" class="block text-gray-700 text-sm font-bold mb-2">
                            Subject Name: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="addSubjectName" id="addSubjectName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="e.g., Mathematics in the Modern World" pattern="^(?=.*[a-zA-Z])[a-zA-Z\s]{2,}$"
                            title="Subject name must be at least 2 letters and contain only letters and spaces.">
                        <p class="text-gray-500 text-xs italic">Enter the full name of the subject.</p>
                    </div>
                    <div>
                        <label for="addUnits" class="block text-gray-700 text-sm font-bold mb-2">
                            Units: <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="addUnits" id="addUnits" required min="1" step="1" pattern="\d*"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline no-spinner"
                            placeholder="e.g., 3" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <p class="text-gray-500 text-xs italic">Enter the number of units (integer only).</p>
                    </div>
                    <div>
                        <label for="addProgramName" class="block text-gray-700 text-sm font-bold mb-2">
                            Program: <span class="text-red-500">*</span>
                        </label>
                        <select name="addProgramName" id="addProgramName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a Program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?php echo htmlspecialchars($program); ?>">
                                    <?php echo htmlspecialchars($program); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-gray-500 text-xs italic">Choose the program this subject belongs to.</p>
                    </div>
                    <div>
                        <label for="addYearLevel" class="block text-gray-700 text-sm font-bold mb-2">
                            Year Level: <span class="text-red-500">*</span>
                        </label>
                        <select name="addYearLevel" id="addYearLevel" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select Year Level</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                        <p class="text-gray-500 text-xs italic">Indicate the year level when this subject is offered.
                        </p>
                    </div>
                    <div>
                        <label for="addSemester" class="block text-gray-700 text-sm font-bold mb-2">
                            Semester: <span class="text-red-500">*</span>
                        </label>
                        <select name="addSemester" id="addSemester" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select Semester</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                        <p class="text-gray-500 text-xs italic">Select the semester when this subject is offered.</p>
                    </div>
                    <div class="col-span-3 flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="addNewCurriculumModal">
                            Cancel
                        </button>
                        <button type="submit" name="btnAdd"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Add Curriculum
                        </button>
                    </div>
                </form>

                <style>
                    /* Remove number input spinners for Chrome, Safari, Edge, Opera */
                    input.no-spinner::-webkit-outer-spin-button,
                    input.no-spinner::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }

                    /* Remove number input spinners for Firefox */
                    input.no-spinner[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
                <script>
                    document.getElementById('addNewCurriculumForm').addEventListener('submit', function (event) {
                        const subjectNameInput = document.getElementById('addSubjectName');
                        const subjectAreaInput = document.getElementById('addSubjectArea');
                        const subjectNameValue = subjectNameInput.value.trim();
                        const subjectAreaValue = subjectAreaInput.value.trim();
                        const subjectNameRegex = /^[a-zA-Z\s]{2,}$/;
                        const subjectAreaRegex = /^[a-zA-Z]{2,}$/;
                        if (!subjectNameRegex.test(subjectNameValue)) {
                            event.preventDefault();
                            alert('Subject name must be at least 2 letters and contain only letters and spaces.');
                            subjectNameInput.focus();
                            return;
                        }
                        if (!subjectAreaRegex.test(subjectAreaValue)) {
                            event.preventDefault();
                            alert('Subject area must be at least 2 letters and contain only letters with no spaces or numbers.');
                            subjectAreaInput.focus();
                            return;
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>
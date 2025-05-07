<?php
$departments = getDepartments();
$programs = getPrograms();
?>
<div id="editUserModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-md max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Edit User
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="editUserModal">
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
<form method="POST" action="src/partials/dashboard_pages/admin/functions/func_users.php"
    id="editUserForm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="editUser" />
    <input type="hidden" name="editUserID" id="editUserID" />
                    <div class="mb-4">
                        <label for="editFirstName" class="block text-gray-700 text-sm font-bold mb-2">
                            First Name:
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="editFirstName" id="editFirstName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="First Name">
                    </div>
                    <div class="mb-4">
                        <?php if ($type === 'admin' || $type === 'faculty'): ?>
                            <label for="editMiddleName" class="block text-gray-700 text-sm font-bold mb-2">
                                Middle Name:
                            </label>
                            <input type="text" name="editMiddleName" id="editMiddleName"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Middle Name">
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="editLastName" class="block text-gray-700 text-sm font-bold mb-2">
                            Last Name:
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="editLastName" id="editLastName" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Last Name">
                    </div>
                    <?php if ($type === 'admin' || $type === 'faculty'): ?>
                        <div class="mb-4">
                            <label for="editUsernameDisplay" class="block text-gray-700 text-sm font-bold mb-2">
                                Username:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editUsernameDisplay" readonly
                                class="bg-gray-100 cursor-not-allowed shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Username">
                            <input type="hidden" name="editUsername" id="editUsername" />
                        </div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <label for="editPassword" class="block text-gray-700 text-sm font-bold mb-2">
                            Password:
                        </label>
                        <input type="password" name="editPassword" id="editPassword"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="New Password (leave blank to keep current)">
                    </div>
                    <div id="editDepartmentDiv" class="mb-4 hidden">
                        <label for="editDepartment" class="block text-gray-700 text-sm font-bold mb-2">Department</label>
                        <select name="editDepartment" id="editDepartment"
                            class="w-full border border-gray-300 p-3 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-300">
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= htmlspecialchars($department['DepartmentID']) ?>">
                                    <?= htmlspecialchars($department['DepartmentName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="editProgramDiv" class="mb-4 hidden">
                        <label for="editProgram" class="block text-gray-700 text-sm font-bold mb-2">Program</label>
                        <select name="editProgram" id="editProgram"
                            class="w-full border border-gray-300 p-3 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-300">
                            <option value="" disabled selected>Select Program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= htmlspecialchars($program['ProgramID']) ?>"
                                    data-department="<?= htmlspecialchars($program['DepartmentID'] ?? '') ?>">
                                    <?= htmlspecialchars($program['ProgramName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="editPreferredSubjectsDiv" class="mb-4 hidden">
                        <label for="editPreferredSubjects" class="block text-gray-700 text-sm font-bold mb-2">Preferred Subjects</label>
                        <select name="editPreferredSubjects[]" id="editPreferredSubjects" multiple size="6"
                            class="w-full border border-gray-300 p-3 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-300">
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="editProfilePic" class="block text-gray-700 text-sm font-bold mb-2">
                            Profile Picture:
                        </label>
                        <input type="file" name="editProfilePic" id="editProfilePic" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                            file:rounded file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100">
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="editUserModal">
                            Cancel
                        </button>
                        <button type="submit" name="btnEdit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

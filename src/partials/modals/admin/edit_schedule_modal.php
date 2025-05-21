<div id="editScheduleModal" tabindex="-1" aria-hidden="true"
    class="opacity-0 pointer-events-none overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full transition-opacity duration-300 ease-in-out">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out"></div>
    <div class="relative p-4 w-full max-w-md max-h-full z-10">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b rounded-t">
                <h3 class="text-xl font-semibold text-gray-900">
                    Edit Schedule
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="editScheduleModal">
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
                <form method="POST" id="editScheduleForm">
                    <input type="hidden" name="context" value="editSchedule" />
                    <input type="hidden" name="editScheduleID" id="editScheduleID" />
                    <div class="mb-4">
                        <label for="editFacultyName" class="block text-gray-700 text-sm font-bold mb-2">
                            Faculty:
                        </label>
                        <input type="text" id="editFacultyName" name="editFacultyName" readonly
                            class="bg-gray-100 cursor-not-allowed shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                    </div>
                    <div class="mb-4">
                        <label for="editSubjectName" class="block text-gray-700 text-sm font-bold mb-2">
                            Subject:
                        </label>
                        <input type="text" id="editSubjectName" name="editSubjectName" readonly
                            class="bg-gray-100 cursor-not-allowed shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                    </div>
                    <div class="mb-4">
                        <label for="editDay" class="block text-gray-700 text-sm font-bold mb-2">
                            Day:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="editDay" id="editDay" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>Select a Day</option>
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach ($days as $day):
                            ?>
                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="editRoomID" class="block text-gray-700 text-sm font-bold mb-2">
                            Room:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="editRoomID" id="editRoomID" required
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
                        <label for="editSectionID" class="block text-gray-700 text-sm font-bold mb-2">
                            Section:
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="editSectionID" id="editSectionID" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled selected>Select a Section</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?php echo htmlspecialchars($section['SectionID']); ?>">
                                    <?php echo htmlspecialchars($section['SectionName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4 flex flex-col md:flex-row md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="editStartTime" class="block text-gray-700 text-sm font-bold mb-2">
                                Start Time:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="editStartTime" id="editStartTime" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                        </div>
                        <div class="w-full md:w-1/2 mt-4 md:mt-0">
                            <label for="editEndTime" class="block text-gray-700 text-sm font-bold mb-2">
                                End Time:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="editEndTime" id="editEndTime" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                            data-modal-hide="editScheduleModal">
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
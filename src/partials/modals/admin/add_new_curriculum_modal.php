<div id="addNewModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-semibold mb-4">Add New Curriculum</h2>
        <form id="addNewForm" action="#" method="POST" class="space-y-4">
            <div class="flex space-x-4">
                <div class="flex-1">
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" name="subject" id="subject" required
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div class="w-24">
                    <label for="units" class="block text-sm font-medium text-gray-700">Units</label>
                    <input type="number" name="units" id="units" required min="0" step="0.5"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
                <select name="program" id="program" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Program</option>
                    <!-- Add program options here -->
                </select>
            </div>
            <div>
                <label for="year_level" class="block text-sm font-medium text-gray-700">Year Level</label>
                <select name="year_level" id="year_level" required
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Year Level</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelBtn"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 focus:outline-none">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none">Add</button>
            </div>
        </form>
    </div>
</div>

<script>
const addNewBtn = document.getElementById('addNewBtn');
const addNewModal = document.getElementById('addNewModal');
const cancelBtn = document.getElementById('cancelBtn');

function fadeIn(element) {
    element.classList.remove('opacity-0', 'pointer-events-none');
    element.classList.add('animate-fadeIn');
}

function fadeOut(element) {
    element.classList.remove('animate-fadeIn');
    element.classList.add('opacity-0', 'pointer-events-none');
}

addNewBtn.addEventListener('click', () => {
    fadeIn(addNewModal);
});

cancelBtn.addEventListener('click', () => {
    fadeOut(addNewModal);
});

let isMouseDownOnModal = false;

addNewModal.addEventListener('mousedown', (e) => {
    if (e.target === addNewModal) {
        isMouseDownOnModal = true;
    } else {
        isMouseDownOnModal = false;
    }
});

addNewModal.addEventListener('mouseup', (e) => {
    if (e.target === addNewModal && isMouseDownOnModal) {
        fadeOut(addNewModal);
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
    animation: fadeIn 0.05s ease forwards;
}
</style>
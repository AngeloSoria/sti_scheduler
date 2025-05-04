<?php
// Placeholder for Programs page content
?>

<section class="p-4 sm:p-6 bg-white rounded shadow-md overflow-x-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <h1 class="text-lg sm:text-xl font-semibold mb-4 md:mb-0">Programs View</h1>
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
            <button id="addNewProgramBtn"
                class="flex items-center justify-center px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M19 12H5"></path>
                </svg>
                Add New Program
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program
                        Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                // Example static rows - replace with dynamic data rendering
                $programs = [
                    ['name' => 'Program A', 'description' => 'Description for Program A'],
                    ['name' => 'Program B', 'description' => 'Description for Program B'],
                ];
                foreach ($programs as $program) {
                    echo '<tr>';
                    echo '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($program['name']) . '</td>';
                    echo '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($program['description']) . '</td>';
                    echo '<td class="px-4 py-2 whitespace-nowrap text-center space-x-2">';
                    echo '<button class="text-blue-600 hover:text-blue-900">Edit</button>';
                    echo '<button class="text-red-600 hover:text-red-900">Delete</button>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>
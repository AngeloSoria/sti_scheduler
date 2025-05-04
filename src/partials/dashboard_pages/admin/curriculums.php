<?php
// Placeholder for PHP logic to fetch, filter, paginate, and handle CSV import/export
?>

<section class="p-4 sm:p-6 bg-white rounded shadow-md overflow-x-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
        <h1 class="text-lg sm:text-xl font-semibold mb-4 md:mb-0">Curriculum View</h1>
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
            <button
                class="flex items-center justify-center px-3 sm:px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M19 12H5"></path>
                </svg>
                Import CSV
            </button>
            <button
                class="flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 w-full sm:w-auto">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export CSV
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 items-center">
            <label for="filter-subject" class="text-sm font-medium">Filter Subject:</label>
            <input type="text" id="filter-subject" name="filter-subject" placeholder="Subject"
                class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto" />
            <label for="filter-year" class="text-sm font-medium">Year Level:</label>
            <select id="filter-year" name="filter-year"
                class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                <option value="">All</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 items-center">
            <label for="rows-per-page" class="text-sm font-medium">Rows per page:</label>
            <select id="rows-per-page" name="rows-per-page"
                class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                <option>10</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
            <input type="text" id="search" name="search" placeholder="Search..."
                class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto" />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year
                        Level</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                // Example static rows - replace with dynamic data rendering
                $curriculums = [
                    ['subject' => 'Mathematics', 'unit' => 'Algebra', 'year_level' => '1'],
                    ['subject' => 'Science', 'unit' => 'Biology', 'year_level' => '2'],
                    ['subject' => 'English', 'unit' => 'Grammar', 'year_level' => '3'],
                ];
                foreach ($curriculums as $curriculum) {
                    echo '<tr>';
                    echo '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['subject']) . '</td>';
                    echo '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['unit']) . '</td>';
                    echo '<td class="px-4 py-2 whitespace-nowrap">' . htmlspecialchars($curriculum['year_level']) . '</td>';
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

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-4 space-y-2 md:space-y-0">
        <div class="text-sm text-gray-700">
            Showing 1 to 10 of 100 results
        </div>
        <div class="inline-flex rounded-md shadow-sm" role="group" aria-label="Pagination">
            <button
                class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100 rounded-l-md w-full md:w-auto mb-2 md:mb-0">Previous</button>
            <button
                class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-100 w-full md:w-auto mb-2 md:mb-0">1</button>
            <button
                class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-100 w-full md:w-auto mb-2 md:mb-0">2</button>
            <button
                class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 hover:bg-gray-100 w-full md:w-auto mb-2 md:mb-0">3</button>
            <button
                class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 rounded-r-md w-full md:w-auto mb-2 md:mb-0">Next</button>
        </div>
    </div>
</section>
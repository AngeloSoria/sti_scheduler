<?php if (!empty($data)): ?>
    <div class="overflow-x-auto">
        <h3 class="text-lg font-semibold mb-2 text-gray-800 text-center">
            <?php
            $filterText = "Curriculum";
            if (!empty($programFilter))
                $filterText .= " for " . htmlspecialchars($programFilter);
            if (!empty($yearFilter))
                $filterText .= " - Year " . htmlspecialchars($yearFilter);
            if (!empty($search))
                $filterText .= " (Search: '" . htmlspecialchars($search) . "')";
            echo $filterText;
            ?>
        </h3>
        <table class="min-w-full leading-normal shadow-md rounded-lg overflow-hidden">
            <thead>
                <tr>
                    <th
                        class="px-5 py-3 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Program</th>
                    <th
                        class="px-5 py-3 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Subject</th>
                    <th
                        class="px-5 py-3 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Unit</th>
                    <th
                        class="px-5 py-3 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Year Level</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($row['ProgramName']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($row['SubjectName']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($row['CreditUnit']); ?>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <?php echo htmlspecialchars($row['Year']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalRows > 0): ?>
        <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                <?php
                $start = ($currentPage - 1) * $rowsPerPage + 1;
                $end = min($currentPage * $rowsPerPage, $totalRows);
                echo "Showing $start to $end of $totalRows entries";
                ?>
            </div>
            <div class="space-x-2">
                <?php
                $filterParams = http_build_query(array_filter([
                    'page' => $_GET['page'] ?? null,
                    'program' => $_GET['program'] ?? null,
                    'year' => $_GET['year'] ?? null,
                    'search' => $_GET['search'] ?? null,
                    'rowsPerPage' => $_GET['rowsPerPage'] ?? null,
                ]));
                $paginationBaseUrl = "dashboard?page=curriculums&fetch_data=1&" . preg_replace('/&page=\d+/', '', $filterParams);
                ?>
                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo $paginationBaseUrl; ?>&page=<?php echo $currentPage - 1; ?>"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">&laquo;
                        Previous</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?php echo $paginationBaseUrl; ?>&page=<?php echo $p; ?>"
                        class="<?php echo $p == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-800'; ?> font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <?php echo $p; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo $paginationBaseUrl; ?>&page=<?php echo $currentPage + 1; ?>"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Next
                        &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <p class="text-gray-700">No curriculum data found based on the selected filters.</p>
<?php endif; ?>
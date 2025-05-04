<?php
$db = new Database();
$conn = $db->getConnection();

$importSuccess = null;
$importErrors = [];
$addSuccess = null;
$addErrors = [];

// Handle CSV Import
if (isset($_POST['btnImport']) && isset($_FILES['csvFile'])) {
    $file = fopen($_FILES['csvFile']['tmp_name'], 'r');
    $isHeader = true;
    $importSuccess = false;
    $importErrors = [];

    while (($row = fgetcsv($file, 1000, ',')) !== false) {
        if ($isHeader) {
            $isHeader = false;
            continue;
        }

        $subjectName = trim($row[0]);
        $creditUnit = trim($row[1]);
        $programName = trim($row[2]);
        $yearLevel = trim($row[3]);

        // Lookup ProgramID using PDO
        $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
        $stmt->execute([$programName]);
        $program = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($program) {
            $programID = $program['ProgramID'];

            // Insert into curriculum
            $stmtInsert = $conn->prepare("INSERT INTO curriculums (SubjectName, CreditUnit, ProgramID, Year) VALUES (?, ?, ?, ?)");
            if (!$stmtInsert->execute([$subjectName, $creditUnit, $programID, $yearLevel])) {
                $importSuccess = false;
                $importErrors[] = "Error inserting subject '$subjectName' for program '$programName'.";
                break;
            } else {
                $importSuccess = true;
            }
        } else {
            $importSuccess = false;
            $importErrors[] = "Program '$programName' not found in the database.";
            break;
        }
    }

    fclose($file);

    if ($importSuccess && empty($importErrors)) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">CSV data imported successfully!</span>
                </div>';
    } elseif ($importSuccess === false) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Import failed. ' . (!empty($importErrors) ? implode('<br>', $importErrors) : 'Invalid data.') . '</span>
                </div>';
    }
}

// Handle Manual Add
if (isset($_POST['btnAdd'])) {
    $addSubjectName = trim($_POST['addSubjectName']);
    $addCreditUnit = trim($_POST['addCreditUnit']);
    $addProgramName = trim($_POST['addProgramName']);
    $addYearLevel = trim($_POST['addYearLevel']);

    // Lookup ProgramID
    $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmt->execute([$addProgramName]);
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        $programID = $program['ProgramID'];
        $stmtInsert = $conn->prepare("INSERT INTO curriculums (SubjectName, CreditUnit, ProgramID, Year) VALUES (?, ?, ?, ?)");
        if ($stmtInsert->execute([$addSubjectName, $addCreditUnit, $programID, $addYearLevel])) {
            $addSuccess = true;
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">Subject "' . htmlspecialchars($addSubjectName) . '" added successfully!</span>
                    </div>';
        } else {
            $addSuccess = false;
            $addErrors[] = "Error adding subject '" . htmlspecialchars($addSubjectName) . "'.";
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">Failed to add subject. ' . (!empty($addErrors) ? implode('<br>', $addErrors) : 'Please try again.') . '</span>
                    </div>';
        }
    } else {
        $addSuccess = false;
        $addErrors[] = "Program '" . htmlspecialchars($addProgramName) . "' not found.";
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">Failed to add subject. Program "' . htmlspecialchars($addProgramName) . '" not found.</span>
                </div>';
    }
}

// Load Data for Selected Program with Pagination and Search
$data = [];
$totalRows = 0;
$rowsPerPageOptions = [5, 10, 20, 50, 100];
$rowsPerPage = isset($_GET['rowsPerPage']) && in_array($_GET['rowsPerPage'], $rowsPerPageOptions) ? $_GET['rowsPerPage'] : 10;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = max(0, ($currentPage - 1) * $rowsPerPage);
$programFilter = isset($_GET['program']) ? $_GET['program'] : '';
$yearFilter = isset($_GET['year']) ? $_GET['year'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$whereClauses = [];
$queryParams = [];
$programIDFilter = null;

if (!empty($programFilter)) {
    $stmt = $conn->prepare("SELECT ProgramID FROM programs WHERE ProgramName = ?");
    $stmt->execute([$programFilter]);
    $programRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($programRow) {
        $programIDFilter = $programRow['ProgramID'];
        $whereClauses[] = "c.ProgramID = ?";
        $queryParams[] = $programIDFilter;
    } else {
        $totalRows = 0;
        $totalPages = 0;
        $data = [];
    }
}

if (!empty($yearFilter)) {
    $whereClauses[] = "c.Year = ?";
    $queryParams[] = $yearFilter;
}

if (!empty($search)) {
    $whereClauses[] = "c.SubjectName LIKE ?";
    $queryParams[] = '%' . $search . '%';
}

$whereString = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

if (empty($programFilter) || $programIDFilter !== null) {
    $sql = "SELECT c.SubjectName, c.CreditUnit, c.Year, p.ProgramName
            FROM curriculums c
            JOIN programs p ON c.ProgramID = p.ProgramID
            " . $whereString . "
            LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    try {
        $paramIndex = 1;
        if (!empty($queryParams)) {
            foreach ($queryParams as $param) {
                $stmt->bindValue($paramIndex++, $param);
            }
        }
        $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, $rowsPerPage, PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countQuery = "SELECT COUNT(*) AS total
                       FROM curriculums c
                       JOIN programs p ON c.ProgramID = p.ProgramID
                       " . $whereString;
        $stmtCount = $conn->prepare($countQuery);
        $stmtCount->execute($queryParams);
        $row = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $totalRows = $row['total'];
        $totalPages = ceil($totalRows / $rowsPerPage);

    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "<br>";
        echo "SQL: " . $sql . "<br>";
        $totalRows = 0;
        $totalPages = 0;
        $data = [];
    }

} else {
    $totalPages = 0;
}

// Fetch all unique year levels for the filter
$stmtYears = $conn->prepare("SELECT DISTINCT Year FROM curriculums ORDER BY Year");
$stmtYears->execute();
$yearLevels = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

// Fetch all programs for the program filter and add modal dropdown
$stmtPrograms = $conn->prepare("SELECT ProgramName FROM programs ORDER BY ProgramName");
$stmtPrograms->execute();
$programs = $stmtPrograms->fetchAll(PDO::FETCH_COLUMN);

?>
<section>
    <h2 class="text-2xl font-semibold text-gray-800 mb-3">Curriculum Management</h2>

    <div class="flex items-center mb-4 space-x-4">
        <button data-modal-target="addCurriculumModal" data-modal-toggle="addCurriculumModal"
            class="flex items-center bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            type="button">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd"></path>
            </svg>
            Add New
        </button>
        <form action="dashboard?page=curriculums" method="post" enctype="multipart/form-data"
            class="flex items-center space-x-2">
            <div class="relative">
                <label for="csvFile" class="sr-only">Import CSV</label>
                <input type="file" name="csvFile" id="csvFile" accept=".csv" required
                    class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" name="btnImport"
                class="flex items-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586l1.293-1.293a1 1 0 011.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414zM13 5a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Import CSV
            </button>
        </form>
        <form action="dashboard?page=curriculums" method="get" class="flex items-center space-x-2" id="filterForm">
            <input type="hidden" name="page" value="curriculums">

            <div>
                <label for="program" class="text-gray-700 text-sm font-bold">Program:</label>
                <select name="program" id="program"
                    class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-8">
                    <option value="">All Programs</option>
                    <?php foreach ($programs as $prog): ?>
                        <option value="<?php echo htmlspecialchars($prog); ?>" <?= isset($_GET['program']) && $_GET['program'] == $prog ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($prog); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="year" class="text-gray-700 text-sm font-bold">Year:</label>
                <select name="year" id="year"
                    class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-8">
                    <option value="">All Years</option>
                    <?php foreach (array_unique($yearLevels) as $year): ?>
                        <option value="<?php echo htmlspecialchars($year); ?>" <?= isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($year); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="search" class="text-gray-700 text-sm font-bold">Search:</label>
                <input type="text" name="search" id="search" placeholder="Subject"
                    value="<?= htmlspecialchars($search ?? '') ?>"
                    class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <button type="submit"
                class="flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd"></path>
                </svg>
                Filter
            </button>
        </form>
    </div>

    <div id="curriculum-data">
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
                        $paginationBaseUrl = "dashboard?page=curriculums&" . preg_replace('/&page=\d+/', '', $filterParams);
                        ?>
                        <?php if ($currentPage > 1): ?>
                            <a href="<?php echo $paginationBaseUrl; ?>&page=<?php echo $currentPage - 1; ?>"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">&laquo;
                                Previous</a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <a href="<?php echo $paginationBaseUrl; ?>&page=<?php echo $p; ?>"
                                class="<?php echo $p == $currentPage ? 'bg-indigo-500 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-800'; ?> font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
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
    </div>

    <div id="addCurriculumModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Add New Curriculum
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                        data-modal-hide="addCurriculumModal">
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
                    <form method="POST" action="dashboard?page=curriculums">
                        <div class="mb-4">
                            <label for="addSubjectName" class="block text-gray-700 text-sm font-bold mb-2">
                                Subject Name:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="addSubjectName" id="addSubjectName" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="e.g., Introduction to Programming">
                            <p class="text-gray-500 text-xs italic">Enter the full name of the subject.</p>
                        </div>
                        <div class="mb-4">
                            <label for="addCreditUnit" class="block text-gray-700 text-sm font-bold mb-2">
                                Credit Unit:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="addCreditUnit" id="addCreditUnit" required min="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="e.g., 3">
                            <p class="text-gray-500 text-xs italic">Specify the number of credit units for this subject.
                            </p>
                        </div>
                        <div class="mb-4">
                            <label for="addProgramName" class="block text-gray-700 text-sm font-bold mb-2">
                                Program:
                                <span class="text-red-500">*</span>
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
                        <div class="mb-6">
                            <label for="addYearLevel" class="block text-gray-700 text-sm font-bold mb-2">
                                Year Level:
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="addYearLevel" id="addYearLevel" required min="1"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="e.g., 1">
                            <p class="text-gray-500 text-xs italic">Indicate the year level when this subject is
                                offered.</p>
                        </div>
                        <div class="flex justify-end">
                            <button type="button"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                data-modal-hide="addCurriculumModal">
                                Cancel
                            </button>
                            <button type="submit" name="btnAdd"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-3">
                                Add Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filterForm');
        const curriculumDataContainer = document.getElementById('curriculum-data');

        function attachPaginationListeners() {
            const paginationLinks = curriculumDataContainer.querySelectorAll('.space-x-2 a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    fetch(url)
                        .then(response => response.text())
                        .then(data => {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data;
                            const newCurriculumData = tempDiv.querySelector('#curriculum-data');
                            if (newCurriculumData) {
                                curriculumDataContainer.innerHTML = newCurriculumData.innerHTML;
                                attachPaginationListeners(); // Re-attach after update
                            }
                        })
                        .catch(error => console.error('Error fetching page:', error));
                });
            });
        }

        if (filterForm && curriculumDataContainer) {
            filterForm.addEventListener('change', function () {
                this.submit(); // Standard form submission on filter change
            });

            filterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const params = new URLSearchParams(formData);
                const url = `dashboard?page=curriculums&${params.toString()}`;

                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data;
                        const newCurriculumData = tempDiv.querySelector('#curriculum-data');

                        if (newCurriculumData) {
                            curriculumDataContainer.innerHTML = newCurriculumData.innerHTML;
                            attachPaginationListeners(); // Re-attach after update
                        } else {
                            console.error('Curriculum data container not found in response.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching curriculum data:', error);
                    });
            });
        } else {
            console.error('Filter form or curriculum data container not found.');
        }

        attachPaginationListeners(); // Initial attachment
    });
</script>
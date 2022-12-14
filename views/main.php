<?php

use App\Service\Database;

$databaseService = new Database();

// Get all the enrolments.
$enrolments = $databaseService->getEnrolments();

// Get all the courses.
$courses = $databaseService->getCourses();

// Get all the users.
$users = $databaseService->getUsers();

// Set per page.
$perPageArray = [
    10, 20, 50, 100
];
$statusArray = Database::STATUS_ARRAY;

// Get the filters.
list(
    $page,
    $perPage,
    $search,
    $courseId,
    $userId,
    $status
) = $databaseService->getAllFilters();

$pageCount = $databaseService->getPageCount($perPage);
$totalRecords = $databaseService->getTotalRecords();

$queryString = '';
if (isset($courseId) && $courseId) {
    $queryString = $queryString . '&course=' . $courseId;
}

if (isset($userId) && $userId) {
    $queryString = $queryString . '&user=' . $userId;
}

if (isset($status) && $status) {
    $queryString = $queryString . '&status=' . $status;
}

if ($search !== '') {
    $queryString = $queryString . '&search=' . $search;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/styles.css">
    <title>MindAtlas Test</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="/">MindAtlas Test</a>
        </div>
        <div class="table-filter-container">
            <div class="input">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" placeholder="Search..." value="<?= $search ?>">
            </div>
            <div class="input">
                <label for="course">Course</label>
                <select name="course" id="course">
                    <option value="">Select Course</option>
                    <?php
                    foreach ($courses as $course) {
                        if ($courseId == $course['course_id']) {
                            echo '<option value="' . $course['course_id'] . '" selected>' . $course['description'] . '</option>';

                            continue;
                        }

                        echo '<option value="' . $course['course_id'] . '">' . $course['description'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="input">
                <label for="user">User</label>
                <select name="user" id="user">
                    <option value="">Select User</option>
                    <?php
                    foreach ($users as $user) {
                        if ($userId == $user['user_id']) {
                            echo '<option value="' . $user['user_id'] . '" selected>' . $user['fname'] . ' ' . $user['surname'] . '</option>';

                            continue;
                        }

                        echo '<option value="' . $user['user_id'] . '">' . $user['fname'] . ' ' . $user['surname'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="input">
                <label for="completionStatus">Completion Status</label>
                <select name="completionStatus" id="completionStatus">
                    <option value="">Select Completion Status</option>
                    <?php
                    foreach ($statusArray as $statusValue) {
                        if ($status == $statusValue) {
                            echo '<option value="' . $statusValue . '" selected>' . $statusValue . '</option>';

                            continue;
                        }

                        echo '<option value="' . $statusValue . '">' . $statusValue . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="input">
                <button class="primary-button" id="applyFilter" onclick="applyFilter(<?= $perPage ?>)">Apply</button>
            </div>
        </div>
        <div class="table-container">
            <div class="table-pagination">
                <div class="per-page">
                    <select name="perPage" id="perPage">
                        <?php
                        foreach ($perPageArray as $perPageValue) {
                            if ($perPageValue === $perPage) {
                                echo '<option value="' . $perPageValue . '" selected>Showing ' . ($perPageValue < $totalRecords ? $perPageValue : 'All') . ' out of ' . $totalRecords . '</option>';
                                continue;
                            }
                            echo '<option value="' . $perPageValue . '">' . $perPageValue . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="pagination">
                    <?php
                    if ($page > 1) {
                        echo '<a href="?per_page=' . $perPage . '&page=' . ($page - 1) . $queryString . '">&laquo;</a>';
                    }

                    // Chunk of 5 pages for each pagination display.
                    $from = $page % 5 === 0 ? ($page - 4) : ($page + 1 - $page % 5);
                    $to = $pageCount <= ($from + 4) ? $pageCount : ($from + 4);

                    for ($i = $from; $i <= $to; $i++) {
                        echo '<a href="?per_page=' . $perPage . '&page=' . $i . $queryString . '" ' . ($page === $i ? 'class="active">' : '>') . $i . '</a>';
                    }
                    if ($page <  $pageCount) {
                        echo '<a href="?per_page=' . $perPage . '&page=' . ($page + 1) . $queryString . '">&raquo;</a>';
                    }
                    ?>
                </div>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>Name</th>
                        <th>Enrolled Course</th>
                        <th>Completion Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($enrolments as $enrolment) {
                        echo "<tr>
                            <td>$enrolment[enrolment_id]</td>
                            <td>$enrolment[fname] $enrolment[surname]</td>
                            <td>$enrolment[description]</td>
                            <td>$enrolment[status]</td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript" src="../asset/script.js"></script>
</body>

</html>
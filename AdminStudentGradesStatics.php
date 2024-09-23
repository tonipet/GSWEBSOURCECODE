<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<style>
    /* Add any additional styling here */
    .chart-container {
        margin: 20px;
        width: calc(50% - 40px); /* 50% width minus margin for spacing */
        box-sizing: border-box;
    }
    .chart-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -20px; /* Negative margin to counteract the chart-container margins */
    }
    canvas {
        display: block;
        margin: 10px 0;
    }
</style>

<?php
// Include the database connection function and get the connection
$db = getDB();

// Fetch grades data for all students
$sql = "SELECT SS.sectionName, FG.FinalGrade 
        FROM tbl_finalgrades FG 
        INNER JOIN tbl_androidstudentProfile SF ON SF.UID = FG.UID
        INNER JOIN tbl_androidstudentsection SS ON SS.UID = SF.SectionID"; 
$stmt = $db->prepare($sql);
$stmt->execute();
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert grades to JSON for use in JavaScript
echo '<script>';
echo 'const gradesData = ' . json_encode($grades) . ';';
echo '</script>';
?>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h4 class="mb-0 fs-5"></h4>
        </div>
        <div class="card-body">
            <div class="input-box">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sectionSearch" class="form-label">Search Sections</label>
                        <input type="text" class="form-control" id="sectionSearch" placeholder="Search sections..." />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="GradeSelect" class="form-label">Grade</label>
                        <select id="GradeSelect" class="form-select">
                            <option value="">ALL</option>
                            <option value="Passed">Passed</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                </div>
                <button id="generateButton" class="btn btn-primary">Generate Graph</button>
            </div>

            <!-- Loading Message -->
            <div id="loading-message" class="alert alert-info" style="display: none;">
                <strong>Please wait, loading...</strong>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
   
</div>
<div class="chart-container">
        <canvas id="gradeChart"></canvas>
    </div>
<?php include 'layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    let gradeChart = null;

    function generateGraph() {
        document.getElementById('loading-message').style.display = 'block';

        const sectionSearch = document.getElementById('sectionSearch').value.toLowerCase();
        const selectedGrade = document.getElementById('GradeSelect').value;

        // Filter the grades data based on search and selected grade
        const filteredData = gradesData.filter(item => {
            const matchesSection = item.sectionName.toLowerCase().includes(sectionSearch);
            const matchesGrade = selectedGrade === '' || item.FinalGrade === selectedGrade;
            return matchesSection && matchesGrade;
        });

        // Prepare data for the chart
        const sectionCounts = {};
        filteredData.forEach(item => {
            if (!sectionCounts[item.sectionName]) {
                sectionCounts[item.sectionName] = { Passed: 0, Failed: 0, Total: 0 };
            }
            sectionCounts[item.sectionName][item.FinalGrade]++;
            sectionCounts[item.sectionName].Total++;
        });

        const labels = Object.keys(sectionCounts);
        const passedData = labels.map(section => sectionCounts[section].Passed || 0);
        const failedData = labels.map(section => sectionCounts[section].Failed || 0);
        const totalData = labels.map(section => sectionCounts[section].Total || 0);

        // Destroy previous chart instance if it exists
        if (gradeChart) {
            gradeChart.destroy();
        }

        // Create the chart
        const ctx = document.getElementById('gradeChart').getContext('2d');
        gradeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Passed',
                        data: passedData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Failed',
                        data: failedData,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Students',
                        data: totalData,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        type: 'line',
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Sections'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Students'
                        }
                    }
                }
            }
        });

        document.getElementById('loading-message').style.display = 'none';
    }

    document.getElementById('generateButton').addEventListener('click', generateGraph);
    document.getElementById('sectionSearch').addEventListener('input', generateGraph);
    document.getElementById('GradeSelect').addEventListener('change', generateGraph);

    // Generate the initial graph
    generateGraph();
});
</script>

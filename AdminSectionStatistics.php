<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>
<style>
    /* Add any additional styling here */
    .chart-container {
        margin: 20px;
        /* Adjust width to fit three items per row */
        width: calc(50% - 40px); /* 33.333% width minus margin for spacing */
        box-sizing: border-box;
        /* Ensure each chart container fits within the grid */
    }
    .chart-row {
        display: flex;
        flex-wrap: wrap;
        /* Allow charts to wrap to the next row */
        margin: 0 -20px; /* Negative margin to counteract the chart-container margins */
    }
    canvas {
        display: block;
        margin: 10px 0;
    }
</style>


<div class="container mt-4">
    <!-- Main Card for Form and Charts -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h4 class="mb-0 fs-5">View Gaming Consumption</h4>
        </div>
        <div class="card-body">
            <!-- Input Box -->
            <div class="input-box">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sectionSearch" class="form-label">Search Sections</label>
                        <input type="text" class="form-control" id="sectionSearch" placeholder="Search sections..." />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="genderSelect" class="form-label">Gender</label>
                        <select id="genderSelect" class="form-select">
                            <option value="">ALL</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
                <button id="generateButton" class="btn btn-primary">Generate Graph</button>
            </div>

            <!-- Loading Message -->
           

            <!-- Chart Container -->
          
        </div>
       
    </div>
    <div id="loading-message" class="alert alert-info" style="display: none;">
                <strong>Please wait, loading...</strong>
            </div>
</div>
  <div id="chartContainer" ></div>

<?php include 'layout/footer.php'; ?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('generateButton').addEventListener('click', function() {
        generateGraph(); // Regenerate graph based on selected date range and gender
    });
    document.getElementById('sectionSearch').addEventListener('input', handleSearch);
    document.getElementById('genderSelect').addEventListener('change', function() {
        generateGraph(); // Regenerate graph based on selected gender
    });
});

function generateGraph() {
    document.getElementById("loading-message").style.display = "block";
    var database = firebase.database();

    // References to your data
    var userProfileRef = database.ref("user_profile/");
    var userStatsRef = database.ref("usage_GameStatsInfo/");
    var sectionRef = database.ref("user_section/");

    // Object to hold the combined data
    var combinedData = {};

    // Get selected date range and gender
    var startDate = document.getElementById('startDate').value;
    var endDate = document.getElementById('endDate').value;
    var selectedGender = document.getElementById('genderSelect').value;

    // Fetch user profile data
    userProfileRef.once('value').then(function(snapshot) {
        snapshot.forEach(function(userSnapshot) {
            var userId = userSnapshot.key;
            var userData = userSnapshot.val();
            combinedData[userId] = { profile: userData, stats: [], section: null };
        });

        // Fetch usage stats data
        userStatsRef.once('value').then(function(snapshot) {
            snapshot.forEach(function(statsSnapshot) {
                var statsData = statsSnapshot.val();
                var userId = statsData.userId;
                var statDate = statsData.date;

                if (combinedData[userId] && isDateInRange(statDate, startDate, endDate)) {
                    combinedData[userId].stats.push(statsData);
                }
            });

            // Fetch section data
            sectionRef.once('value').then(function(snapshot) {
                snapshot.forEach(function(sectionSnapshot) {
                    var sectionData = sectionSnapshot.val();
                    var sectionId = sectionData.UID;

                    // Find the user with matching sectionId
                    for (var userId in combinedData) {
                        if (combinedData[userId].profile.sectionId === sectionId) {
                            combinedData[userId].section = sectionData;
                        }
                    }
                });

                // Now `combinedData` holds merged data from all sources
                generateGraphFromData(combinedData, selectedGender);
            });
        });
    });
}

function isDateInRange(date, startDate, endDate) {
    if (!startDate || !endDate) return true; // If no date range is selected, include all dates
    return date >= startDate && date <= endDate;
}

function generateGraphFromData(data, genderFilter) {
    var sectionStats = {};

    for (var userId in data) {
        var user = data[userId];
        var section = user.section;
        var gender = user.profile.gender.toLowerCase();

        if (section && (genderFilter === '' || gender === genderFilter)) {
            if (!sectionStats[section.sectionName]) {
                sectionStats[section.sectionName] = {};
            }

            user.stats.forEach(function(stat) {
                if (!sectionStats[section.sectionName][stat.appName]) {
                    sectionStats[section.sectionName][stat.appName] = 0;
                }
                // Convert totalTimeInForeground to seconds
                var timeParts = stat.totalTimeInForeground.split(':');
                var seconds = (+timeParts[0]) * 3600 + (+timeParts[1]) * 60 + (+timeParts[2]);
                sectionStats[section.sectionName][stat.appName] += seconds;
            });
        }
    }

    // Prepare data for the chart
    var sectionNames = Object.keys(sectionStats);
    var chartContainer = document.getElementById('chartContainer');
    chartContainer.innerHTML = ''; // Clear any existing charts

    var chartRow;
    sectionNames.forEach(function(sectionName, index) {
        if (index % 3 === 0) {
            // Create a new row every 3 charts
            chartRow = document.createElement('div');
            chartRow.classList.add('chart-row');
            chartContainer.appendChild(chartRow);
        }

        var games = sectionStats[sectionName];
        var topGames = Object.entries(games).sort((a, b) => b[1] - a[1]).slice(0, 3);

        var chartLabels = topGames.map(game => game[0]);
        var chartData = topGames.map(game => game[1]);

        var chartWrapper = document.createElement('div');
        chartWrapper.classList.add('chart-container');

        var header = document.createElement('h3');
        header.textContent = sectionName;
        chartWrapper.appendChild(header);

        var ctx = document.createElement('canvas');
        ctx.id = sectionName.replace(/\s+/g, '_') + '_chart';
        chartWrapper.appendChild(ctx);

        chartRow.appendChild(chartWrapper);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Top 3 Games',
                    data: chartData,
                    backgroundColor: `rgba(${Math.floor(Math.random()*255)}, ${Math.floor(Math.random()*255)}, ${Math.floor(Math.random()*255)}, 0.5)`,
                    borderColor: `rgba(${Math.floor(Math.random()*255)}, ${Math.floor(Math.random()*255)}, ${Math.floor(Math.random()*255)}, 1)`,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' seconds';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Games'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Time (Seconds)'
                        }
                    }
                }
            }
        });
    });
    document.getElementById("loading-message").style.display = "none";
}

function handleSearch() {
    var searchInput = document.getElementById('sectionSearch').value.toLowerCase();
    var chartRows = document.querySelectorAll('#chartContainer .chart-row');
    chartRows.forEach(row => {
        var containers = row.querySelectorAll('.chart-container');
        var rowVisible = false;
        containers.forEach(container => {
            var sectionName = container.querySelector('h3').textContent.toLowerCase();
            if (sectionName.includes(searchInput) || searchInput === '') {
                container.style.display = 'block'; // Show matching charts
                rowVisible = true;
            } else {
                container.style.display = 'none'; // Hide non-matching charts
            }
        });
        row.style.display = rowVisible ? 'flex' : 'none'; // Show row if any charts are visible
    });
}

</script>

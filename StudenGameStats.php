<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>


<div class="card mb-4">

    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-1"></i>View Gaming Consumption</span>
        <button type="button" class="btn btn-success" onclick="goBack()">Back to List</button>
    </div>
    <div class="card-body p-3">
        <!-- Existing form fields -->
        <div class="row g-2">
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text">Full Name</span>
                    <input type="text" class="form-control form-control-sm" id="fullname" style="max-width: 100%;" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text">Parent Name</span>
                    <input type="text" class="form-control form-control-sm" id="parentName" style="max-width: 100%;" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text">Contact Number</span>
                    <input type="text" class="form-control form-control-sm" id="phone" style="max-width: 100%;" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text">Email</span>
                    <input type="text" class="form-control form-control-sm" id="Email" placeholder="Enter Section" style="max-width: 100%;" readonly>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text">Date From</span>
                    <input type="date" class="form-control form-control-sm" id="date-from" style="max-width: 100%;">
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text">Date To</span>
                    <input type="date" class="form-control form-control-sm" id="date-to" style="max-width: 100%;">
                </div>
            </div>
            <div class="col-12 mb-2 d-flex justify-content-end">
                <button class="btn btn-primary btn-sm" onclick="search()">Search <i class="fas fa-search"></i></button>
            </div>
        </div>
        <div id="loading-message" class="alert alert-info" style="display: none;">
            <strong>Please wait, loading...</strong>
        </div>
<!-- Canvas for Line Graph -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h4 class="mb-0 fs-5">Gaming Consumption Graph</h4>
    </div>
    <div class="card-body p-3">
        <canvas id="consumption-graph" width="800" height="400"></canvas> <!-- Set the height here if you prefer -->
    </div>
</div>
        <!-- Table for displaying consumption data with progress bar -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h4 class="mb-0 fs-5">Gaming Consumption Details</h4>
            </div>
            <div class="card-body p-3">
                <table class="table table-bordered" id="consumption-table">
                    <thead>
                        <tr>
                            <th class="app-info-col">App Info</th> <!-- Reduced width column -->
                            <th>Progress</th> <!-- Column for progress bar and total time -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    document.addEventListener('DOMContentLoaded', function() {
        firebase.database().ref("user_profile/")
            .orderByChild("uid").equalTo(id).once("value")
            .then(function(snap) {
                const data = snap.val();
                if (data) {
                    Object.keys(data).forEach(key => {
                        const childData = data[key];
                        document.getElementById("fullname").value = childData.studentName || '';
                        document.getElementById("parentName").value = childData.parentName || '';
                        document.getElementById("Email").value = childData.email || '';
                        document.getElementById("phone").value = childData.phone || '';
                    });
                } else {
                    console.log('No data found for the given ID');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    });

    function goBack() {
        window.history.back();
    }

    function search() {
        const dateFrom = document.getElementById("date-from").value;
        const dateTo = document.getElementById("date-to").value;

        if (!dateFrom || !dateTo) {
            alert('Please select both start and end dates.');
            return;
        }

        document.getElementById("loading-message").style.display = "block";
        firebase.database().ref("usage_GameStatsInfo/")
            .orderByChild("userId").equalTo(id).once("value")
            .then(function(snap) {
                const data = snap.val();
                const tableBody = document.querySelector('#consumption-table tbody');
                tableBody.innerHTML = ''; // Clear any existing rows

                // Prepare data for the graph
                const labels = [];
                const dataPoints = [];
                let totalUsageTime = 0; // Total usage time within the selected date range

                if (data) {
                    const appStats = {};

                    // Aggregate data by app
                    Object.keys(data).forEach(key => {
                        const childData = data[key];
                        const dateOfRecord = new Date(childData.date);
                        const startDate = new Date(dateFrom);
                        const endDate = new Date(dateTo);

                        if (dateOfRecord >= startDate && dateOfRecord <= endDate) {
                            const totalTime = childData.totalTimeInForeground || '00:00:00';
                            const [hours, minutes, seconds] = totalTime.split(':').map(Number);
                            const totalSeconds = hours * 3600 + minutes * 60 + seconds;

                            const appName = childData.appName || 'Unknown App';
                            if (!appStats[appName]) {
                                appStats[appName] = 0;
                            }
                            appStats[appName] += totalSeconds;

                            // Accumulate total usage time
                            totalUsageTime += totalSeconds;
                        }
                    });

                    // Calculate percentage and populate table
                    Object.keys(appStats).forEach(appName => {
                        const totalSeconds = appStats[appName];

                        const daysRange = Math.ceil((new Date(dateTo) - new Date(dateFrom)) / (1000 * 60 * 60 * 24)); // Days between dates
                        const dailySeconds = 86400; // Seconds in a day

                        const percentage = Math.min(100, (totalSeconds / (dailySeconds * (daysRange + 1))) * 100);

                        // const percentage = totalUsageTime > 0 ? (totalSeconds / totalUsageTime) * 100 : 0;

                        const totalTimeStr = formatSecondsToReadableTime(totalSeconds);

                        const row = document.createElement('tr');

                        // App Info cell (logo + app name)
                        const infoCell = document.createElement('td');
                        infoCell.className = 'app-info-cell';
                        
                        const appLogo = document.createElement('img');
                        appLogo.src = "data:image/png;base64," + (Object.values(data).find(item => item.appName === appName)?.appLogo || '');
                        // appLogo.alt = 'App Logo';
                        appLogo.className = 'app-logo'; // Apply logo class
                        
                        const appNameElem = document.createElement('span');
                        appNameElem.textContent = appName;

                        infoCell.appendChild(appLogo);
                        // infoCell.appendChild(appNameElem);
                        row.appendChild(infoCell);

                        // Progress cell with app name and total time
                        const progressCell = document.createElement('td');
                        const progressContainer = document.createElement('div');
                        progressContainer.className = 'progress-container';

                        const timeText = document.createElement('div');
                        timeText.className = 'total-time-text';
                        timeText.textContent = appName + ': ' + totalTimeStr + ' (' + percentage.toFixed(1) + '%)';

                        const progressBarBackground = document.createElement('div');
                        progressBarBackground.className = 'progress-bar-background';
                        
                        const progressBar = document.createElement('div');
                        progressBar.className = 'progress-bar';
                        progressBar.style.width = percentage + '%';

                        progressBarBackground.appendChild(progressBar);
                        progressContainer.appendChild(timeText);
                        progressContainer.appendChild(progressBarBackground);

                        progressCell.appendChild(progressContainer);
                        row.appendChild(progressCell);

                        tableBody.appendChild(row);

                        // Prepare data for the graph
                        labels.push(appName);
                        dataPoints.push(percentage);
                        document.getElementById("loading-message").style.display = "none";
                    });

                    // Update the graph
                    updateGraph(labels, dataPoints);
                } else {
                    console.log('No data found for the given ID');
                }
              
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    let chartInstance = null;

function updateGraph(labels, dataPoints) {
    const ctx = document.getElementById('consumption-graph').getContext('2d');
    
    // Destroy the existing chart if it already exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Create a new chart instance
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Gaming Consumption (%)',
                data: dataPoints,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Apps'
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100, // Max value set to 100 for percentage
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                }
            }
        }
    });
}
    function formatSecondsToReadableTime(totalSeconds) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
</script>

<style>
    .app-info-col {
        width: 120px; /* Adjust width of the App Info column */
        text-align: center;
    }

    .app-info-cell {
        display: flex;
        align-items: center;
        justify-content: start;
    }

    .progress-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }

    .progress-bar-background {
        background-color: #e9ecef; /* Gray background */
        border-radius: 4px;
        height: 20px;
        width: 100%;
        position: relative;
    }

    .progress-bar {
        height: 100%;
        background-color: #007bff; /* Blue color */
        border-radius: 4px;
    }

    .total-time-text {
        margin-bottom: 5px;
        font-size: 14px; /* Adjust font size */
    }

    .app-logo {
        width: 40px; /* Adjust logo size */
        height: 40px; /* Adjust logo size */
        margin-right: 10px; /* Space between logo and text */
    }

    #consumption-graph {
        height: 400px; /* Adjust the height as needed */
        width: 100%; /* Ensure it takes the full width of the container */
    }
</style>

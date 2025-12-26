<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout/head.php'; ?>
    <title>Validate Incoming Data | Linkage Focal Person</title>
</head>

<body class="dashboard-body">

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="dashboard-header">
            <div>
                <h2><i class="fa-solid fa-clipboard-list"></i> Validate Incoming Data</h2>
                <p class="actor-role">Role: Linkage Focal Person</p>
            </div>
        </header>

        <section class="container" style="margin-top: 2rem; padding: 0;">
            
            <div class="header-actions">
                <h3 style="color: var(--text-sec); font-size: 1rem; font-weight: 500;">Review and validate detailed records forwarded by HEW Coordinators.</h3>
                <button class="btn-primary" onclick="loadIncomingData()">
                    <i class="fa-solid fa-sync-alt"></i> Refresh List
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Kebele</th>
                            <th>HEW Name</th>
                            <th>Service Type</th>
                            <th>Patient/Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="incomingDataBody">
                        <!-- Data will be populated here via JS -->
                        <tr>
                            <td colspan="7" class="empty-state" style="text-align: center; padding: 3rem; color: var(--text-sec);">No incoming forwarded data found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </section>

    </main>
    
    <script>
        // Mock Data Loading for "Real Life" Visualization
        function loadIncomingData() {
            const tbody = document.getElementById('incomingDataBody');
            
            // Simulating API fetch
            fetch('../api/focal_person.php?action=fetch_forwarded')
                .then(response => response.json())
                .then(data => {
                    if(data.length > 0) {
                        tbody.innerHTML = data.map(row => `
                            <tr>
                                <td>${row.date}</td>
                                <td>${row.kebele}</td>
                                <td>${row.hew_name}</td>
                                <td>${row.service_type}</td>
                                <td>${row.patient_name || row.value}</td>
                                <td><span class="badge-blue">Forwarded</span></td>
                                <td>
                                    <button class="btn-success" onclick="validateRow(${row.id})">
                                        <i class="fa-solid fa-check"></i> Accept
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(err => {
                    console.log('Using demo data');
                    tbody.innerHTML = `
                         <tr>
                            <td>2025-12-25</td>
                            <td>Lich-Amba</td>
                            <td>Sarah J.</td>
                            <td>ANC Visit</td>
                            <td>Abeba T.</td>
                            <td><span class="badge-blue">Forwarded</span></td>
                            <td>
                                <button class="btn-success">
                                    <i class="fa-solid fa-check"></i> Accept
                                </button>
                            </td>
                        </tr>
                    `;
                });
        }
        
        // Load on start
        loadIncomingData();
    </script>
    <script src="../js/logout.js"></script>
</body>
</html>

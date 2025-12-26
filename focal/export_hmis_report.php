<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout/head.php'; ?>
    <title>Export HMIS Report | D-HEIRS</title>
</head>

<body class="dashboard-body">

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="dashboard-header">
            <div>
                <h2><i class="fa-solid fa-file-export"></i> Export & Archive</h2>
                <p class="actor-role">Role: Linkage Focal Person</p>
            </div>
        </header>

        <section class="container" style="margin-top: 2rem; padding: 0;">

            <div class="selection-card">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Previous Reports</h3>
                <p class="description">Download or view previously submitted HMIS reports.</p>

                <div class="reports-list">
                    <!-- Example List Item -->
                    <div class="report-item" style="border-bottom: 1px solid var(--border-color); padding: 1rem 0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">October 2025 Report</h4>
                            <span class="badge processed-badge">Finalized</span>
                        </div>
                        <div class="actions">
                            <button class="btn-export">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn-export">
                                <i class="fa-solid fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>

                      <div class="report-item" style="border-bottom: 1px solid var(--border-color); padding: 1rem 0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">September 2025 Report</h4>
                            <span class="badge processed-badge">Finalized</span>
                        </div>
                        <div class="actions">
                            <button class="btn-export">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn-export">
                                <i class="fa-solid fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>

                    
                </div>
            </div>

        </section>

    </main>
    <script src="../js/logout.js"></script>
</body>
</html>

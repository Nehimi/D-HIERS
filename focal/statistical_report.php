<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layout/head.php'; ?>
    <title>Generate Statistical Report | D-HEIRS</title>
</head>

<body class="dashboard-body">

    <!-- Sidebar -->
    <?php include 'layout/sidebar.php'; ?>

    <main class="main-content">
        
        <header class="dashboard-header">
            <div>
                <h2><i class="fa-solid fa-chart-pie"></i> Reporting & Summarization</h2>
                <p class="actor-role">Role: Linkage Focal Person</p>
            </div>
        </header>

        <section class="report-selection-section" style="margin-top: 2rem;">
            <div class="selection-card">
                <h3>Generate Woreda Statistical Report</h3>
                <p class="description">Select the reporting period for which all Kebele data has been validated and
                    summarized.</p>

                <form method="POST" action="hmis_data_submission.php" class="report-form">

                    <div class="form-group">
                        <label for="reportMonth"><i class="fa-solid fa-calendar-alt"></i> Select Reporting Month</label>
                        <select id="reportMonth" name="reportMonth" required>
                            <option value="">-- Choose Month --</option>
                            <option value="2025-11">November 2025 (Ready)</option>
                            <option value="2025-10" disabled>October 2025 (Processed)</option>
                            <option value="2025-12">December 2025 (Pending)</option>
                        </select>
                        <small class="help-text">Only periods with **Focal-Validated** data can be
                            selected.</small>
                    </div>

                    <div class="form-group">
                        <label for="kebeleFilter"><i class="fa-solid fa-location-dot"></i> Filter by Kebele
                            (Optional)</label>
                        <select id="kebeleFilter" name="kebeleFilter">
                            <option value="all">-- All Kebeles (Woreda Summary) --</option>
                            <option value="Lich-Amba">Lich-Amba</option>
                            <option value="Arada">Arada</option>
                            <option value="Lereba">Lereba</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button name="GenerateReport" type="submit" class="btn-primary">
                            <i class="fa-solid fa-file-export"></i> Generate Statistical Report
                        </button>
                    </div>
                </form>

            </div>

            <div class="status-summary-card">
                <h3>Report Status Overview</h3>
                <ul class="status-list">
                    <li class="status-item ready">
                        <span class="period">November 2025</span>
                        <span class="badge ready-badge">Ready to Summarize</span>
                    </li>
                    <li class="status-item pending">
                        <span class="period">December 2025</span>
                        <span class="badge pending-badge">Pending Validation</span>
                    </li>
                    <li class="status-item processed">
                        <span class="period">October 2025</span>
                        <span class="badge processed-badge">Finalized</span>
                    </li>
                    <li class="status-item missing">
                        <span class="period">September 2025</span>
                        <span class="badge missing-badge">Data Missing</span>
                    </li>
                </ul>
            </div>

        </section>

    </main>
    <script src="../js/logout.js"></script>
</body>
</html>

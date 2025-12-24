<?php
session_start();
include "../../dataBaseConnection.php";

// Set HEW Name (In a real app, this comes from the session)
$hewName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Semira Kedir";
$kebeleName = isset($_SESSION['kebele']) ? $_SESSION['kebele'] : "Lich-Amba";

// Fetch Stats for the Report
$totalHouseholdsResult = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as count FROM household");
$totalHouseholds = mysqli_fetch_assoc($totalHouseholdsResult)['count'];

$totalHealthDataResult = mysqli_query($dataBaseConnection, "SELECT COUNT(*) as count FROM health_data");
$totalHealthData = mysqli_fetch_assoc($totalHealthDataResult)['count'];

// Get recent health data for the report table
$recentHealthQuery = mysqli_query($dataBaseConnection, "SELECT h.householdId, hh.memberName, h.serviceType, h.visitDate 
                                                      FROM health_data h 
                                                      JOIN household hh ON h.householdId = hh.householdId 
                                                      ORDER BY h.id DESC LIMIT 10");

$currentDate = date('F d, Y');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Reports | D-HEIRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="../css/hew.css">
    <link rel="stylesheet" href="../css/hew_style.css">
    <link rel="stylesheet" href="../../css/logout.css">
    
    <style>
        /* Print-specific styles */
        @media print {
            body * {
                visibility: hidden;
            }
            #printableReport, #printableReport * {
                visibility: visible;
            }
            #printableReport {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
                color: black;
                padding: 20px;
                border: none;
                box-shadow: none;
            }
            .sidebar, .dashboard-header, .form-actions-web {
                display: none !important;
            }
        }

        .report-preview {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #e2e8f0;
        }

        .report-header {
            text-align: center;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .report-header h1 {
            color: #0f766e;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            color: #475569;
        }

        .report-section {
            margin-bottom: 2rem;
        }

        .report-section h3 {
            color: #0f766e;
            font-size: 1.1rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .report-table th, .report-table td {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .report-table th {
            background-color: #f8fafc;
            color: #0f766e;
        }

        .signature-area {
            margin-top: 4rem;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 3rem;
            padding-top: 0.5rem;
            font-weight: bold;
        }
    </style>
</head>

<body class="dashboard-body">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <img src="../images/images.jpg" alt="Logo">
            </div>
            <div class="brand-text">
                D-HEIRS
                <span>HEW Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="hew_dashboard.php" class="nav-item">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="register_household.php" class="nav-item">
                <i class="fa-solid fa-users-gear"></i>
                <span>Register Household</span>
            </a>
            <a href="enter_health_data.php" class="nav-item">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Enter Health Data</span>
            </a>
            <a href="edit_submitted_data.php" class="nav-item">
                <i class="fa-solid fa-file-pen"></i>
                <span>Edit Submitted Data</span>
            </a>
            <a href="submit_reports.php" class="nav-item active">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Submit Reports</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../../index.html" class="nav-item logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="dashboard-header">
            <h2>Report Generation</h2>
            <div class="header-actions">
                <div class="user-profile">
                    <img src="../images/avatar.png" alt="HEW" class="avatar-sm">
                    <div class="user-info">
                        <span class="name"><?php echo htmlspecialchars($hewName); ?></span>
                        <span class="role">Health Ext. Worker</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="form-actions-web" style="margin-bottom: 2rem; display: flex; justify-content: center; gap: 1rem; max-width: 800px; margin-left: auto; margin-right: auto;">
                <button onclick="window.print()" class="btn-secondary" style="padding: 0.75rem 1.5rem;">
                    <i class="fa fa-print"></i> Print Preview
                </button>
                <button id="submitToCoordBtn" class="btn-primary" style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #0f766e, #115e59); border: none; box-shadow: 0 4px 6px rgba(15, 118, 110, 0.2);">
                    <i class="fa fa-paper-plane"></i> Finalize & Submit to Coordinator
                </button>
            </div>

            <!-- Printable Report Area -->
            <div id="printableReport" class="report-preview">
                <div class="report-header">
                    <h1>Weekly Health Activity Report</h1>
                    <p>D-HEIRS Health Extension Portal</p>
                </div>

                <div class="report-meta">
                    <div>
                        <strong>Kebele:</strong> <?php echo htmlspecialchars($kebeleName); ?><br>
                        <strong>Report Date:</strong> <?php echo $currentDate; ?>
                    </div>
                    <div style="text-align: right;">
                        <strong>Prepared By:</strong> <?php echo htmlspecialchars($hewName); ?><br>
                        <strong>ID:</strong> HEW-001
                    </div>
                </div>

                <div class="report-section">
                    <h3>summary Statistics</h3>
                    <table class="report-table">
                        <tr>
                            <th>Metric</th>
                            <th>Count</th>
                        </tr>
                        <tr>
                            <td>Total Registered Households</td>
                            <td><?php echo $totalHouseholds; ?></td>
                        </tr>
                        <tr>
                            <td>Total Health Services Provided</td>
                            <td><?php echo $totalHealthData; ?></td>
                        </tr>
                    </table>
                </div>

                <div class="report-section">
                    <h3>Recent Health Service Activities</h3>
                    <?php if (mysqli_num_rows($recentHealthQuery) > 0): ?>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>HH ID</th>
                                <th>Beneficiary Name</th>
                                <th>Service Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recentHealthQuery)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['householdId']); ?></td>
                                <td><?php echo htmlspecialchars($row['memberName']); ?></td>
                                <td><?php echo ucwords(str_replace('_', ' ', $row['serviceType'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['visitDate'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p style="text-align: center; color: #64748b; padding: 1rem;">No detailed health data available yet.</p>
                    <?php endif; ?>
                </div>

                <div class="signature-area">
                    <div class="signature-box">
                        <p>Certified Correct</p>
                        <div class="signature-line">
                            <?php echo htmlspecialchars($hewName); ?><br>
                            <span style="font-weight: normal; font-size: 0.85rem;">Health Extension Worker</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../../js/logout.js"></script>
    <script>
        document.getElementById('submitToCoordBtn').addEventListener('click', function() {
            const btn = this;
            if (!confirm("Are you sure you want to finalize and submit this report to the Coordinator?")) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

            fetch('../../api/hew_coordinator.php?action=notify_submission', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    hewId: '<?php echo $_SESSION['userId'] ?? "HEW-001"; ?>',
                    kebele: '<?php echo $kebeleName; ?>',
                    hewName: '<?php echo $hewName; ?>'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Submission Successful! Your Coordinator has been notified.");
                    btn.innerHTML = '<i class="fa fa-check"></i> Report Submitted';
                    btn.style.background = '#64748b'; // Gray out after success
                } else {
                    alert("Submission Error: " + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-paper-plane"></i> Finalize & Submit to Coordinator';
                }
            })
            .catch(err => {
                console.error(err);
                alert("Network error occurred during submission.");
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-paper-plane"></i> Finalize & Submit to Coordinator';
            });
        });
    </script>
</body>

</html>

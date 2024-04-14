<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
require_once "../php/report-page.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- kani ang i copy paste =. ilisan ang karaan na datatables na link -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.2/b-3.0.1/r-3.0.0/sc-2.4.1/sb-1.7.0/sp-2.3.0/sl-2.0.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.2/b-3.0.1/r-3.0.0/sc-2.4.1/sb-1.7.0/sp-2.3.0/sl-2.0.0/datatables.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../css/dashboard_styles.css" />
    <link rel="stylesheet" href="../css/payroll-styles.css" />
    <link rel="stylesheet" href="../../components/_components.css">
</head>

<body>
    <div class="app-bar d-lg-none d-flex">
        <a href="#">
            <button id="burger-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                <i class="bi bi-list"></i>
            </button>
        </a>
        <span class="mx-3 sidebar-logo"><a href="#">TwinPeaks</a></span>
    </div>

    <div class="wrapper">
        <aside id="sidebar" tabindex="-1" class="shadow-lg offcanvas-lg offcanvas-start" data-bs-backdrop="true">
            <div class="d-flex mb-2">
                <button id="toggle-btn" type="button">
                    <i class="bi bi-calendar-week"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">PeakSched</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="../dashboard.php" class="sidebar-link">
                        <i class="bi bi-house"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../appointments/" class="sidebar-link">
                        <i class="bi bi-calendar2"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../employee_page.php" class="sidebar-link">
                        <i class="bi bi-person"></i>
                        <span>Employee</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../customer_page.php" class="sidebar-link">
                        <i class="bi bi-emoji-smile"></i>
                        <span>Customer</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../payroll/" class="sidebar-link ">
                        <i class="bi bi-wallet"></i>
                        <span>Payroll</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../services_page.php" class="sidebar-link">
                        <i class="bi bi-file-post-fill"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="./" class="sidebar-link selected">
                        <i class="bi bi-flag-fill"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../notifcation/" class="sidebar-link">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../setting_account_page.php" class="sidebar-link ">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="../php/logout.php" class="sidebar-link">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <section class="main p-2" id="main">
            <div class="container-fluid" id="mainArea">
                <div class="mb-5">
                    <h1>Reports</h1>
                </div>
                <div class="container-fluid" id="subArea-single">
                    <div>
                        <h5>All Reports</h5>
                    </div>
                    <table id="myTable" class="table table-hover table-striped">
                        <!-- //!TODO: para mailisan ang color sa header ug status-->
                        <thead id="tableHead">
                            <th style="color: white;">Report Id</th>
                            <th style="color: white;">Supervisor</th>
                            <th style="color: white;">Appointment Id</th>
                            <th style="color: white;">Date Reported</th>
                            <th style="color: white;">Time</th>
                            <th style="color: white;">Status</th>
                            <th style="color: white;">Actions</th>
                        </thead>
                        <tbody>
                            <?php
                            // LOOP TILL END OF DATA
                            while ($rows = $result->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $rows['report_id']; ?></td>
                                    <td><?php echo $rows['Supervisor']; ?></td>
                                    <td><?php echo $rows['appointment_id']; ?></td>
                                    <td><?php echo $rows['report_date']; ?></td>
                                    <td><?php echo $rows['report_time']; ?></td>
                                    <?php
                                    if ($rows['status'] == 'Pending') $badgeType = 'my-badge-pending';
                                    else if ($rows['status'] == 'Approved') $badgeType = 'my-badge-approved';
                                    ?>
                                    <td><span class="badge rounded-pill <?php echo $badgeType ?>"><?php echo $rows['status']; ?></span></td>
                                    <td></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <script src="../js/data-table-report.js"></script>
        <script src="../js/script.js"></script>
</body>

</html>
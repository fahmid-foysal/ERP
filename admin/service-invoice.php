<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mondol Motors - Service invoice</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet">
    <link href="dist/css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    <style>
       @media (max-width: 768px) {
            /* Sidebar default (hidden) */
            .left-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100%;
                background: #343a40;
                transition: transform 0.3s ease-in-out;
                transform: translateX(-100%);
                z-index: 1000;
            }
        
            /* Sidebar when active (visible) */
            .left-sidebar.active {
                transform: translateX(0);
            }
        
            /* Page content wrapper */
            .page-wrapper {
                transition: margin-left 0.3s ease-in-out;
                margin-left: 0;
            }
        
            /* Move content when sidebar is open */
            .page-wrapper.sidebar-open {
                margin-left: 250px;
            }
        
            /* Sidebar toggle button */
            .sidebar-toggle {
                position: fixed;
                left: 10px;
                top: 10px;
                background: #2962FF;
                color: white;
                border: none;
                padding: 10px 15px;
                cursor: pointer;
                z-index: 1100;
            }
        }



    </style>
</head>

<body>
    <div id="main-wrapper">
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="container-fluid">
                    <div class="row w-100 align-items-center">
                        <div class="col-auto">
                            <a class="navbar-brand" href="index.php">
                                <span class="logo-text">
                                    <h3 class="m-0">Mondol Motors</h3>
                                </span>
                            </a>
                        </div>
        
                        <div class="col text-end">
                            <button id="sidebarToggle" class="btn btn-outline-light">☰</button>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <?php include 'template_html/sidebar.html' ?>


        <div class="page-wrapper bg-white">
            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-primary">Service Invoice</h4>
                    <button class="btn btn-success" onclick="showServiceInvoice()">Add service invoice</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Service invoice no</th>
                                <th>Service</th>
                                <th>Total Amount</th>
                                <th>Total Paid</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Total due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="serviceInvoiceList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="serviceInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Make a New Service Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="serviceInvoiceForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Purchase Date *</label>
                            <input type="date" class="form-control" name="date" id="invoiceDate" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Customer Name *</label>
                            <input type="text" class="form-control" name="cus_name" id="cus_name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Customer Phone *</label>
                            <input type="text" class="form-control" name="cus_phone" id="cus_phone" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Customer Address *</label>
                            <input type="text" class="form-control" name="cus_address" id="cus_address" required>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Service Name *</label>
                            <input type="text" class="form-control" name="service_name" id="service_name" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Amount *</label>
                            <input type="number" step="any" name="amount" class="form-control" min="0" value="0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Paid Amount *</label>
                            <input type="number" step="any" name="paid" class="form-control" min="0" value="0" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="submitBtn" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<diV class="text-center">© Copyright <b>NirmaIT</b> All Rights Reserved</diV>


    
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bootstrap-Select JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"></script>

    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar");
            const pageWrapper = document.querySelector(".page-wrapper");
        
            toggleBtn.addEventListener("click", function () {
                sidebar.classList.toggle("active");
                pageWrapper.classList.toggle("sidebar-open");
            });
        });
    </script>

    
    <script>
    
        function showServiceInvoice() {
            $('#serviceInvoiceModal').modal('show');
        }

    </script>
    <script src="assets/api-calls/logout.js"></script>
    <script src="assets/api-calls/service-invoice.js"></script>
    <script>
        $(document).ready(function () {
        $("#cus_phone").on("input", function () {
            getCustomerInfo();
        });
    });
    </script>
</body>

</html>
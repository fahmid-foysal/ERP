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
    <title>Mondol Motors - Customer</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet">
    <link href="dist/css/style.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap-Select CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    <?php include 'template_html/responsive.html'; ?>

</head>

<body>
    <div id="main-wrapper">
        <?php include 'template_html/header.html'; ?>
        <?php include 'template_html/sidebar.html'; ?>

        <div class="page-wrapper bg-white">
            <div class="container mt-4">

                <!--<div class="d-flex justify-content-between align-items-center mb-3">-->
                <!--    <h4 class="text-primary">Customer</h4>-->
                <!--    <button class="btn btn-success" onclick="showPopup()">Add customer</button>-->
                <!--</div>-->
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Total Amount</th>
                                <th>Total Paid</th>
                                <th>Total Due</th>
                            </tr>
                        </thead>
                        <tbody id="customerList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!--<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">-->
    <!--    <div class="modal-dialog">-->
    <!--        <div class="modal-content">-->
    <!--            <div class="modal-header">-->
    <!--                <h5 class="modal-title" id="supplierModalLabel">supplier Form</h5>-->
    <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
    <!--            </div>-->
    <!--            <div class="modal-body">-->
    <!--                <form id="supplierForm">-->
                        
    <!--                    <div class="mb-3">-->
    <!--                        <label for="supplierName" class="form-label" >Supplier Name</label>-->
    <!--                        <input type="text" class="form-control" placeholder="Enter supplier name" name="name" required>-->
    <!--                    </div>-->
    <!--                    <div class="mb-3">-->
    <!--                        <label for="supplierPhone" class="form-label" >Supplier Phone</label>-->
    <!--                        <input type="text" class="form-control" placeholder="Enter supplier phone" name="phone" required>-->
    <!--                    </div>-->
    <!--                    <div class="mb-3">-->
    <!--                        <label for="supplierAddress" class="form-label" >Supplier Address</label>-->
    <!--                        <input type="text" class="form-control" placeholder="Enter supplier address" name="address" required>-->
    <!--                    </div>-->
    <!--                </form>-->
    <!--            </div>-->
    <!--            <div class="modal-footer">-->
    <!--                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
    <!--                <button type="submit" form="supplierForm" class="btn btn-primary">Submit</button>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->


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
    
    <script src="assets/api-calls/logout.js"></script>
    <script src="assets/api-calls/customer.js"></script>
</body>

</html>
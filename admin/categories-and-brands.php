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
    <title>Mondol Motors - Categories and Brands</title>
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

                <div class="row">
            
                    <!-- Category Table -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-primary">Categories</h4>
                            <button class="btn btn-success btn-sm" onclick="showCategory()">Add Category</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryList"></tbody>
                            </table>
                        </div>
                    </div>
            
                    <!-- Brand Table -->
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-primary">Brands</h4>
                            <button class="btn btn-success btn-sm" onclick="showBrand()">Add Brand</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="brandList"></tbody>
                            </table>
                        </div>
                    </div>
            
                </div>
                <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="supplierModalLabel">Add a new brand</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="brandForm">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Add a brand</label>
                                        <input type="text" name="brand" class="form-control brand" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="categoryModalLabel">Add a new category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="categoryForm">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Add a Category</label>
                                        <input type="text" name="category" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
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
    
        function showCategory() {
            $('#categoryModal').modal('show');
        }
        function showBrand() {
            $('#brandModal').modal('show');
        }

    </script>
    <script src="assets/api-calls/logout.js"></script>
    <script src="assets/api-calls/categories-and-brands.js"></script>
    <script>
        $(document).ready(function () {
        $("#cus_phone").on("input", function () {
            getCustomerInfo();
        });
    });
    </script>
</body>


</html>
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
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <meta name="robots" content="noindex, nofollow">
    <!-- Custom CSS -->
    <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="dist/css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    
    
<![endif]-->
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="ml-2">
                            <a class="navbar-brand" href="index.php">
                                <span class="logo-text">
                                    <h3 class="m-0">Mondol Motors</h3>
                                </span>
                            </a>
                        </div>
    
                        <div class="mr-2">
                            <button onclick="logout();" class="btn btn-danger btn-sm">Logout</button>
                        </div>
                    </div>

                </div>
            </nav>
        </header>


        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper" style="margin-left:0px">

            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Sales Cards  -->
                <!-- ============================================================== -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-cyan text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                                <h6 class="text-white">Dashboard</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-4 col-xlg-3" onclick="window.location.href='purchase.php';">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white"><i class="mdi  mdi-ticket"></i></h1>
                                <h6 class="text-white">Manage purchase</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                    <div class="col-md-6 col-lg-2 col-xlg-3" onclick="window.location.href='manage-user.php';">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-calendar-check"></i></h1>
                                <h6 class="text-white">Manage User</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xlg-3" onclick="window.location.href='expenses.php';">
                        <div class="card card-hover">
                            <div class="box bg-danger text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-cash"></i></h1>
                                <h6 class="text-white">Expenses</h6>
                            </div>
                        </div>
                    </div>
                
                    <!-- Invoice Card -->
                    <div class="col-md-6 col-lg-4 col-xlg-3" onclick="window.location.href='invoice.php';">
                        <div class="card card-hover">
                            <div class="box bg-warning text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-arrow-all"></i></h1>
                                <h6 class="text-white">Invoice</h6>
                            </div>
                        </div>
                    </div>
                
                    
                
                    <!-- Product Card -->
                    <div class="col-md-6 col-lg-2 col-xlg-3" onclick="window.location.href='product.php';">
                        <div class="card card-hover">
                            <div class="box bg-info text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-cube"></i></h1>
                                <h6 class="text-white">Product</h6>
                            </div>
                        </div>
                    </div>
                    <!-- Product Card -->
                    <div class="col-md-6 col-lg-4 col-xlg-3" onclick="window.location.href='customer.php';">
                        <div class="card card-hover">
                            <div class="box bg-info text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-account-multiple"></i></h1>
                                <h6 class="text-white">Customer</h6>
                            </div>
                        </div>
                    </div>
                
                    <!-- Supplier Card -->
                    <div class="col-md-6 col-lg-2 col-xlg-3" onclick="window.location.href='supplier.php';">
                        <div class="card card-hover">
                            <div class="box bg-dark text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-truck"></i></h1>
                                <h6 class="text-white">Supplier</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Column -->



                </div>
                <!-- ============================================================== -->
                <!-- Sales chart -->
                <!-- ============================================================== -->

                <div class="col-lg-3">
                    
                    <div class="row">

                        <!-- Filter Form -->
                        <div class="col-12 mb-3">
                            <form id="businessSummeryForm">
                                <div class="row g-2">
                                    <div class="col-md-5 col-sm-12">
                                        <label>From</label>
                                        <input type="date" class="form-control" name="from_date" id="from_date" placeholder="From Date">
                                    </div>
                                    <div class="col-md-5 col-sm-12">
                                        <label>To</label>
                                        <input type="date" class="form-control" name="till_date" id="till_date" placeholder="Till Date">
                                    </div>
                                    <div class="col-md-2 col-sm-12">
                                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i></button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    
                        <!-- Total Amount -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-money-bill-wave m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="totalAmount">0.00</h5>
                                <small class="font-light">Total Sale</small>
                            </div>
                        </div>
                        
                        <!-- Total Expense -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-chart-line m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="totalExpense">0.00</h5>
                                <small class="font-light">Total Expense</small>
                            </div>
                        </div>
                        <!-- Service sale -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-cube m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="productSale">0.00</h5>
                                <small class="font-light">Product Sale</small>
                            </div>
                        </div>
                        
                        <!-- Total Expense -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-wrench m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="serviceSale">0.00</h5>
                                <small class="font-light">Service sale</small>
                            </div>
                        </div>
                        
                        <!-- Total Paid -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-check-circle m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="totalPaid">0.00</h5>
                                <small class="font-light">Total Paid</small>
                            </div>
                        </div>
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-shopping-cart m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="totalPurchase">0.00</h5>
                                <small class="font-light">Total Purchase</small>
                            </div>
                        </div>
                        
                        <!-- Total Due -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-hourglass-half m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="totalDue">0.00</h5>
                                <small class="font-light">Total Due</small>
                            </div>
                        </div>
                        
                        <!-- Total Profit -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-coins m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="totalProfit">0.00</h5>
                                <small class="font-light">Total Profit</small>
                            </div>
                        </div>
                        
                        <!-- Payable -->
                        <div class="col-6 m-t-15">
                            <div class="bg-dark p-10 text-white text-center">
                                <i class="fa fa-wallet m-b-5 font-16"></i>
                                <h5 class="m-b-0 m-t-5" id="payable">0.00</h5>
                                <small class="font-light">Payable</small>
                            </div>
                        </div>

                    
                    </div>
                <!-- column -->
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <diV class="text-center">© Copyright <b>NirmaIT</b> All Rights Reserved</diV>
    
    <?php include 'template_html/scripts.html'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/api-calls/index.js"></script>
    

</body>

</html>
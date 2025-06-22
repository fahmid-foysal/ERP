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
    <title>Mondol Motors - Puchase</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        /* Add some print-specific styles */
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 15px;
            }
        }

        /* Regular styles for better PDF layout */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .purchase-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .logo-area {
            flex: 1;
        }
        address {
            text-align: right;
            flex: 1;
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
                    <h4 class="text-primary">Purchase</h4>
                    <button class="btn btn-success" onclick="showPopup()">Make new purchase</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Purchase no</th>
                                <th>Purchase date</th>
                                
                                <th>Total Amount</th>
                                <th>Total Paid</th>
                                <th>Total due</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Show</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Make a New Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="purchaseForm">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Purchase Date *</label>
                            <input type="date" class="form-control" name="date" id="purchaseDate" required>
                        </div>


                        <div id="input" class="mb-3 form-group">
                                    <label class="mb-2">Supplier *</label>
                                    <div class="d-flex">
                                        <select class="form-control selectpicker" id="select-supplier" data-live-search="true" required>
                                        </select>
                                        <button type="button" class="btn btn-primary ms-2" onclick="showSupplierModal()">Add</button>
                                    </div>
                                </div>

                        <div class="mb-3">
                        <label class="form-label">Product Filter</label>
                        <div class="d-flex">
                                        <select class="form-control selectpicker border border-dark" id="select-category" data-live-search="true">
                                        </select>
                                        <button type="button" class="btn btn-success ms-2" onclick="showCategoryModal()">Add</button>
                                    </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex">
                            <select class="form-control selectpicker border border-dark" id="select-brand" data-live-search="true" >
                            </select>
                            <button type="button" class="btn btn-success ms-2" onclick="showBrandModal()">Add</button>
                        </div>
                    </div>
                    <button type="button" onclick="checkSelection()" class="btn btn-warning">Filter</button>
                    <div class="input-group my-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                                </div>
                                <input type="text" id="barcode" class="form-control ui-autocomplete-input border border-dark" placeholder="Type &amp; Barcode" aria-label="Type &amp; Barcode" onkeydown="return event.keyCode !== 13" autocomplete="off">
                                <button type="button" class="btn btn-warning" onclick="addProductByBarCode()">search</button>
                            </div>
                    <h5 class="mt-4">Selected Products</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>Purchase price</th>
                                    <th>Quantity</th>
                                    <th>Total (Tk)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="selected-products-table">
                                
                            </tbody>
                        </table>
                    </div>
                        

                        


                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Paid Amount</label>
                            <input type="number" step="any" name="paid_amount" class="form-control purchase-total" min="0" value="0" required>
                        </div>

                        

                        <button type="submit" id="submitBtn" class="btn btn-primary">Save</button>
                    </form>

                </div>
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
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Select Products</h5>
                    <form class="d-flex d-none ml-2" id="search-form">
                        <input type="text" class="form-control search-product" name="search-product" id="search-product">
                        <button type="button" class="btn btn-success" onclick="fetchProductBySearch()">Search</button>
                    </form>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="productContainer" class="row"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addSelectedProducts()">Add Selected</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">supplier Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm">
                        
                        <div class="mb-3">
                            <label for="supplierName" class="form-label" >Supplier Name</label>
                            <input type="text" class="form-control" placeholder="Enter supplier name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="supplierPhone" class="form-label" >Supplier Phone</label>
                            <input type="text" class="form-control" placeholder="Enter supplier phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="supplierAddress" class="form-label" >Supplier Address</label>
                            <input type="text" class="form-control" placeholder="Enter supplier address" name="address" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="supplierForm" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="modal fade" id="showPurchaseModal" tabindex="-1" aria-labelledby="showPurchaseModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title" id="showPurchaseModalLabel">Purchase Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
    
          <div class="modal-body">
            <div class="card card-body">
              <div id="print-area">
    
                <div class="purchase-header">
                  <div class="logo-area">
                    <h2 style="font-weight: bold;" class="mb-2 text-center">Mondol Motors</h2>
                  </div>
    
                  
                </div>
    
                <div class="bill-date d-flex justify-content-between">
                  <div class="bill-no">Purchcase :</div>
                  <div class="date">Date: <strong></strong></div>
                  
                </div>
                <div class="name">Purchase name: <span></span></div>
                <div class="phone">Supplier phone: <span></span></div>
    
                <table class="table table-bordered my-3">
                  <thead>
                    <tr>
                      <th>#SL</th>
                      <th>Barcode</th>
                      <th>Product</th>
                      <th>Quantity</th>
                      <th>Unit Price</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Dynamic rows will come here via JS -->
                  </tbody>
                </table>
    
              </div>
            </div>
          </div>
    
          <div class="modal-footer">
            <button id="print-area-btn" class="btn btn-primary">Print</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
    <script src="assets/api-calls/category_and_brand.js"></script>
    
    <script>
      $(document).ready(function() {
        $('.selectpicker').selectpicker();
        getBrands();
        getCategories();
      });
    </script>

    
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
    
    
        function showPopup() {
            $('#purchaseModal').modal('show');
        }

        function showCategoryModal() {
            $('#categoryModal').modal('show');
        }
        
        function showBrandModal() {
            $('#brandModal').modal('show');
        }
        function showSupplierModal() {
            $('#supplierModal').modal('show');
        }
    </script>
    <script src="assets/api-calls/logout.js"></script>
    <script src="assets/api-calls/purchase.js"></script>
    <script>
        document.getElementById('print-area-btn').addEventListener('click', () => {
            const element = document.getElementById('print-area');
            // Get purchase number from the "purchase:" text
            const purchaseText = document.querySelector('.bill-no').textContent;
            const purchaseNumber = purchaseText.split(':')[1]?.trim();
            // Configure html2pdf options
            const options = {
                margin:       10,
                filename: `purchase-${purchaseNumber}.pdf`,
                image:        { type: 'jpeg', quality: 2 },
                html2canvas:  { scale: 2, logging: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'], avoid: 'tr' },
                enableLinks:  true
            };
        
            // Generate PDF
            html2pdf()
                .set(options)
                .from(element)
                .toPdf()
                .get('pdf')
                .then((pdf) => {
                    // Add page breaks automatically if content is too long
                    const totalPages = pdf.internal.getNumberOfPages();
                    for(let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);
                        pdf.setFontSize(10);
                        pdf.text(`Page ${i} of ${totalPages}`, (pdf.internal.pageSize.width - 30), (pdf.internal.pageSize.height - 10));
                    }
                })
                .save();
        });
    </script>
</body>

</html>
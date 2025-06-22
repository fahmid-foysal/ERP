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
    <title>Mondol Motors - invoice</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet">
    <link href="dist/css/style.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap-Select CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <?php include 'template_html/responsive.html'; ?>
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
        .invoice-header {
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
        <?php include 'template_html/header.html'; ?>
        <?php include 'template_html/sidebar.html'; ?>

        <div class="page-wrapper bg-white">
            <div class="container mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-primary">Invoice</h4>
                    <button class="btn btn-success" onclick="showPopup()">Make a new invoice</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="myTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer name</th>
                                <th>Customer contact</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Actions</th>
                                <th>Show</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="checkoutForm">
                    <div class="mb-3">
                        <label class="form-label">Customer Information</label>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control border border-dark" id="customerContact" placeholder="Contact" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control border border-dark" id="customerName" placeholder="Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control border border-dark" id="address" placeholder="Address" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="select-bulk">
                          <label class="form-check-label" for="flexSwitchCheckDefault">Bulk rate</label>
                        </div>
                        <label class="form-label">Product Filter</label>
                        <div class="d-flex">
                                        <select class="form-control selectpicker border border-dark" id="select-category" data-live-search="true" required>
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
                                    <th>Price (Tk)</th>
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
                    <h5 class="mt-4">Service</h5>
                    <div class="table-responsive">
                        <button class="btn btn-success mb-2" onclick="addServiceRow()">Add service</button>

                        <!-- Updated Service Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Price (Tk)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="services-table">
                                <!-- Service rows will be added here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount (%)</label>
                        <input type="number" class="form-control" id="discount" min="0" value="0" disabled oninput="updateTotal()">
                    </div>

                    <div class="text-end">
                        <h5>Total: <span id="invoice-total">0.00</span> Tk</h5>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="showCheckoutModal()" form="checkoutForm" class="btn btn-primary">Go to Checkout</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="showInvoiceModal" tabindex="-1" aria-labelledby="showInvoiceModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title" id="showInvoiceModalLabel">Invoice Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
    
          <div class="modal-body">
            <div class="card card-body">
              <div id="print-area">
    
                <div class="invoice-header">
                  <div class="logo-area">
                    <h2 style="font-weight: bold;" class="mb-2 text-center">Mondol Motors</h2>
                  </div>
    
                  
                </div>
    
                <div class="bill-date d-flex justify-content-between">
                  <div class="bill-no">Invoice :</div>
                  <div class="date">Date: <strong></strong></div>
                </div>
                <div class="name">Name : <span></span></div>
                <div class="phone">Phone : <span></span></div>
    
                <table class="table table-bordered my-3">
                  <thead>
                    <tr>
                      <th>#SL</th>
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
    
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment &gt; <span id="customer_name">Customer name</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Bank Account</label>
                            <select class="form-control select2" name="bank_id" required>
                                <option value="1">Cash</option>
                                <option value="2">Nagad</option>
                                <option value="3">bKash</option>
                            </select>
                            <div class="form-group mt-3">
                                <h5 class="text-center">Payment Option</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-primary w-100 full_pay_btn" id="full_payment_btn">Full Payment</button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button class="btn btn-danger w-100 full_due_btn" id="full_due_btn">Full Due</button>
                                    </div>
                                </div>
                                <div class="mt-3 input-group input-group-lg">
                                    <span class="input-group-text">Pay Amount</span>
                                    <input type="number" class="form-control pay_amount" name="pay_amount" id="pay_amount" min="0" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="text-center">Payment Details</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Subtotal</th>
                                            <td class="text-right"><strong><span class="sub_total">0</span> TK</strong></td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Discount</th>
                                            <td class="text-right"><strong><span class="discount_amount">0.00</span> TK</strong></td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Payable Amount (<span class="total_item">2</span> items)</th>
                                            <td class="text-right"><strong><span class="payable_amount">0</span> TK</strong></td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Paid Amount</th>
                                            <td class="text-right"><strong><span class="paid_amount">0.00</span> TK</strong></td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Due Amount</th>
                                            <td class="text-right"><strong><span class="due_amount">0.00</span> TK</strong></td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Balance</th>
                                            <td class="text-right"><strong><span class="balance">0.00</span> TK</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="showPopup()" data-bs-dismiss="modal">Go Back</button>
                    <button type="button" class="btn btn-success" id="checkout" onclick="generateInvoice()">Checkout</button>
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
        fetchInvoices();
      });
    </script>

    <script>
        function showPopup() {
            $('#invoiceModal').modal('show');
            
        }

        function showCategoryModal() {
            $('#categoryModal').modal('show');
        }
        
        function showBrandModal() {
            $('#brandModal').modal('show');
        }
        
        

    </script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/api-calls/invoice.js"></script>
r    <script>
    $(document).ready(function () {
        $("#customerContact").on("input", function () {
            getCustomerInfo();
        });
    });
</script>
    
    <script src="assets/api-calls/logout.js"></script>
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
        window.addEventListener('DOMContentLoaded', () => {
            const myTable = document.querySelector("#myTable");
            if(myTable){
                new simpleDatatables.DataTable(myTable);
            }
        });
    </script>
    <script>
        document.getElementById('print-area-btn').addEventListener('click', () => {
            const element = document.getElementById('print-area');
            // Get invoice number from the "Invoice:" text
            const invoiceText = document.querySelector('.bill-no').textContent;
            const invoiceNumber = invoiceText.split(':')[1]?.trim();
            // Configure html2pdf options
            const options = {
                margin:       10,
                filename: `invoice-${invoiceNumber}.pdf`,
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
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-product');

        searchInput.addEventListener('keydown', function (event) {
            
            if (event.code === 'Space') {
                // event.preventDefault(); 
                fetchProductBySearch();
            }
        });
    });
</script>



</body>

</html>
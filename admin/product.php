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
    <title>Mondol Motors - Product</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet">
    <link href="dist/css/style.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap-Select CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <?php include 'template_html/responsive.html'; ?>
    
    <style>
        @media print {
          /* hide everything except #print-area */
          body * {
            visibility: hidden !important;
          }
          #print-area, #print-area * {
            visibility: visible !important;
          }
          #print-area {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 10mm;            /* match your jsPDF margin */
          }
        
          /* force A4 page size & margins */
          @page {
            size: A4 portrait;
          }
        
          /* remove any scrolling wrappers */
          .table-responsive {
            overflow: visible !important;
          }
        
          /* hide all buttons (including your Delete buttons) */
          button, .btn {
            display: none !important;
          }
        
          /* hide the “Actions” column entirely */
          /* if it’s always the last column: */
          #print-area th:last-child,
          #print-area td:last-child {
            display: none !important;
          }
        
          /* ensure header repeats and rows don’t break in half */
          table {
            page-break-inside: auto;
            width: 100%;
            border-collapse: collapse;
          }
          thead {
            display: table-header-group;    /* repeat on each page */
          }
          tfoot {
            display: table-footer-group;
          }
          tr {
            page-break-inside: avoid;
            page-break-after: auto;
          }
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
                    <h4 class="text-primary">Products</h4>
                    <div class="d-flex">
                        <button class="btn btn-success mb-2 mr-2" onclick="showProductModal()">Add a product</button>
                        <button id="print-area-btn" class="btn btn-primary mb-2">Print</button>
                    </div>
                    
                </div>
                
                <div class="table-responsive" id="print-area">
                    <table class="table table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Bar code</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Sale Price</th>
                                <th>Purchase Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productList"></tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    
    


    
    

    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add a new product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <div class="col-md-12">
                            <div class="row">
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Product Name *</label>
                                    <input class="form-control name" type="text" name="name" value="" placeholder="Enter Product Name" required>
                                    <span id="name" class="text-danger d-none"></span>
                                </div>
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Barcode</label>
                                    <input class="form-control bar-code" type="text" name="bar-code" value="" placeholder="Enter Bar code">
                                    <span id="bar-code" class="text-danger d-none"></span>
                                </div>
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Category *</label>
                                    <div class="d-flex">
                                        <select class="form-control selectpicker" id="select-category" data-live-search="true" required>
                                        </select>
                                        <button type="button" class="btn btn-primary ms-2" onclick="showCategoryModal()">Add</button>
                                    </div>
                                </div>
                                
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Brand *</label>
                                    <div class="d-flex">
                                        <select class="form-control selectpicker" id="select-brand" data-live-search="true" required>
                                        </select>
                                        <button type="button" class="btn btn-primary ms-2" onclick="showBrandModal()">Add</button>
                                    </div>
                                </div>
    
                                
                                
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Sale price *</label>
                                    <input class="form-control sale-price" type="number" name="sale-price" value="0" placeholder="" required>
                                    <span id="sale-price" class="text-danger d-none"></span>
                                </div>
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Purchase price *</label>
                                    <input class="form-control purchase-price" type="number" name="purchase-price" value="0" placeholder="" required>
                                    <span id="purchase-price" class="text-danger d-none"></span>
                                </div>
                                <div id="input" class="mb-3 form-group">
                                    <label class="mb-2">Bulk price *</label>
                                    <input class="form-control bulk-price" type="number" name="bulk-price" value="0" placeholder="" required>
                                    <span id="bulk-price" class="text-danger d-none"></span>
                                </div>
                                <div id="input" class="mb-3 form-group">
                                    <label class="mb-2">Description</label>
                                    <textarea class="form-control description" type="text" name="description" placeholder=""></textarea>
                                    <span id="purchase-price" class="text-danger d-none"></span>
                                </div>
                                <div id="input" class="mb-3 form-group col-md-6">
                                    <label class="mb-2">Product Image</label>
                                    <input class="form-control" type="file" name="product-image" accept="image/*">
                                    <span id="product-image" class="text-danger d-none"></span>
                                </div>
                            </div>
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

    

   
    
    <script src="assets/api-calls/logout.js"></script>
    <script src="assets/api-calls/product.js"></script>
    <script src="assets/api-calls/category_and_brand.js"></script>
    
    <script>
      $(document).ready(function() {
        $('.selectpicker').selectpicker();
        getBrands();
        getCategories();
      });
    </script>
     <script>
        function showProductModal() {
            $('#productModal').modal('show');
            
            
        }
        
        function showCategoryModal() {
            $('#categoryModal').modal('show');
        }
        
        function showBrandModal() {
            $('#brandModal').modal('show');
        }
        
        

        

    </script>
    <script>
        document.getElementById('print-area-btn').addEventListener('click', () => {
          const element = document.getElementById('print-area');
        
          // filename with timestamp
          const ts = new Date().toISOString().slice(0,19).replace(/[:T]/g,'-');
          const filename = `product-list-${ts}.pdf`;
        
          const options = {
            margin:      4,               // mm
            filename:     filename,
            image:        { type: 'jpeg', quality: 2 },
            html2canvas:  { scale: 2, logging: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    {
              mode: ['css','legacy'],       // honor our CSS @page, thead, tr rules
              avoid: 'tr'
            },
            enableLinks:  true
          };
        
          html2pdf().set(options).from(element)
            .toPdf()
            .get('pdf')
            .then(pdf => {
              const total = pdf.internal.getNumberOfPages();
              for (let i = 1; i <= total; i++) {
                pdf.setPage(i)
                   .setFontSize(10)
                   .text(
                     `Page ${i} of ${total}`,
                     pdf.internal.pageSize.getWidth() - 30,
                     pdf.internal.pageSize.getHeight() - 10
                   );
              }
            })
            .save();
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
</body>

</html>
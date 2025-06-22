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
    <title>Mondol Motors - Expenses</title>
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

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-primary">expense</h4>
                    <button class="btn btn-success" onclick="showPopup()">Add expense</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Expense Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Option</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expenseList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
     <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expenseModalLabel">expense Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="expenseForm">
                        
                        <div class="mb-3">
                            <label for="expenseName" class="form-label" >Expense Name</label>
                            <input type="text" class="form-control" placeholder="Enter Expense name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label" >Amount</label>
                            <input type="number" class="form-control" placeholder="Enter amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-label">Date</label>
                            <input type="date" class="form-control" name="date" id="expenseDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-label">Pyament method</label>
                            <div class="d-flex">
                                <select class="form-select" name="option" id="option" required>
                                    <option value="">Choose payment method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Rocket">Rocket</option>
                                </select>
                                <button class="btn btn-primary" onclick="showSupplierModal()">Add</button>
                            </div>

                        </div>
                        <div class="mb-3">
                            <label class="form-label font-label">Note</label>
                            <textarea type="date" class="form-control" name="note" id="note"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="expenseForm" class="btn btn-primary">Submit</button>
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
      $(document).ready(function() {
        $('.selectpicker').selectpicker();
      });
    </script>

    <script>
    
    
        function showPopup() {
            $('#expenseModal').modal('show');
        }
        


        

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
    
    <script src="assets/api-calls/logout.js"></script>
    <script src="assets/api-calls/expense.js"></script>
</body>

</html>
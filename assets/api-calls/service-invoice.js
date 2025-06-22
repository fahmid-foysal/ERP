$(document).ready(function() {
        fetchServiceInvoices();
    });
function fetchServiceInvoices() {
        $.ajax({
            url: 'https://mondolmotors.com/api/get_all_service_invoice.php',  // API endpoint
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.status === 'success') {
                    populateTable(data.invoices);
                } else {
                    console.error('Error fetching service invoices');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching products:', error);
            }
        });
    }
    

    // Populate the table with product data
    function populateTable(serviceInvoices) {
        const serviceInvoiceList = $('#serviceInvoiceList');
        serviceInvoiceList.empty(); // Clear the table before populating

        serviceInvoices.forEach((serviceInvoice, index) => {
            const row = $('<tr>');

            
            row.html(`
                <td>${index + 1}</td>
                <td>SINV-00${serviceInvoice.id}</td>
                <td>${serviceInvoice.service}</td>
                <td>${serviceInvoice.total_amount}</td>
                <td>${serviceInvoice.total_paid}</td>
                <td>${serviceInvoice.date}</td>
                <td>${serviceInvoice.name}</td>
                <td>${serviceInvoice.phone}</td>
                <td>${serviceInvoice.total_due}</td>
                <td>${serviceInvoice.status}</td>
                <td>
                    <!-- Add action buttons here -->
                    <button class="btn btn-warning btn-sm">Edit</button>
                    <button class="btn btn-danger btn-sm" data-id="${serviceInvoice.id}" data-type="service_invoice" onclick="deleteItem(this)">Delete</button>
                </td>
            `);

            serviceInvoiceList.append(row); // Append the row to the table
        });
    }
    function deleteItem(button) {
        let id = $(button).data('id');
        let deletable = $(button).data('type');
    
        if(confirm("Are you sure you want to delete this?")) {
            $.ajax({
                url: 'https://mondolmotors.com/api/delete.php',
                type: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify({
                    deletable: deletable,
                    id: id
                }),
                success: function(response) {
                    if(response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Refresh to show updated list
                    } else {
                        alert("Failed to delete: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Something went wrong!");
                    console.log(error);
                }
            });
        }
    }
    
function getCustomerInfo() {
    let phone = $("#cus_phone").val().trim(); 

    if (phone.length >= 11) { 
        $.ajax({
            url: "https://mondolmotors.com/api/customer_data.php", 
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({ cus_phone: phone }),
            xhrFields: {
                withCredentials: true
            },
            success: function (response) {
                console.log(response);
                if (response.response_code === 1) {
                    $("#cus_name").val(response.customer_data.name);
                    $("#cus_address").val(response.customer_data.address);
                } else {
                    $("#cus_name").val("");
                    $("#cus_address").val("");
                }
            },
            error: function (xhr, status, error) {
                console.error("API call failed:", error);
            }
        });
    }
}


function generateServiceInvoice() {
    let customerName = $("#cus_name").val().trim();
    let customerPhone = $("#cus_phone").val().trim();
    let customerAddress = $("#cus_address").val().trim();
    let serviceName = $("#service_name").val().trim();
    let serviceAmount = parseFloat($("input[name='amount']").val().trim());
    let paidAmount = parseFloat($("input[name='paid']").val().trim());
    let date = $("#invoiceDate").val().trim();

    // Validations
    if (!customerName || !customerPhone) {
        alert("Customer Name and Phone are required.");
        return;
    }

    if (!serviceName) {
        alert("Service name is required.");
        return;
    }

    if (isNaN(serviceAmount) || serviceAmount <= 0) {
        alert("Please enter a valid service amount.");
        return;
    }

    if (isNaN(paidAmount) || paidAmount < 0) {
        alert("Please enter a valid paid amount.");
        return;
    }

    if (paidAmount > serviceAmount) {
        alert("Paid amount cannot be greater than service amount.");
        return;
    }

    if (!date) {
        alert("Please select a date.");
        return;
    }

    // Prepare the request data
    let requestData = {
        cus_name: customerName,
        cus_phone: customerPhone,
        cus_address: customerAddress,
        service_name: serviceName,
        amount: serviceAmount,
        paid: paidAmount,
        date: date
    };

    // Send the data via AJAX
    $.ajax({
        url: 'https://mondolmotors.com/api/service_invoice.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(requestData),
        success: function (response) {
            console.log(response);
            if (response.status === "success") {
                alert(response.message);
                // Optionally reset the form or update UI
                $("#serviceInvoiceForm")[0].reset();
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert("Something went wrong. Please try again.");
        }
    });
}



document.getElementById('serviceInvoiceForm').addEventListener('submit', generateServiceInvoice);
function getCustomerInfo() {
    let phone = $("#customerContact").val().trim(); 

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
                    $("#customerName").val(response.customer_data.name);
                    $("#address").val(response.customer_data.address);
                } else {
                    $("#customerName").val("");
                    $("#address").val("");
                }
            },
            error: function (xhr, status, error) {
                console.error("API call failed:", error);
            }
        });

    }
}
function checkSelection() {
    let category = $("#select-category").val() || ""; 
    let brand = $("#select-brand").val() || ""; 

    // Debugging logs
    if(brand ==="" && category ===""){
        alert("Choose category or brand to filter");
    }else{
        fetchProducts(category, brand);
        $("#productModal").modal("show");
    }
}

function addProductByBarCode() {
    let barcode = $("#barcode").val().trim();

    $.ajax({
        url: "https://mondolmotors.com/api/get_product_by_barcode.php?barcode=" + barcode,
        type: "GET",
        xhrFields: {
            withCredentials: true
        },
        dataType: "json",
        success: function(response) {
            console.log("API Response:", response); 

            let selectedProductsTable = document.getElementById("selected-products-table");

            if (response.status === "success" && response.product) {
                let product = response.product;
                console.log("Rendering product:", product); 

                let productName = product.name || 'No Name';
                let productPrice = parseFloat(product.sale_price).toFixed(2);
                let productPurchasePrice = parseFloat(product.purchase_price).toFixed(2);
                let productId = product.barcode;
                let productBrand = checkbox.getAttribute("data-brand");
                // ✅ Check if the product is already in the table
                if (document.querySelector(`tr[data-product-id="${productId}"]`)) {
                    alert(`Product with ID ${productId} is already added to the invoice.`);
                    return;
                }

                let row = `
                    <tr data-product-id="${productId}">
                        <td>${productName}</td>
                        <td class="product-brand">${productBrand}</td>
                        <td><input type="number" class="form-control price-input" value="${productPrice}" min="0" 
                            oninput="updateTotal(this)"></td>
                        <td>${productPurchasePrice}</td>
                        <td><input type="number" class="form-control quantity-input" value="1" min="1" 
                            oninput="updateTotal(this)"></td>
                        <td class="product-total">${productPrice}</td>
                        <td><button class="btn btn-danger btn-sm" onclick="removeProduct('${productId}')">Remove</button></td>
                    </tr>
                `;

                selectedProductsTable.insertAdjacentHTML("beforeend", row); // Append row properly

                updateInvoiceTotal();
                $("#productModal").modal("hide");
                $("#invoiceModal").modal("show");
            } else {
                console.warn("No product found with the given barcode.");
                alert("Product not found!");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching product:", xhr.responseText);
            alert("Failed to load product.");
        }
    });
}






        
function fetchProducts(category, brand) {
    console.log("Fetching products with parameters:", { category_id: category, brand_id: brand });
    if (category === "") {
        $('#search-form').addClass('d-none');
    } else {
        $('#search-form').removeClass('d-none');
    }

    $.ajax({
        url: "https://mondolmotors.com/api/filtered_products.php",
        type: "POST",
        contentType: "application/json", 
        data: JSON.stringify({ 
            category_id: category, 
            brand_id: brand 
        }),
        xhrFields: {
            withCredentials: true
        },
        dataType: "json",
        success: function(response) {
            console.log("API Response:", response); 

            let container = $("#productContainer");
            container.empty(); 

            if (response.status === "success" && response.product.length > 0) {
                const isBulk = $('#select-bulk').is(':checked');  // Check if bulk is selected

                response.product.forEach(product => {
                    console.log("Rendering product:", product); 

                    let productImage = product.img_path ? product.img_path : "default.jpg";

                    let selectedPrice = isBulk && product.bulk_rate ? product.bulk_rate : product.sale_price;
                    let purchasePrice = product.purchase_price

                    let productCard = `
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card product-card">
                                <img src="${productImage}" 
                                    class="card-img-top" style="height: 125px;" alt="${product.name || 'No Image Available'}">
                                <div class="card-body text-center">
                                    <h6>${product.name}</h6>
                                    <b>Code: </b>${product.barcode}
                                    <b>Brand: </b> ${product.brand}
                                    
                                    <p>
                                    Price: ${selectedPrice} Tk</p>
                                    <p>Stock: ${product.in_stock}</p>
                                    <input type="checkbox" class="product-checkbox" value="${product.barcode}" 
                                        data-name="${product.name}" data-purchase-price="${product.purchase_price}" data-price="${selectedPrice}" data-brand="${product.brand}" ${product.in_stock <= 0 ? 'disabled' : ''}>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(productCard);
                });
            } else {
                console.warn("No products found for given filters."); 
                container.html("<p class='text-center'>No products found.</p>");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching products:", xhr.responseText); 
            $("#productContainer").html("<p class='text-center text-danger'>Failed to load products.</p>");
        }
    });
}
function fetchProductBySearch() {
    let category = $("#select-category").val() || ""; 
    let search = $("#search-product").val() || "";
    console.log("Searching products by name:", search, "in category:", category);

    if (search.trim() === "") {
        console.warn("Search term is empty.");
        alert("Give an input");
        return;
    }

    $.ajax({
        url: "https://mondolmotors.com/api/search_by_name.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            category_id: category,
            product_name: search
        }),
        xhrFields: {
            withCredentials: true
        },
        dataType: "json",
        success: function(response) {
            console.log("Search API Response:", response);

            let container = $("#productContainer");
            container.empty();

            if (response.status === "success" && response.products.length > 0) {
                const isBulk = $('#select-bulk').is(':checked');
                response.products.forEach(product => {
                    
                    console.log("Rendering searched product:", product);

                    let productImage = product.img_path ? product.img_path : "default.jpg"; 

                    let selectedPrice = isBulk && product.bulk_rate ? product.bulk_rate : product.sale_price;

                    let productCard = `
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card product-card">
                                <img src="${productImage}" 
                                    class="card-img-top" style="height: 125px;" alt="${product.name || 'No Image Available'}">
                                <div class="card-body text-center">
                                    <h6>${product.name}</h6>
                                    <b>Brand: </b> ${product.brand}
                                    
                                    <p>
                                    Price: ${selectedPrice} Tk</p>
                                    <p>Stock: ${product.in_stock}</p>
                                    <input type="checkbox" class="product-checkbox" value="${product.barcode}" 
                                        data-name="${product.name}" data-purchase-price="${product.purchase_price}" data-price="${selectedPrice}" data-brand="${product.brand}" ${product.in_stock <=0? 'disabled': ''}>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(productCard);
                });
            } else {
                console.warn("No products matched the search query.");
                container.html("<p class='text-center'>No products found for the search term.</p>");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error searching products:", xhr.responseText);
            $("#productContainer").html("<p class='text-center text-danger'>Failed to search products.</p>");
        }
    });
}





        
function addSelectedProducts() {
    let selectedProductsTable = document.getElementById("selected-products-table");

    document.querySelectorAll(".product-checkbox:checked").forEach(checkbox => {
        let productId = checkbox.value;

        
        if (document.querySelector(`tr[data-product-id="${productId}"]`)) {
            alert(`Product with ID ${productId} is already added to the invoice.`);
            return;
        }

        let productName = checkbox.getAttribute("data-name");
        let productPrice = parseFloat(checkbox.getAttribute("data-price")).toFixed(2);
        let productPurchasePrice = parseFloat(checkbox.getAttribute("data-purchase-price")).toFixed(2);
        let productBrand = checkbox.getAttribute("data-brand");

        let row = `
            <tr data-product-id="${productId}">
                <td class="product-name">${productName}</td>
                <td class="product-brand">${productBrand}</td>
                <td><input type="number" class="form-control price-input" value="${productPrice}" min="0" 
                    oninput="updateTotal(this)"></td>
                <td>${productPurchasePrice}</td>
                <td><input type="number" class="form-control quantity-input" value="1" min="1" 
                    oninput="updateTotal(this)"></td>
                <td class="product-total">${productPrice}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeProduct('${productId}')">Remove</button></td>
            </tr>
        `;

        selectedProductsTable.insertAdjacentHTML("beforeend", row);
    });

    updateInvoiceTotal();
    $("#productModal").modal("hide"); // Close product modal
    $("#invoiceModal").modal("show"); // Show invoice modal
}


        
// Update total price for each row and overall invoice total
function updateTotal(input) {
  const row = input.closest('tr');
  // grab your two inputs by class
  const priceInput    = row.querySelector('.price-input');
  const quantityInput = row.querySelector('.quantity-input');
  // parse them (falling back to zero or one)
  const price = parseFloat(priceInput.value)    || 0;
  let   qty   = parseInt(quantityInput.value, 10) || 0;

  // enforce a minimum of 1
  if (qty < 1) {
    qty = 1;
    quantityInput.value = 1;
  }

  // compute and write the row total
  const total = (price * qty).toFixed(2);
  row.querySelector('.product-total').textContent = total;

  // now update the overall invoice
  updateInvoiceTotal();
}


function updateInvoiceTotal() {
    let total = 0;
    let hasItems = false;
    
    // Product totals
    const productRows = document.querySelectorAll("#selected-products-table tr");
    productRows.forEach(row => {
        const totalCell = row.querySelector(".product-total");
        total += parseFloat(totalCell.textContent) || 0;
    });
    
    // Service totals
    const serviceRows = document.querySelectorAll("#services-table tr");
    serviceRows.forEach(row => {
        const priceInput = row.querySelector(".service-price");
        total += parseFloat(priceInput.value) || 0;
    });

    // Check if we have any items
    hasItems = productRows.length > 0 || serviceRows.length > 0;
    
    // Enable/disable discount based on items
    const discountField = document.getElementById("discount");
    discountField.disabled = !hasItems;
    
    // If no items, reset discount to 0
    if (!hasItems) {
        discountField.value = "0";
    }

    // Apply discount
    const discountPercentage = parseFloat(discountField.value) || 0;
    const discountAmount = (total * discountPercentage) / 100;
    const finalTotal = total - discountAmount;

    document.getElementById("invoice-total").textContent = finalTotal.toFixed(2);
    return finalTotal;
}


document.getElementById("discount").addEventListener("input", updateInvoiceTotal);
        
function removeProduct(productId) {
    document.querySelector(`tr[data-product-id="${productId}"]`).remove();
    updateInvoiceTotal();
}

function showCheckoutModal() {
    // Check if at least one product or service is selected
    const hasProducts = document.querySelectorAll("#selected-products-table tr").length > 0;
    const hasServices = document.querySelectorAll("#services-table tr").length > 0;
    
    if (!hasProducts && !hasServices) {
        alert("Please add at least one product or service before checkout.");
        return;
    }

    // Get customer information
    let customerNameInput = document.getElementById("customerName");
    let customerContactInput = document.getElementById("customerContact"); // Fixed from customerName to customerContact
    let addressInput = document.getElementById("address");
    
    if(customerNameInput.value.trim() === "" || customerContactInput.value.trim() === "") {
        alert("Please provide all customer information");
        return;
    }
    
    // Set customer name in checkout modal
    document.getElementById("customer_name").textContent = customerNameInput.value.trim();

    // Calculate totals
    let subtotal = 0;
    
    // Product totals
    document.querySelectorAll(".product-total").forEach(cell => {
        subtotal += parseFloat(cell.textContent) || 0;
    });
    
    // Service totals
    document.querySelectorAll(".service-price").forEach(input => {
        subtotal += parseFloat(input.value) || 0;
    });

    // Calculate discount and payable amount
    let discountField = document.getElementById("discount");
    let discountPercentage = parseFloat(discountField.value) || 0;
    let discountAmount = (subtotal * discountPercentage) / 100;
    let payableAmount = subtotal - discountAmount;

    // Update UI with calculated values
    document.querySelector(".sub_total").textContent = subtotal.toFixed(2);
    document.querySelector(".discount_amount").textContent = discountAmount.toFixed(2);
    
    // Count both products and services for total items
    const productCount = document.querySelectorAll("#selected-products-table tr").length;
    const serviceCount = document.querySelectorAll("#services-table tr").length;
    document.querySelector(".total_item").textContent = productCount + serviceCount;
    
    document.querySelector(".payable_amount").textContent = payableAmount.toFixed(2);
    document.querySelector(".paid_amount").textContent = "0.00";
    document.querySelector(".due_amount").textContent = payableAmount.toFixed(2);
    document.querySelector(".balance").textContent = payableAmount.toFixed(2);

    // Reset payment input
    const payInput = document.getElementById("pay_amount");
    payInput.value = "";

    // Handle payment amount changes
    payInput.oninput = function() {
        let payAmount = parseFloat(this.value) || 0;
        
        if (isNaN(payAmount) || payAmount < 0 || payAmount > payableAmount) {
            alert("Invalid payment amount! Please enter a valid amount.");
            this.value = "";
            document.querySelector(".paid_amount").textContent = "0.00";
            document.querySelector(".due_amount").textContent = payableAmount.toFixed(2);
            return;
        }
        
        document.querySelector(".paid_amount").textContent = payAmount.toFixed(2);
        let due = payableAmount - payAmount;
        document.querySelector(".due_amount").textContent = (due >= 0 ? due : 0).toFixed(2);
    }

    // Full due button handler
    document.getElementById("full_due_btn").onclick = function() {
        payInput.value = "0.00";
        document.querySelector(".paid_amount").textContent = "0.00";
        document.querySelector(".due_amount").textContent = payableAmount.toFixed(2);
    }

    // Full payment button handler
    document.getElementById("full_payment_btn").onclick = function() {
        payInput.value = payableAmount.toFixed(2);
        document.querySelector(".paid_amount").textContent = payableAmount.toFixed(2);
        document.querySelector(".due_amount").textContent = "0.00";
    }

    // Show checkout modal
    $("#invoiceModal").modal("hide");
    $("#checkoutModal").modal("show");
}


function addServiceRow() {
    const servicesTable = document.getElementById("services-table");
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input type="text" class="form-control service-name" placeholder="Service Name" required></td>
        <td><input type="number" class="form-control service-price" min="0" value="0" required oninput="updateInvoiceTotal()"></td>
        <td><button class="btn btn-danger btn-sm" onclick="removeServiceRow(this)">Remove</button></td>
    `;
    servicesTable.appendChild(newRow);
    updateInvoiceTotal();
}

function removeServiceRow(button) {
    button.closest('tr').remove();
    updateInvoiceTotal();
}

function generateInvoice() {
    const customerName = $("#customerName").val().trim();
    const customerContact = $("#customerContact").val().trim();
    const address = $("#address").val().trim();
    const payAmount = parseFloat($("#pay_amount").val().trim()) || 0;
    const discount = parseFloat($("#discount").val().trim()) || 0;
    const date = new Date().toISOString().split('T')[0];

    // Collect items
    const items = [];
    document.querySelectorAll("#selected-products-table tr").forEach(row => {
        const productId = row.getAttribute("data-product-id");
        const quantity = parseFloat(row.querySelector(".quantity-input").value) || 0;
        const rate = parseFloat(row.querySelector(".price-input").value) || 0;

        if (productId && quantity > 0 && rate > 0) {
            items.push({
                barcode: productId,
                quantity: quantity,
                rate: rate
            });
        }
    });

    // Collect services
    const services = [];
    document.querySelectorAll("#services-table tr").forEach(row => {
        const serviceName = row.querySelector(".service-name").value.trim();
        const servicePrice = parseFloat(row.querySelector(".service-price").value) || 0;

        if (serviceName && servicePrice > 0) {
            services.push({
                service_name: serviceName,
                service_price: servicePrice
            });
        }
    });

    // Validation
    if (!customerName || !customerContact) {
        alert("Customer Name and Phone are required.");
        return;
    }

    if (items.length === 0 && services.length === 0) {
        alert("Please add at least one product or service.");
        return;
    }

    if (isNaN(payAmount) || payAmount < 0) {
        alert("Please enter a valid payment amount.");
        return;
    }

    // Prepare request data
    const requestData = {
        cus_name: customerName,
        cus_phone: customerContact,
        cus_address: address,
        paid: payAmount,
        discount: discount,
        date: date,
        items: items,
        services: services
    };

    // Remove empty arrays
    if (requestData.items.length === 0) delete requestData.items;
    if (requestData.services.length === 0) delete requestData.services;

    // Submit to API
    $.ajax({
        url: 'https://mondolmotors.com/api/create_invoice.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(requestData),
        success: function(response) {
            if (response.status === "success") {
                alert(response.message);
                window.location.href = 'https://mondolmotors.com/admin/invoice.php';
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            alert("Error: " + (xhr.responseJSON?.message || "Something went wrong"));
        }
    });
}





// function fetchInvoices() {
//     $.ajax({
//         url: 'https://mondolmotors.com/api/get_all_invoice.php',  // API endpoint
//         method: 'GET',
//         dataType: 'json',
//         xhrFields: {
//             withCredentials: true
//         },
//         success: function(response) {
//             console.log(response);
//             if (response.status === 'success') {
//                 populateTable(response.invoices);
//             } else {
//                 console.error('Error fetching invoices');
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error('Error fetching invoices:', error);
//         }
//     });
// }

// function populateTable(invoices) {
//     const invoiceList = $('#invoiceList');
//     invoiceList.empty(); // Clear any existing rows

//     // Populate the table with new rows
//     invoices.forEach((invoice, index) => {
//         const row = $('<tr>');
//         row.html(`
//             <td>${index + 1}</td>
//             <td>${invoice.date}</td>
//             <td>INV-00${invoice.invoice_id}</td>
//             <td>${invoice.cus_name}</td>
//             <td>${invoice.cus_phone}</td>
//             <td>${invoice.total_amount}</td>
//             <td>${invoice.paid}</td>
//             <td>${invoice.due}</td>
//             <td>
//                 <button class="btn btn-warning btn-sm">Edit</button>
//                 <button class="btn btn-danger btn-sm">Delete</button>
//             </td>
//         `);
//         invoiceList.append(row);
//     });
// }

// // Call fetchInvoices() to load data when needed, e.g., on page load or button click
$(document).ready(function() {
    // Initialize DataTable
    $('#myTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'https://mondolmotors.com/api/get_all_invoice.php',
            type: 'GET',
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            dataSrc: function(response) {
                if (response.status === 'success') {
                    return response.invoices;
                }
                return [];
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'date' },
            { 
                data: 'invoice_id',
                render: function(data) {
                    return `INV-00${data}`;
                }
            },
            { data: 'cus_name' },
            { data: 'cus_phone' },
            { data: 'total_amount' },
            { data: 'paid' },
            { data: 'due' },
            {
                data: null,
                render: function(data, type, row, meta) {
                    return `
                        <button class="btn btn-warning btn-sm" onclick="updateInvoice(${row.invoice_id}, ${row.due})">Update</button>
                        <button class="btn btn-danger btn-sm" data-id="${row.invoice_id}" data-type="invoices" onclick="deleteItem(this)">Delete</button>
                    `;
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data, type, row, meta) {
                    return `
                        <button class="btn btn-secondary btn-sm" data-id="${row.invoice_id}" onclick="showInvoice(${row.invoice_id})">Show</button>
                    `;
                },
                orderable: false,
                searchable: false
            }


        ],
        columnDefs: [
            { className: "text-center", targets: "_all" }
        ]
    });
});

function showInvoice(invoice_id) {
    $.ajax({
        url: 'https://mondolmotors.com/api/get_invoice_by_id.php?id=' + invoice_id,
        type: 'GET',
        data: { invoice_id: invoice_id },
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                console.log(response);
                
                let invoice = response.invoice;
                let items = response.invoice.items;
                let services = response.invoice.services;

                // Invoice Basic Info
                $('#showInvoiceModal .bill-no').text('Invoice : INV-00' + invoice.invoice_id);
                $('#showInvoiceModal .date strong').text(invoice.date);
                $('#showInvoiceModal .name span').text(invoice.cus_name);
                $('#showInvoiceModal .phone span').text(invoice.cus_phone);


                // Table Body Reset
                let rows = '';
                items.forEach((item, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.rate} TK</td>
                            <td>${item.quantity*item.rate} TK</td>
                        </tr>
                    `;
                });
                
                if(services.length >0){
                    rows +=`
                        <td colspan ="5" class="text-center"><b>Services</b></td>
                    `;
                };
                
                services.forEach((service, index)=>{
                    rows +=`
                        <tr>
                            <td>${index + 1}</td>
                            <td colspan="3">${service.service_name}</td>
                            <td>${service.service_price} TK</td>
                        </tr>
                    `;
                });

                // Totals Row
                rows += `
                
                    <tr>
                        <td colspan="3" rowspan="5">
                            
                        </td>
                        <td class="text-end">Discount:</td>
                        <td>${invoice.discount}%</td>
                    </tr>
                    <tr>
                        
                        <td class="text-end">Sub Total:</td>
                        <td>${invoice.total_amount} TK</td>
                    </tr>
                    
                    <tr>
                        
                        <td class="text-end">Paid:</td>
                        <td>${invoice.paid} TK</td>
                    </tr>
                    <tr>
                        
                        <td class="text-end">Due:</td>
                        <td>${invoice.due} TK</td>
                    </tr>
                    <tr>
                        
                        <td class="text-end"><strong>Grand Total:</strong></td>
                        <td><strong>${invoice.total_amount} TK</strong></td>
                    </tr>
                `;

                $('#showInvoiceModal tbody').html(rows);

                // Show Modal
                $('#showInvoiceModal').modal('show');
            } else {
                alert('Invoice not found!');
            }
        },
        error: function() {
            alert('Something went wrong!');
        }
    });
}





function deleteItem(button) {
    let id = $(button).data('id');
    let deletable = $(button).data('type');
    const correctPassword = "M0nd0lHafeez"; // Change this to your desired password

    const userPassword = prompt("Enter password to delete:");

    if (userPassword === null) {
        // User canceled the prompt
        return;
    }

    if (userPassword !== correctPassword) {
        alert("Incorrect password. Deletion cancelled.");
        return;
    }

    if (confirm("Are you sure you want to delete this?")) {
        $.ajax({
            url: 'https://mondolmotors.com/api/delete.php',
            type: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({
                deletable: deletable,
                id: id
            }),
            success: function(response) {
                if (response.status === 'success') {
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


function updateInvoice(invoice_id, due) {
    let amount = prompt("Enter Amount:", due);

    if (amount === null || amount.trim() === '') {
        alert("Amount is required.");
        return;
    }

    amount = parseFloat(amount);

    if (isNaN(amount) || amount <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    if (amount > due) {
        alert("Amount can't be greater than due amount (" + due + ")");
        return;
    }

    $.ajax({
        url: 'https://mondolmotors.com/api/update_invoice.php',
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify({
            invoice_id: invoice_id,
            amount: amount
        }),
        
        success: function(response) {
            if (response.status === 'success') {
                alert(response.message);
                $('#myTable').DataTable().ajax.reload();
            } else {
                alert("Failed to update invoice: " + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert("Something went wrong!");
        }
    });
}



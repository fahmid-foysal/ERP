$(document).ready(function() {
    
    fetchPurchases();
    $('.selectpicker').selectpicker();
    getSuppliers();
    
});
function fetchPurchases() {
    $.ajax({
        url: 'https://mondolmotors.com/api/get_all_purchase.php',  // API endpoint
        method: 'GET',
        dataType: 'json',
        
        success: function(data) {
            console.log(data);
            if (data.status === 'success') {
                populateTable(data.purchases);
            } else {
                console.error('Error fetching purchases');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching purchases:', error);
        }
    });
}
function addPurchase(event) {
    event.preventDefault();

    const date = document.querySelector('input[name="date"]').value.trim();
    const supplier_id = document.getElementById('select-supplier').value;
    const paid_amount = document.querySelector('input[name="paid_amount"]').value.trim();

    // Collect all selected products
    const productRows = document.querySelectorAll("#selected-products-table tr");
    const products = [];

    productRows.forEach(row => {
        const barcode = row.getAttribute("data-product-id");
        const quantity = parseInt(row.querySelector(".quantity-input").value.trim()) || 0;
        const rate = parseFloat(row.querySelector(".price-input").value.trim()) || 0;

        if (barcode && quantity > 0 && rate > 0) {
            products.push({ barcode, quantity, rate });
        }
    });

    if (!date || !supplier_id || !paid_amount || products.length === 0) {
        alert("Please fill all required fields and select at least one product.");
        return;
    }

    const data = {
        supplier_id: parseInt(supplier_id),
        paid: parseFloat(paid_amount),
        date: date,
        products: products
    };

    $.ajax({
        url: "https://mondolmotors.com/api/add_purchase.php",
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        xhrFields: { withCredentials: true },

        success: function(response) {
            console.log('Response:', response);
            try {
                response = typeof response === 'string' ? JSON.parse(response) : response;
            } catch (e) {
                alert('Invalid response from server.');
                return;
            }

            if (response.status === 'success') {
                alert(response.message);
                document.getElementById('purchaseForm').reset();
                document.getElementById("selected-products-table").innerHTML = ""; // clear selected table
                $(".purchase-total").val("0.00");
                $("#purchaseModal").modal("hide");
                fetchPurchases();
            } else {
                alert('Error: ' + response.message);
            }
        },

        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        }
    });
}


document.getElementById('purchaseForm').addEventListener('submit', addPurchase);





function getSuppliers() {
    $.ajax({
        url: "https://mondolmotors.com/api/get_all_supplier.php",
        method: "GET",
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (response.status === "success") {
                
                populateSupplierDropdown(response.suppliers); // Rebuild dropdown
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching Suppliers:", error);
        }
    });
}


function populateSupplierDropdown(suppliers) {
    const $selectSupplier = $("#select-supplier");
    
    $selectSupplier.selectpicker('destroy');
    
    $selectSupplier.empty();
    
    $selectSupplier.append('<option value="">Choose a Supplier</option>');
    
    suppliers.forEach((supplier) => {
        $selectSupplier.append(`<option value="${supplier.id}">${supplier.name}</option>`);
    });
    
    $selectSupplier.selectpicker();
}
    
function addSupplier(event) {
        event.preventDefault(); // Prevent the default form submission
    
        // Collect the form data
        const name = document.querySelector('input[name="name"]').value;
        const phone = document.querySelector('input[name="phone"]').value;
        const address = document.querySelector('input[name="address"]').value;
        
    
        // Prepare FormData
        
         let requestData = {
            name: name,
            phone: phone,
            address: address,
        };
    
        // AJAX request using jQuery
        $.ajax({
            url: "https://mondolmotors.com/api/add_supplier.php",
            type: 'POST',
            data: JSON.stringify(requestData),
            processData: false, 
            contentType: false,
            xhrFields: {
                        withCredentials: true 
                    },
            success: function(response) {
                console.log('Response:', response); 
                if (response.status === 'success') {
                    alert('Supplier added successfully');
                    document.getElementById('supplierForm').reset();
                    $('#supplierModal').modal('hide');
                    getSuppliers();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            }
        });
    }
document.getElementById('supplierForm').addEventListener('submit', addSupplier);



function populateTable(purchases) {
    const purchaseList = $('#purchaseList');
    purchaseList.empty(); // Clear the table before populating
        purchases.forEach((purchase, index) => {
        const row = $('<tr>');

        row.html(`
            <td>${index + 1}</td>
            <td>PUR-00${purchase.id}</td>
            <td>${purchase.date}</td>
            <td>${purchase.total_amount}</td>
            <td>${purchase.total_paid}</td>
            <td>${purchase.total_due}</td>
            <td>${purchase.name}</td>
            <td><span class="badge ${purchase.status.toLowerCase() === 'due' ? 'bg-danger' : 'bg-success'}">${purchase.status}</span></td>
            <td>
                <button class="btn btn-warning btn-sm edit-btn" data-id="${purchase.id}" data-paid="${purchase.total_due}">Edit</button>
                <button class="btn btn-danger btn-sm" data-id="${purchase.id}" data-type="purchase" onclick="deleteItem(this)">Delete</button>
            </td>
            <td>
                <button class="btn btn-warning btn-sm show-btn" data-id="${purchase.id}" onclick="showPurchase(${purchase.id})">Show</button>
            </td>

        `);
            purchaseList.append(row); // Append the row to the table
    });
}
function showPurchase(purchaseId) {
    $.ajax({
        url: 'https://mondolmotors.com/api/show_purchase.php?id=' + purchaseId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (response.status === 'success') {
                let purchase = response.purchase_details;

                // Set basic info
                $('#showPurchaseModal .bill-no').text('Purchase ID: ' + purchaseId);
                $('#showPurchaseModal .date strong').text(purchase.date);
                $('#showPurchaseModal .name span').text(purchase.supplier_name);
                $('#showPurchaseModal .phone span').text(purchase.supplier_phone);

                let rows = '';
                let index = 1;

                purchase.products.forEach(product => {
                    const total = parseFloat(product.quantity) * parseFloat(product.rate);

                    rows += `
                        <tr>
                            <td>${index++}</td>
                            <td>${product.barcode}</td>
                            <td>${product.product_name}</td>
                            <td>${product.quantity}</td>
                            <td>${product.rate} TK</td>
                            <td>${total.toFixed(2)} TK</td>
                        </tr>
                    `;
                });

                // Add total summary rows
                rows += `
                    <tr>
                        <td colspan="4" class="text-end"><strong>Paid:</strong></td>
                        <td>${parseFloat(purchase.total_paid).toFixed(2)} TK</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Due:</strong></td>
                        <td>${parseFloat(purchase.total_due).toFixed(2)} TK</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                        <td><strong>${parseFloat(purchase.total_amount).toFixed(2)} TK</strong></td>
                    </tr>
                `;

                // Inject into modal
                $('#showPurchaseModal tbody').html(rows);

                // Show modal
                $('#showPurchaseModal').modal('show');
            } else {
                alert('Purchase not found!');
            }
        },
        error: function() {
            alert('Something went wrong!');
        }
    });
}



function updatePurchase(purchaseId, amount) {
    $.ajax({
        url: 'https://mondolmotors.com/api/update_purchase.php',
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify({
            purchase_id: purchaseId,
            amount: amount
        }),
        success: function(response) {
            if (response.status === 'success') {
                alert(response.message);
                // Reload purchase list after successful update
                fetchPurchases(); // Assuming you have a function to reload table
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the purchase.');
        }
    });
}
function deleteItem(button) {
    let id = $(button).data('id');
    let deletable = $(button).data('type');
    const correctPassword = "M0nd0lHafeez";

    const userPassword = prompt("Enter password to delete:");

    if (userPassword === null) {
        // User clicked "Cancel"
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
                console.log("Rendering product:", product); // Debugging log
                

                let productName = product.name || 'No Name';
                let productPurchasePrice = parseFloat(product.purchase_price).toFixed(2);
                let productId = product.barcode;
                let productBrand = checkbox.getAttribute("data-brand");
                if (document.querySelector(`tr[data-product-id="${productId}"]`)) {
                    alert('Already added');
                    return;
                }
                

                selectedProductsTable.insertAdjacentHTML("beforeend", `
                    <tr data-product-id="${productId}">
                        <td class="product-name">${productName}</td>
                        <td class="product-brand">${productBrand}</td>
                        <td><input type="number" class="form-control price-input" value="${productPurchasePrice}" min="0" 
                            oninput="updateTotal(this)"></td>
                        <td><input type="number" class="form-control quantity-input" value="1" min="1" 
                            oninput="updateTotal(this)"></td>
                        <td class="product-total">${productPurchasePrice}</td>
                        <td><button class="btn btn-danger btn-sm" onclick="removeProduct('${productId}')">Remove</button></td>
                    </tr>
                `);

                updatePurchaseTotal();
                $("#productModal").modal("hide"); // Close product modal
                $("#purchaseModal").modal("show"); // Show invoice modal
            } else {
                console.warn("No product found with the given barcode."); // Debugging log
                alert("Product not found!");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching product:", xhr.responseText); // Debugging log
            alert("Failed to load product.");
        }
    });
}
$(document).on('click', '.edit-btn', function() {
    const purchaseId = $(this).data('id');
    const currentPaid = parseFloat($(this).data('paid'));  // Ensure it's a number

    const newAmount = prompt('Enter amount:', currentPaid);

    if (newAmount !== null) {
        if (isNaN(newAmount)) {
            alert('Invalid input! Please enter a valid number.');
            return;
        }

        if (parseFloat(newAmount) > currentPaid) {
            alert('Invalid Amount! New paid amount cannot be greater than current due amount.');
            return;
        }

        updatePurchase(purchaseId, parseFloat(newAmount));
    }
});

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
                

                response.product.forEach(product => {
                    console.log("Rendering product:", product); 

                    let productImage = product.img_path ? product.img_path : "default.jpg";

                    let selectedPrice = product.sale_price;
                    let purchasePrice = product.purchase_price

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
                                        data-name="${product.name}" data-purchase-price="${product.purchase_price}" data-price="${selectedPrice}" data-brand="${product.brand}"}>
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
                

                response.products.forEach(product => {
                    console.log("Rendering searched product:", product);

                    let productImage = product.img_path ? product.img_path : "default.jpg"; // optional handling if `img_path` exists

                    let selectedPrice = product.sale_price;

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
                                        data-name="${product.name}" data-purchase-price="${product.purchase_price}" data-price="${selectedPrice}" data-brand="${product.brand}">
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

        // 🔁 Skip if product is already added
        if (document.querySelector(`tr[data-product-id="${productId}"]`)) {
            alert('Already added');
            return;
        }

        let productName = checkbox.getAttribute("data-name");
        let productPrice = parseFloat(checkbox.getAttribute("data-price")).toFixed(2);
        let productPurchasePrice = parseFloat(checkbox.getAttribute("data-purchase-price")).toFixed(2);
        let productBrand = checkbox.getAttribute("data-brand");

        selectedProductsTable.insertAdjacentHTML("beforeend", `
            <tr data-product-id="${productId}">
                <td class="product-name">${productName}</td>
                <td class="product-brand">${productBrand}</td>
                <td><input type="number" class="form-control price-input" value="${productPurchasePrice}" min="0" 
                    oninput="updateTotal(this)"></td>
                <td><input type="number" class="form-control quantity-input" value="1" min="1" 
                    oninput="updateTotal(this)"></td>
                <td class="product-total">${productPurchasePrice}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeProduct('${productId}')">Remove</button></td>
            </tr>
        `);
    });

    updatePurchaseTotal();
    $("#productModal").modal("hide");
    $("#purchaseModal").modal("show");
}

function removeProduct(productId) {
    document.querySelector(`tr[data-product-id="${productId}"]`).remove();
    updatePurchaseTotal();
}
function updateTotal(input) {
    const row = input.closest('tr');

    // Get inputs by class instead of relying on column index
    const priceInput = row.querySelector('.price-input');
    const quantityInput = row.querySelector('.quantity-input');

    // Parse values safely
    const price = parseFloat(priceInput.value) || 0;
    let quantity = parseInt(quantityInput.value, 10) || 0;

    // Handle empty or invalid quantity
    if (quantity < 1) {
        quantity = 1;
        quantityInput.value = 1;
    }

    // Calculate total and update the cell
    const total = (price * quantity).toFixed(2);
    row.querySelector('.product-total').textContent = total;

    // Call your total updater
    updatePurchaseTotal();
}


function updatePurchaseTotal() {
    let total = 0;

    const productRows = document.querySelectorAll("#selected-products-table tr");
    productRows.forEach(row => {
        const totalCell = row.querySelector(".product-total");
        total += parseFloat(totalCell.textContent) || 0;
    });

    // Update input field value (not textContent)
    document.querySelector(".purchase-total").value = total.toFixed(2);

    return total;
}


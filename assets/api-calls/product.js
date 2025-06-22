    $(document).ready(function() {
        fetchProducts();
    });

    function addProduct(event) {
        event.preventDefault(); // Prevent the default form submission
    
        // Collect the form data
        const name = document.querySelector('input[name="name"]').value;
        const barcode = document.querySelector('input[name="bar-code"]').value;
        const category_id = document.getElementById('select-category').value;
        const brand_id = document.getElementById('select-brand').value;
        const unit = 'Piece'
        const sale_price = document.querySelector('input[name="sale-price"]').value;
        const purchase_price = document.querySelector('input[name="purchase-price"]').value;
        const bulk_price = document.querySelector('input[name="bulk-price"]').value;
        const description = document.querySelector('textarea[name="description"]').value;
        const img_input = document.querySelector('input[name="product-image"]');
        const img_path = img_input.files.length > 0 ? img_input.files[0] : null;
    
        // Prepare FormData
        let formData = new FormData();
        formData.append('name', name);
        formData.append('barcode', barcode);
        formData.append('category_id', category_id);
        formData.append('brand_id', brand_id);
        formData.append('unit', unit);
        formData.append('sale_price', sale_price);
        formData.append('purchase_price', purchase_price);
        formData.append('bulk_rate', bulk_price);
        formData.append('description', description);
    
        // Append the product image if there's one
        if (img_path) {
            formData.append('img_path', img_path);
        }
    
        // AJAX request using jQuery
        $.ajax({
            url: "https://mondolmotors.com/api/add_product.php",
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from converting the data into a query string
            contentType: false, // Allow the request to send data as FormData
            xhrFields: {
                        withCredentials: true // Ensure session cookies are sent
                    },
            success: function(response) {
                console.log('Response:', response); // Log the response to see its structure
                if (response.status === 'success') {
                    alert('Product added successfully');
                    document.getElementById('productForm').reset();
                    $('#myModal').modal('hide');
                    fetchProducts();
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

    document.getElementById('productForm').addEventListener('submit', addProduct);

    
    // Fetch products using AJAX
    function fetchProducts() {
        $.ajax({
            url: 'https://mondolmotors.com/api/get_all_product.php',  // API endpoint
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.status === 'success') {
                    populateTable(data.products);
                } else {
                    console.error('Error fetching products');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching products:', error);
            }
        });
    }
    

    // Populate the table with product data
    function populateTable(products) {
        const productList = $('#productList');
        productList.empty(); // Clear the table before populating

        products.forEach((product, index) => {
            const row = $('<tr>');

            
            row.html(`
                <td>${index + 1}</td>
                <td><img src="${product.img_path}" width="70" height="70"></td>
                <td>${product.name}</td>
                <td>${product.barcode}</td>
                <td>${product.category}</td>
                <td>${product.brand}</td>
                <td>${product.sale_price}</td>
                <td>${product.purchase_price}</td>
                <td>${product.in_stock}</td>
                <td>
                    <button class="btn btn-danger btn-sm" data-id="${product.id}" data-type="products" onclick="deleteItem(this)">Delete</button>
                </td>
            `);

            productList.append(row); // Append the row to the table
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
    
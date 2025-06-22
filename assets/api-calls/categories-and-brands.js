$(document).ready(function() {
    getBrands();
    getCategories();
});
function getBrands() {
    $.ajax({
        url: "https://mondolmotors.com/api/get_all_brands.php",
        method: "GET",
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                
                populateBrandTable(response.brands); // Rebuild dropdown
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching brands:", error);
        }
    });
}
function populateBrandTable(brands) {
    const brandList = $('#brandList');
    brandList.empty();
        brands.forEach((brand, index) => {
        const row = $('<tr>');

        row.html(`
            <td>${brand.id}</td>
            <td>${brand.brand}</td>
            <td>
                <!-- Add action buttons here -->
                <button class="btn btn-danger btn-sm" data-id="${brand.id}" data-type="brands" onclick="deleteItem(this)">Delete</button>
            </td>
        `);
            brandList.append(row); 
    });
}
function addBrand(event) {
    event.preventDefault(); // Prevent form submission

    const brandName = document.querySelector('input[name="brand"]').value.trim();
    
    

    if (brandName === "") {
        alert("Brand name is required.");
        console.log("Brand name is empty.");
        return;
    }

    const data = {
        brand: brandName
    };

    console.log("JSON data before sending:", JSON.stringify(data));

    $.ajax({
        url: "https://mondolmotors.com/api/add_brand.php", 
        type: "POST", 
        data: JSON.stringify(data), 
        contentType: "application/json", 
        xhrFields: {
            withCredentials: true 
        },
        success: function(response) {
            console.log("Response from server:", response);

            if (response.status === "success") {
                alert(response.message);

                // Optionally, close the modal
                $('#brandModal').modal('hide');

                // Reset the form
                document.getElementById('brandForm').reset();
                getBrands();
                
                
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

// Add event listener to the form submit action
document.getElementById('brandForm').addEventListener('submit', addBrand);

function getCategories(){
        $.ajax({
            url:"https://mondolmotors.com/api/get_all_category.php",
            method: "GET",
            dataType:"json",
            success: function(response){
                if(response.status=== "success"){
                    populateCategoryTable(response.categories);
                }
            }
        });
    }

function populateCategoryTable(categories) {
    const categoryList = $('#categoryList');
    categoryList.empty();
        categories.forEach((category, index) => {
        const row = $('<tr>');

        row.html(`
            <td>${category.id}</td>
            <td>${category.category}</td>
            <td>
                <!-- Add action buttons here -->
                <button class="btn btn-danger btn-sm" data-id="${category.id}" data-type="categories" onclick="deleteItem(this)">Delete</button>
            </td>
        `);
            categoryList.append(row); 
    });
}

function addCategory(){
    event.preventDefault(); // Prevent form submission

    const categoryName = document.querySelector('input[name="category"]').value.trim();
    
    

    if (categoryName === "") {
        alert("Category name is required.");
        console.log("Category name is empty.");
        return;
    }

    const data = {
        category: categoryName
    };

    console.log("JSON data before sending:", JSON.stringify(data));

    $.ajax({
        url: "https://mondolmotors.com/api/add_category.php", 
        type: "POST", 
        data: JSON.stringify(data), 
        contentType: "application/json", 
        xhrFields: {
            withCredentials: true 
        },
        success: function(response) {
            console.log("Response from server:", response);

            if (response.status === "success") {
                alert(response.message);

                // Optionally, close the modal
                $('#categoryModal').modal('hide');

                // Reset the form
                document.getElementById('categoryForm').reset();
                getCategories();
                
                
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
document.getElementById('categoryForm').addEventListener('submit', addCategory);

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
function getBrands() {
    $.ajax({
        url: "https://mondolmotors.com/api/get_all_brands.php",
        method: "GET",
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                
                populateBrandDropdown(response.brands); // Rebuild dropdown
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching brands:", error);
        }
    });
}


function populateBrandDropdown(brands) {
    const $selectBrand = $("#select-brand");
    
    $selectBrand.selectpicker('destroy');
    
    $selectBrand.empty();
    
    $selectBrand.append('<option value="">Choose a Brand</option>');
    
    brands.forEach((brand) => {
        $selectBrand.append(`<option value="${brand.id}">${brand.brand}</option>`);
    });
    
    $selectBrand.selectpicker();
}



function getCategories(){
        $.ajax({
            url:"https://mondolmotors.com/api/get_all_category.php",
            method: "GET",
            dataType:"json",
            success: function(response){
                if(response.status=== "success"){
                    populateCategoryDropdown(response.categories);
                }
            }
        });
    }
function populateCategoryDropdown(categories){
    const $selectCategory = $("#select-category");
    
    $selectCategory.selectpicker('destroy');
    
    $selectCategory.empty();
    
    $selectCategory.append('<option value="">Choose a Category</option>');
    
    categories.forEach((category) => {
        $selectCategory.append(`<option value="${category.id}">${category.category}</option>`);
    });
    
    $selectCategory.selectpicker();
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




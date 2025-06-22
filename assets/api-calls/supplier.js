$(document).ready(function() {
    fetchSuppliers();
});
function fetchSuppliers() {
    $.ajax({
        url: 'https://mondolmotors.com/api/get_all_supplier.php',  // API endpoint
        method: 'GET',
        dataType: 'json',
        
        success: function(data) {
            console.log(data);
            if (data.status === 'success') {
                populateTable(data.suppliers);
            } else {
                console.error('Error fetching suppliers');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching suppliers:', error);
        }
    });
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
                    fetchSuppliers();
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
    


function populateTable(suppliers) {
    const supplierList = $('#supplierList');
    supplierList.empty(); // Clear the table before populating
        suppliers.forEach((supplier, index) => {
        const row = $('<tr>');

        row.html(`
            <td>${supplier.id}</td>
            <td>${supplier.name}</td>
            <td>${supplier.phone}</td>
            <td>${supplier.address}</td>
            <td>${supplier.payable}</td>
            
            <td>
                <button class="btn btn-danger btn-sm" data-id="${supplier.id}" data-type="suppliers" onclick="deleteItem(this)">Delete</button>
            </td>
        `);
            supplierList.append(row); // Append the row to the table
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

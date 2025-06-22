$(document).ready(function() {
        fetchCustomers();
    });
    function fetchCustomers() {
        $.ajax({
            url: 'https://mondolmotors.com/api/get_all_customer.php',  // API endpoint
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.status === 'success') {
                    populateTable(data.customers);
                } else {
                    console.error('Error fetching customers');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching customers:', error);
            }
        });
    }
    

    // Populate the table with customer data
    function populateTable(customers) {
        const customerList = $('#customerList');
        customerList.empty(); // Clear the table before populating

        customers.forEach((customer, index) => {
            const row = $('<tr>');

            
            row.html(`
                <td>${index + 1}</td>
                <td>${customer.cus_name}</td>
                <td>${customer.cus_phone}</td>
                <td>${customer.cus_address}</td>
                <td>${parseFloat(customer.total_amount).toFixed(2)}</td>
                <td>${parseFloat(customer.total_paid).toFixed(2)}</td>
                <td>${parseFloat(customer.total_due).toFixed(2)}</td>
            `);

            customerList.append(row); // Append the row to the table
        });
    }
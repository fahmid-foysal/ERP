$(document).ready(function() {
    fetchExpenses();
});
function fetchExpenses() {
    $.ajax({
        url: 'https://mondolmotors.com/api/get_all_expense.php',  // API endpoint
        method: 'GET',
        dataType: 'json',
        
        success: function(data) {
            console.log(data);
            if (data.status === 'success') {
                populateTable(data.suppliers);
            } else {
                console.error('Error fetching expenses');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching expenses:', error);
        }
    });
}
    
function addExpense(event) {
    event.preventDefault(); // Prevent the default form submission

    // Collect the form data
    const name = document.querySelector('input[name="name"]').value;
    const amount = document.querySelector('input[name="amount"]').value;
    const date = document.querySelector('input[name="date"]').value;
    const option = document.querySelector('select[name="option"]').value;
    const note = document.querySelector('textarea[name="note"]') ? document.querySelector('textarea[name="note"]').value : '';

    // Prepare Request Data
    let requestData = {
        expense_name: name,
        amount: amount,
        date: date,
        option: option,
        note: note
    };

    // AJAX request using jQuery
    $.ajax({
        url: "https://mondolmotors.com/api/add_expense.php",
        type: 'POST',
        data: JSON.stringify(requestData),
        contentType: 'application/json',  // Send as JSON
        xhrFields: {
                        withCredentials: true 
                    },
        success: function(response) {
            console.log('Response:', response);
            if (response.status === 'success') {
                alert('Expense added successfully');
                document.getElementById('expenseForm').reset();
                $('#expenseModal').modal('hide');
                fetchExpenses();
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


document.getElementById('expenseForm').addEventListener('submit', addExpense);

function populateTable(suppliers) {
    const expenseList = $('#expenseList');
    expenseList.empty(); // Clear the table before populating
        suppliers.forEach((expense, index) => {
        const row = $('<tr>');

        row.html(`
            <td>${expense.id}</td>
            <td>${expense.expense_name}</td>
            <td>${expense.amount}</td>
            <td>${expense.date}</td>
            <td>${expense.option}</td>
            <td>${expense.note}</td>
            <td>
                <!-- Add action buttons here -->
                <button class="btn btn-danger btn-sm" data-id="${expense.id}" data-type="expense" onclick="deleteItem(this)">Delete</button>
            </td>
        `);
            expenseList.append(row); 
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
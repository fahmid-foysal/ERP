function fetchBusinessSummery(from_date = '', till_date = '') {
    $.ajax({
        url: 'https://mondolmotors.com/api/business_summery.php',
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            date: from_date,
            till_date: till_date
        }),
        success: function (res) {
            console.log(res);
            if (res.status === 'success') {
                $('#totalAmount').text(parseFloat(res.summery.total_amount).toFixed(2));
                $('#totalExpense').text(parseFloat(res.summery.total_expense).toFixed(2));
                $('#productSale').text(parseFloat(res.summery.product_sale).toFixed(2));
                $('#serviceSale').text(parseFloat(res.summery.service_sale).toFixed(2));
                $('#totalPaid').text(parseFloat(res.summery.total_paid).toFixed(2));
                $('#totalDue').text(parseFloat(res.summery.total_due).toFixed(2));
                $('#totalProfit').text(parseFloat(res.summery.total_profit).toFixed(2));
                $('#payable').text(parseFloat(res.summery.payable).toFixed(2));
                $('#totalPurchase').text(parseFloat(res.summery.total_purchase).toFixed(2));
            }
             else {
                alert(res.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('Failed to fetch data. Please check console for details.');
        }
    });
}

$(document).ready(function () {
    // initial load (no filters)
    fetchBusinessSummery();

    // on filter submit
    $('#businessSummeryForm').on('submit', function (e) {
        e.preventDefault();
        const from_date = $('#from_date').val();
        const till_date = $('#till_date').val();
        fetchBusinessSummery(from_date, till_date);
    });
});
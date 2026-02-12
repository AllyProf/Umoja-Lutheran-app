<script>
function openCeremonyPaymentModal(dayServiceId, guestName, unpaidAmount) {
    document.getElementById('ceremonyDayServiceId').value = dayServiceId;
    document.getElementById('ceremonyGuestName').innerText = guestName;
    document.getElementById('ceremonyUnpaidAmount').innerText = unpaidAmount.toLocaleString() + ' TZS';
    document.getElementById('ceremonyPaymentMethod').value = 'cash';
    document.getElementById('ceremonyPaymentReference').value = '';
    toggleCeremonyRefField();
    $('#ceremonyPaymentModal').modal('show');
}

function toggleCeremonyRefField() {
    const method = document.getElementById('ceremonyPaymentMethod').value;
    const container = document.getElementById('ceremonyRefFieldContainer');
    if (method === 'cash') {
        container.style.display = 'none';
    } else {
        container.style.display = 'block';
    }
}

function submitCeremonyPayment() {
    const dayServiceId = document.getElementById('ceremonyDayServiceId').value;
    const method = document.getElementById('ceremonyPaymentMethod').value;
    const reference = document.getElementById('ceremonyPaymentReference').value.trim();
    
    if (method !== 'cash' && !reference) {
        swal("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
    }

    swal({
        title: "Confirm Settlement",
        text: "Mark all unpaid consumption for this ceremony as paid?",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Settle!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/customer/ceremonies/settle-usage`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    day_service_id: dayServiceId,
                    payment_method: method,
                    payment_reference: reference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message + ` (${data.count} items settled)`,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to settle payment. Please try again.", "error");
            });
        }
    });
}
</script>


  <script src=""""></script>
  <script>
        functi    on receiveTransfer(transferId) {
      swal({
        title: "Receive Transfer?",
        text: "Mark this transfer as received?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, received!",
        cancelButtonText: "Cancel"
      }, function (isConfirm) {
        if (isConfirm) {
          callApi(`""`.replace(':id', transferId), 'PUT', { status: 'completed' });
        }
      });
    }

    function completeOrder(orderId, paymentMethod = 'room_charge') {
      let title = paymentMethod === 'cash' ? "Record Cash Payment" : "Charge to Room";
      let text = paymentMethod === 'cash' ? "Record this walk-in sale as PAID (Cash)?" : "Mark this order as served and charge to the ROOM bill?";
      let btnColor = paymentMethod === 'cash' ? "#28a745" : "#007bff";

      swal({
        title: title,
        text: text,
        type: paymentMethod === 'cash' ? "success" : "info",
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Proceed",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
      }, function (isConfirm) {
        if (isConfirm) {
          callApi(`""`.replace(':id', orderId), 'POST', { payment_method: paymentMethod });
        }
      });
    }

    // POS Logic
    let posCart = [];

    // Data for Dynamic Food selection
    const foodBasesData = [];

    function showFoodBuilderModal(baseName) {
      const sides = foodBasesData[baseName];
      if (!sides || sides.length === 0) return;

      let optionsHtml = '<div class="row">';
      sides.forEach((side, index) => {
        optionsHtml += `
                    <div class="col-12 mb-2">
                        <button class="btn btn-outline-primary btn-block text-left p-3 d-flex justify-content-between align-items-center"
                            onclick="selectFoodAddOn(${index}, '${baseName}')">
                            <span class="font-weight-bold" style="font-size: 1.1rem;">${side.side}</span>
                            <span class="badge badge-primary px-2 py-1" style="font-size: 0.9rem;">${Number(side.price).toLocaleString()} TSH</span>
                        </button>
                    </div>
                `;
      });
      optionsHtml += '</div>';

      Swal.fire({
        title: `<i class="fa fa-cutlery text-primary mr-2"></i> How would you like the ${baseName}?`,
        html: optionsHtml,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
          popup: 'rounded-lg'
        }
      });
    }

    function selectFoodAddOn(sideIndex, baseName) {
      const item = foodBasesData[baseName][sideIndex];
      Swal.close();
      let defaultImage = 'https://img.icons8.com/color/144/restaurant-.png';
      let actImg = item.image ? '/storage/' + item.image : defaultImage;
      // Same logic as addToPosCart
      addToPosCart(item.name, item.price, null, null, 'food', item.id, actImg, 'plate', 999, 1);
    }

    function openWalkInModal(residentRoom = null, residentName = null, dayServiceId = null) {
      $('#walkInModal').modal('show');
      posCart = [];

      // Reset inputs
      document.getElementById('walkInGuestName').value = residentName || '';

      // Store dayServiceId in a global variable for checkout
      window.currentDayServiceId = dayServiceId;

      // If it's for a ceremony, update title/label
      if (dayServiceId) {
        document.getElementById('posModalTitle').innerText = 'Record Usage: ' + residentName;
        document.getElementById('btnConfirmSale').innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>RECORD CEREMONY USAGE</strong>';
      } else {
        document.getElementById('posModalTitle').innerText = 'New Walk-in Order';
        document.getElementById('btnConfirmSale').innerHTML = '<i class="fa fa-check-circle mr-1"></i> <strong>RECORD WALK-IN ORDER</strong>';
      }

      renderPosCart();
    }

    function addToPosCart(name, price, pid, vid, type, foodId = null, image = null, unit = 'pic', currentStock = null, servingsPerPic = 1) {
      const key = foodId ? String(foodId) : `${pid}_${vid}_${unit}`;
      const existing = posCart.find(item => String(item.key) === String(key));

      // Calculate how much we're trying to add
      let newQty = existing ? existing.qty + 1 : 1;

      // Convert to bottles if selling by serving/glass
      let bottlesNeeded = newQty;
      if (unit === 'serving' && servingsPerPic > 0) {
        bottlesNeeded = newQty / servingsPerPic;
      }

      // Check stock availability (only for products with stock tracking)
      if (currentStock !== null && currentStock > 0 && bottlesNeeded > currentStock) {
        const maxAllowed = unit === 'serving' ? Math.floor(currentStock * servingsPerPic) : Math.floor(currentStock);
        Swal.fire({
          title: 'Stock Limit Exceeded',
          html: `<p>Cannot add more of this item.</p>
                           <p><strong>Current Stock:</strong> ${currentStock.toFixed(1)} bottles</p>
                           <p><strong>Maximum ${unit === 'serving' ? 'Glasses' : 'Units'}:</strong> ${maxAllowed}</p>`,
          icon: 'warning',
          confirmButtonText: 'OK'
        });
        return;
      }

      if (existing) {
        existing.qty++;
      } else {
        posCart.push({
          key: key,
          name: name,
          price: price,
          pid: pid,
          vid: vid,
          type: type,
          foodId: foodId,
          image: image,
          unit: unit,
          qty: 1,
          currentStock: currentStock,
          servingsPerPic: servingsPerPic
        });
      }
      renderPosCart();
    }

    function removeFromPosCart(key) {
      posCart = posCart.filter(item => String(item.key) !== String(key));
      renderPosCart();
    }

    function updatePosQty(key, delta) {
      const item = posCart.find(i => String(i.key) === String(key));
      if (item) {
        const newQty = item.qty + delta;

        // If increasing quantity, check stock
        if (delta > 0 && item.currentStock !== null && item.currentStock !== undefined) {
          let bottlesNeeded = newQty;
          if (item.unit === 'serving' && item.servingsPerPic > 0) {
            bottlesNeeded = newQty / item.servingsPerPic;
          }

          if (bottlesNeeded > item.currentStock) {
            const maxAllowed = item.unit === 'serving' ? Math.floor(item.currentStock * item.servingsPerPic) : Math.floor(item.currentStock);
            Swal.fire({
              title: 'Stock Limit Exceeded',
              html: `<p>Cannot add more of this item.</p>
                                   <p><strong>Current Stock:</strong> ${item.currentStock.toFixed(1)} bottles</p>
                                   <p><strong>Maximum ${item.unit === 'serving' ? 'Glasses' : 'Bottles'}:</strong> ${maxAllowed}</p>`,
              icon: 'warning',
              confirmButtonText: 'OK'
            });
            return;
          }
        }

        item.qty = newQty;
        if (item.qty <= 0) removeFromPosCart(key);
        else renderPosCart();
      }
    }

    function renderPosCart() {
      const list = document.getElementById('posCartList');
      const totalEl = document.getElementById('posTotalAmount');
      const btn = document.getElementById('btnConfirmSale');

      if (posCart.length === 0) {
        list.innerHTML = `<div class="text-center text-muted mt-5"><i class="fa fa-shopping-basket fa-3x mb-2"></i><p>Cart is empty</p></div>`;
        totalEl.innerText = '0 TZS';
        btn.disabled = true;
        return;
      }

      let total = 0;
      let html = '';
      posCart.forEach(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;
        html += `
                    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded shadow-sm">
                        <div class="d-flex align-items-center" style="max-width: 65%;">
                            ${item.image ? `<img src="${item.image}" class="mr-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">` : ''}
                            <div>
                                <div class="font-weight-bold" style="font-size: 0.85rem; line-height: 1.2;">${item.name}</div>
                                <small class="text-muted">${item.price.toLocaleString()} x ${item.qty}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn-group btn-group-sm mr-2">
                                <button class="btn btn-outline-secondary px-2" onclick="updatePosQty('${item.key}', -1)">-</button>
                                <button class="btn btn-outline-secondary px-2" onclick="updatePosQty('${item.key}', 1)">+</button>
                            </div>
                            <div class="text-right mr-2" style="min-width: 80px;">
                                <span class="font-weight-bold d-block" style="font-size: 0.85rem;">${itemTotal.toLocaleString()}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger border-0" onclick="removeFromPosCart('${item.key}')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
      });

      list.innerHTML = html;
      totalEl.innerText = `${total.toLocaleString()} TZS`;
      btn.disabled = false;
    }

    let currentCategory = 'all';

    function filterByCategory(category, btn) {
      currentCategory = category;

      // Update UI active state
      const tabs = document.getElementById('posCategoryTabs').querySelectorAll('button');
      tabs.forEach(t => {
        t.classList.remove('btn-primary', 'active');
        t.classList.add('btn-outline-primary');
      });
      btn.classList.remove('btn-outline-primary');
      btn.classList.add('btn-primary', 'active');

      filterPosItems();
    }

    function filterPosItems() {
      const query = document.getElementById('itemSearch').value.toLowerCase();
      const cards = document.querySelectorAll('.pos-item-card');

      cards.forEach(card => {
        const name = card.dataset.name;
        const category = card.dataset.category;

        const matchesQuery = name.includes(query);
        const matchesCategory = currentCategory === 'all' || category === currentCategory;

        if (matchesQuery && matchesCategory) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }

    async function processWalkInCheckout() {
      const now = new Date();
      const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
      const guestName = document.getElementById('walkInGuestName').value.trim() || 'Walk-in (' + timeStr + ')';

      const confirm = await Swal.fire({
        title: "Confirm Sale?",
        text: `Record order for ${guestName}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Record Sale"
      });

      if (!confirm.isConfirmed) return;

      $('#walkInModal').modal('hide');
      Swal.fire({
        title: 'Processing...',
        didOpen: () => { Swal.showLoading(); }
      });

      let successCount = 0;
      let lastError = "An unknown error occurred.";

      for (const item of posCart) {
        const payload = {
          service_id: item.type === 'food' ? "" : "",
          product_id: item.pid,
          product_variant_id: item.vid,
          quantity: item.qty,
          is_walk_in: 1,
          walk_in_name: guestName,
          day_service_id: window.currentDayServiceId || null,
          payment_timing: 'later',
          item_name: item.name,
          service_specific_data: {
            food_id: item.foodId,
            item_name: item.name,
            product_id: item.pid,
            product_variant_id: item.vid,
            selling_method: item.unit || 'pic'
          }
        };

        try {
          const response = await fetch("""", {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });
          const res = await response.json();
          if (res.success) {
            successCount++;
          } else {
            lastError = res.message;
            if (res.errors) {
              lastError += ": " + JSON.stringify(res.errors);
            }
            console.error("Item failed:", item.name, res);
          }
        } catch (e) {
          console.error(e);
          lastError = e.message;
        }
      }

      if (successCount === posCart.length) {
        await Swal.fire({
          title: "Success!",
          text: "Walk-in sale recorded successfully!",
          icon: "success",
          timer: 2000,
          showConfirmButton: false
        });
        window.location.reload();
      } else {
        await Swal.fire({
          title: "Error",
          text: "Some items failed to process. Reason: " + lastError,
          icon: "error"
        });
      }
    }

    function openPaymentModal(orderId, amount, isWalkIn = 0) {
      document.getElementById('paymentOrderId').value = orderId;
      document.getElementById('paymentAmountDisplay').innerText = amount.toLocaleString() + ' TZS';

      const methodSelect = document.getElementById('paymentMethod');
      const roomChargeOption = methodSelect.querySelector('option[value="room_charge"]');

      if (isWalkIn) {
        if (roomChargeOption) roomChargeOption.style.display = 'none';
        if (methodSelect.value === 'room_charge') methodSelect.value = 'cash';
      } else {
        if (roomChargeOption) roomChargeOption.style.display = 'block';
      }

      document.getElementById('paymentMethod').value = 'cash';
      document.getElementById('paymentReference').value = '';
      toggleRefField();
      $('#paymentModal').modal('show');
    }

    function toggleRefField() {
      const method = document.getElementById('paymentMethod').value;
      const container = document.getElementById('refFieldContainer');
      if (method === 'cash' || method === 'room_charge') { // Room charge also doesn't need a ref
        container.style.display = 'none';
      } else {
        container.style.display = 'block';
      }
    }

    function submitPayment() {
      const orderId = document.getElementById('paymentOrderId').value;
      const method = document.getElementById('paymentMethod').value;
      const reference = document.getElementById('paymentReference').value.trim();

      if (method !== 'cash' && method !== 'room_charge' && !reference) {
        swal("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
      }

      $('#paymentModal').modal('hide');

      // If it's a room charge, use the legacy completeOrder
      if (method === 'room_charge') {
        completeOrder(orderId, method, reference);
      } else {
        // Use the new POS settlement route
        settlePOSPayment(orderId, method, reference);
      }
    }

    function settlePOSPayment(orderId, method, reference = '') {
      swal({
        title: "Confirm Payment?",
        text: `Record ${method.toUpperCase()} payment of ${document.getElementById('paymentAmountDisplay').innerText}?`,
        type: "success",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Yes, Paid!",
        closeOnConfirm: false
      }, function (isConfirm) {
        if (isConfirm) {
          const url = `/customer/pos/settle-payment/${orderId}`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              payment_method: method,
              payment_reference: reference
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                swal("Success!", data.message, "success");
                setTimeout(() => location.reload(), 1500);
              } else {
                swal("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              swal("Error!", "Failed to record payment.", "error");
            });
        }
      });
    }

    function serveOrder(orderId, itemName) {
      swal({
        title: "Mark as Served?",
        text: "Confirm that '" + itemName + "' has been taken/served? Payment will remain PENDING.",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#17a2b8",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Served!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
      }, function (isConfirm) {
        if (isConfirm) {
          const url = `/bar-keeper/orders/${orderId}/serve`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                swal({
                  title: "Served!",
                  text: data.message,
                  type: "success",
                  timer: 1500,
                  showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
              } else {
                swal("Error!", data.message, "error");
              }
            })
            .catch(error => {
              console.error('Error:', error);
              swal("Error!", "Failed to update order. Please try again.", "error");
            });
        }
      });
    }

    function completeOrder(orderId, method, reference = '') {
      let title = "Confirm Payment?";
      let text = "Record payment via " + method.replace('_', ' ').toUpperCase() + "?";
      let icon = "warning";
      let btnColor = "#28a745";

      if (method === 'room_charge') {
        title = "Charge to Room?";
        text = "This will add the amount to the guest's room bill.";
        icon = "info";
        btnColor = "#007bff";
      }

      swal({
        title: title,
        text: text,
        type: icon,
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Confirm!",
        cancelButtonText: "No, Cancel",
        closeOnConfirm: false
      }, function (isConfirm) {
        if (isConfirm) {
          const url = `/bar-keeper/orders/${orderId}/complete`;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              payment_method: method,
              payment_reference: reference
            })
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                swal({
                  title: "Success!",
                  text: data.message,
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
              swal("Error!", "An error occurred. Please try again.", "error");
            });
        }
      });
    }

    function printWalkInDocket(orderId) {
      const url = `/bar-keeper/orders/${orderId}/print-docket`;
      window.open(url, 'DocketPrint', 'width=800,height=600');
    }

    function callApi(url, method, data) {
      fetch(url, {
        method: method,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            swal({
              title: "Success!",
              text: data.message || "Action completed successfully!",
              type: "success",
              timer: 2000,
              showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
          } else {
            swal("Error!", data.message || "Action failed.", "error");
          }
        })
        .catch(error => {
          console.error('Error:', error);
          swal("Error!", "An error occurred. Please try again.", "error");
        });
    }
  </script>

  <!-- Payment Modal HTML -->
  <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="paymentOrderId">
          <p class="h4 text-center mb-4">Amount: <span id="paymentAmountDisplay"
              class="font-weight-bold text-primary"></span></p>

          <div class="form-group">
            <label for="paymentMethod">Payment Method</label>
            <select class="form-control" id="paymentMethod" onchange="toggleRefField()">
              <option value="cash">Cash</option>
              <option value="room_charge">Room Charge</option>
              <option value="mpesa">M-Pesa</option>
              <option value="halopesa">Halopesa</option>
              <option value="airtel_money">Airtel Money</option>
              <option value="mixx_by_yass">Mixx by Yass</option>
              <option value="nmb">NMB Bank</option>
              <option value="crdb">CRDB Bank</option>
              <option value="kcb">KCB Bank</option>
            </select>
          </div>
          <div class="form-group" id="refFieldContainer" style="display: none;">
            <label for="paymentReference">Reference Number</label>
            <input type="text" class="form-control" id="paymentReference" placeholder="Enter reference number">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Ceremony Payment Modal HTML -->
  <div class="modal fade" id="ceremonyPaymentModal" tabindex="-1" role="dialog"
    aria-labelledby="ceremonyPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ceremonyPaymentModalLabel">Settle Ceremony Usage</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="ceremonyDayServiceId">
          <p class="h5 text-center mb-2">Guest: <span id="ceremonyGuestName" class="font-weight-bold text-info"></span>
          </p>
          <p class="h4 text-center mb-4">Unpaid Amount: <span id="ceremonyUnpaidAmount"
              class="font-weight-bold text-danger"></span></p>

          <div class="form-group">
            <label for="ceremonyPaymentMethod">Payment Method</label>
            <select class="form-control" id="ceremonyPaymentMethod" onchange="toggleCeremonyRefField()">
              <option value="cash">Cash</option>
              <option value="room_charge">Room Charge</option>
              <option value="mpesa">M-Pesa</option>
              <option value="halopesa">Halopesa</option>
              <option value="airtel_money">Airtel Money</option>
              <option value="mixx_by_yass">Mixx by Yass</option>
              <option value="nmb">NMB Bank</option>
              <option value="crdb">CRDB Bank</option>
              <option value="kcb">KCB Bank</option>
            </select>
          </div>
          <div class="form-group" id="ceremonyRefFieldContainer" style="display: none;">
            <label for="ceremonyPaymentReference">Reference Number</label>
            <input type="text" class="form-control" id="ceremonyPaymentReference" placeholder="Enter reference number">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitCeremonyPayment()">Settle Payment</button>
        </div>
      </div>
    </div>
  </div>
  </div>

  <script>
    function openCeremonyPaymentModal(dayServiceId, guestName, unpaidAmount) {
      document.getElementById('ceremonyDayServiceId').value = dayServiceId;
      document.getElementById('ceremonyGuestName').innerText = guestName;
      document.getElementById('ceremonyUnpaidAmount').innerText = unpaidAmount.toLocaleString() + ' TZS';

      const methodSelect = document.getElementById('ceremonyPaymentMethod');
      const roomChargeOption = methodSelect.querySelector('option[value="room_charge"]');
      if (roomChargeOption) roomChargeOption.style.display = 'none'; // Ceremonies are walk-ins

      document.getElementById('ceremonyPaymentMethod').value = 'cash';
      document.getElementById('ceremonyPaymentReference').value = '';
      toggleCeremonyRefField();
      $('#ceremonyPaymentModal').modal('show');
    }

    function toggleCeremonyRefField() {
      const method = document.getElementById('ceremonyPaymentMethod').value;
      const container = document.getElementById('ceremonyRefFieldContainer');
      if (method === 'cash' || method === 'room_charge') {
        container.style.display = 'none';
      } else {
        container.style.display = 'block';
      }
    }

    function submitCeremonyPayment() {
      const dayServiceId = document.getElementById('ceremonyDayServiceId').value;
      const method = document.getElementById('ceremonyPaymentMethod').value;
      const reference = document.getElementById('ceremonyPaymentReference').value.trim();

      if (method !== 'cash' && method !== 'room_charge' && !reference) {
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
      }, function (isConfirm) {
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

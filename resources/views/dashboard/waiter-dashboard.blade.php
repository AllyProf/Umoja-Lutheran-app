@extends('dashboard.layouts.app')

@section('content')
<style>
    :root {
        --primary: #e77a31;
        --primary-dark: #cc6a27;
        --primary-light: #fff3e0;
        --bg-gray: #f2f4f7;
        --text-main: #2d3436;
        --radius: 20px;
    }

    /* Mobile First Layout Overrides */
    body { background-color: var(--bg-gray); }
    .content-wrapper { padding: 0 !important; margin: 0 !important; }
    .main-footer { display: none !important; }

    /* Sticky Header */
    .pos-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: white;
        padding: 10px 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .search-pill {
        background: var(--bg-gray);
        border-radius: 50px;
        padding: 8px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }
    .search-pill input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
        font-weight: 600;
    }

    /* Categories / Tabs */
    .cat-nav {
        display: flex;
        overflow-x: auto;
        padding: 15px;
        gap: 12px;
        scrollbar-width: none;
    }
    .cat-nav::-webkit-scrollbar { display: none; }
    
    .cat-item {
        flex: 0 0 auto;
        padding: 10px 20px;
        background: white;
        border-radius: 50px;
        font-weight: 800;
        color: var(--text-main);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        cursor: pointer;
        transition: 0.3s;
        border: 2px solid transparent;
        font-size: 0.9rem;
    }
    .cat-item.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 8px 15px rgba(231,122,49,0.2);
    }

    /* Menu Grid */
    .menu-grid {
        padding: 10px 15px 120px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    @media (min-width: 768px) {
        .menu-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (min-width: 1200px) {
        .menu-grid { grid-template-columns: repeat(4, 1fr); }
    }

    .item-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
    }
    .item-img {
        height: 110px;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .item-img img { width: 100%; height: 100%; object-fit: cover; }
    .item-price-tag {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: white;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 900;
        font-size: 0.75rem;
        color: var(--primary);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .item-info { padding: 10px; }
    .item-name {
        font-size: 0.85rem;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1.2;
        min-height: 2rem;
    }
    
    .add-btn-small {
        width: 100%;
        padding: 6px;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
        border: 1.5px solid var(--primary);
        font-weight: 800;
        font-size: 0.7rem;
        transition: 0.2s;
    }
    .add-btn-small:active { transform: scale(0.95); background: var(--primary); color: white; }

    /* Bottom Navigation / Float Action Button */
    .bottom-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        background: white;
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
    }
    .view-cart-btn {
        background: var(--primary);
        color: white;
        padding: 12px 25px;
        border-radius: 20px;
        font-weight: 900;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 5px 15px rgba(231,122,49,0.3);
    }

    /* Cart Drawer (Overlay) */
    .cart-drawer {
        position: fixed;
        bottom: -100%;
        left: 0;
        width: 100%;
        height: 90vh;
        background: white;
        z-index: 1040;
        border-radius: 30px 30px 0 0;
        transition: bottom 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.2);
    }
    .cart-drawer.open { bottom: 0; }
    .drawer-handle {
        width: 40px;
        height: 4px;
        background: #ddd;
        border-radius: 10px;
        margin: 15px auto 5px;
    }
    .drawer-header { padding: 10px 25px 20px; border-bottom: 1px solid #f0f0f0; }
    .cart-body { flex: 1; overflow-y: auto; padding: 20px; }
    .drawer-footer { padding: 20px 25px 40px; background: #fafafa; border-top: 1px solid #eee; }

    /* Quantity Controls */
    .qty-box {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--bg-gray);
        padding: 5px 12px;
        border-radius: 12px;
    }

    /* Selection Pills */
    .type-pill {
        flex: 1;
        padding: 10px;
        border: 2px solid #eee;
        border-radius: 15px;
        text-align: center;
        font-weight: 800;
        cursor: pointer;
    }
    .type-pill.active { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

    .overlay-backdrop {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1039;
        display: none;
    }
</style>

<!-- POS Header -->
<div class="pos-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <span class="text-muted small font-weight-bold">WAITER POS</span>
            <h4 class="font-weight-bold mb-0">Umoj Lutheran Hostel</h4>
            <div id="servingIndicator" class="badge badge-info mt-1" style="display: none; border-radius: 50px; font-size: 0.7rem; padding: 4px 10px;">
                <i class="fa fa-user mr-1"></i> Serving: <span id="servingName">...</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div onclick="window.location.href='{{ route('waiter.sales-summary') }}'" class="mr-3" title="Sales Summary">
                <i class="fa fa-line-chart fa-lg text-primary"></i>
            </div>
            <div onclick="window.location.href='{{ route('waiter.orders') }}'" title="Order History">
                <i class="fa fa-history fa-lg text-muted"></i>
            </div>
        </div>
    </div>
    <div class="search-pill">
        <i class="fa fa-search text-muted"></i>
        <input type="text" id="globalSearch" placeholder="Find food or drinks...">
    </div>
</div>

<!-- Category Navbar -->
<div class="cat-nav">
    <div class="cat-item active" onclick="setMasterTab('food', this)">🍳 Kitchen</div>
    <div class="cat-item" onclick="setMasterTab('drinks', this)">🍷 Bar & Cellar</div>
</div>

<!-- Drink Sub-cats (Visible only when drinks active) -->
<div id="drinkSubCats" class="cat-nav pt-0" style="display: none;">
    <div class="cat-item active" style="font-size: 0.75rem; padding: 6px 15px;" onclick="setDrinkSub('all', this)">All</div>
    <div class="cat-item" style="font-size: 0.75rem; padding: 6px 15px;" onclick="setDrinkSub('spirits', this)">Spirits</div>
    <div class="cat-item" style="font-size: 0.75rem; padding: 6px 15px;" onclick="setDrinkSub('beers', this)">Beers</div>
    <div class="cat-item" style="font-size: 0.75rem; padding: 6px 15px;" onclick="setDrinkSub('soft_drinks', this)">Soft Drinks</div>
</div>

<!-- Menu Grid -->
<div class="menu-grid" id="menuGrid">
    <!-- Food Items -->
    @foreach($foodItems as $food)
    <div class="item-card food-item" data-name="{{ strtolower($food['name']) }}">
        <div class="item-img">
            @if(isset($food['image']))
                <img src="{{ Storage::url($food['image']) }}" alt="{{ $food['name'] }}">
            @else
                <i class="fa fa-cutlery fa-2x text-muted"></i>
            @endif
            <span class="item-price-tag">{{ number_format($food['price']) }}</span>
        </div>
        <div class="item-info">
            <h6 class="item-name">{{ $food['name'] }}</h6>
            <button class="add-btn-small" onclick="fastAdd('food', {{ json_encode($food) }})">ADD TO BASKET</button>
        </div>
    </div>
    @endforeach

    <!-- Drink Items -->
    @foreach($drinks as $drink)
    @php
        $isOut = !($drink->in_stock);
        $stockClass = $isOut ? 'opacity-50' : '';
    @endphp
    <div class="item-card drink-item {{ $stockClass }}" style="display: none;" data-name="{{ strtolower($drink->name) }}" data-cat="{{ $drink->category }}">
        <div class="item-img">
            @if(isset($drink->image))
                <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($drink->name) }}&background=fff3e0&color=e77a31'">
            @else
                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light">
                    <i class="fa fa-glass fa-2x text-muted"></i>
                </div>
            @endif
            
            @if($isOut)
                <div class="position-absolute bg-danger text-white px-2 py-1 rounded" style="top: 10px; left: 10px; font-size: 0.6rem; font-weight: 800;">
                    OUT OF STOCK
                </div>
            @else
                <div class="position-absolute bg-success text-white px-2 py-1 rounded" style="top: 10px; left: 10px; font-size: 0.6rem; font-weight: 800;">
                    LIVE: {{ round($drink->current_stock, 1) }}
                </div>
            @endif
        </div>
        <div class="item-info">
            <h6 class="item-name mb-2">{{ $drink->name }}</h6>
            @foreach($drink->options as $opt)
            <button class="add-btn-small mb-1 d-flex justify-content-between align-items-center" 
                    {{ $isOut ? 'disabled' : '' }}
                    onclick="fastAdd('drink', {{ json_encode($drink) }}, '{{ $opt['type'] }}', {{ $opt['price'] }}, '{{ $opt['method'] }}')">
                <span>{{ strtoupper($opt['type']) }}</span>
                <span>{{ number_format($opt['price']) }}</span>
            </button>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

<!-- Floating Bottom Bar -->
<div class="bottom-bar">
    <div class="d-flex flex-column">
        <span class="text-muted small font-weight-bold">TOTAL</span>
        <span class="font-weight-bold h5 mb-0" id="floatTotal">0 TSH</span>
    </div>
    <div class="view-cart-btn" onclick="toggleCart()">
        <i class="fa fa-shopping-basket"></i>
        <span>BASKET</span>
        <span class="badge badge-light" id="cartCount">0</span>
    </div>
</div>

<!-- Cart Drawer -->
<div class="overlay-backdrop" id="backdrop" onclick="toggleCart()"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="drawer-handle"></div>
    <div class="drawer-header">
        <h5 class="font-weight-bold">Current Order</h5>
        <div class="d-flex gap-10 mt-3" style="gap: 10px;">
            <div class="type-pill active" id="pillResident" onclick="setGuest('resident')">RESIDENT</div>
            <div class="type-pill" id="pillWalkIn" onclick="setGuest('walk_in')">WALK-IN</div>
        </div>
    </div>
    
    <div class="cart-body">
        <!-- Identity Selector -->
        <div id="boxResident" class="mb-4">
            <label class="small font-weight-bold text-muted uppercase">Select Room Number</label>
            <select id="booking_id" class="form-control select2-mobile" style="width: 100%;">
                <option value="">-- Select Room --</option>
                @forelse($activeBookings as $booking)
                    <option value="{{ $booking['id'] }}">{{ $booking['room_type'] }}-{{ $booking['room_number'] }} ({{ $booking['guest_name'] }})</option>
                @empty
                    <option value="" disabled>No active rooms</option>
                @endforelse
            </select>
        </div>
        <div id="boxWalkIn" class="mb-4" style="display: none;">
            <label class="small font-weight-bold text-muted uppercase">Guest Info</label>
            <input type="text" id="walk_in_name" class="form-control" placeholder="Table # / Guest Name">
        </div>

        <div id="cartList" class="mt-2">
            <!-- Items injected here -->
        </div>
    </div>

    <div class="drawer-footer">
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <span class="font-weight-bold text-muted">GRAND TOTAL</span>
            <span class="h4 font-weight-bold mb-0 text-primary" id="drawerTotal">0 TSH</span>
        </div>
        
        <button class="btn btn-primary btn-block py-3 font-weight-bold" id="btnPlaceOrder" onclick="submitOrder()" disabled style="border-radius: 15px; background: var(--primary);">
            SEND TO PREPARATION
        </button>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Cache Buster: {{ now()->timestamp }} - Fixed drink order submission v2.1
    let posItems = [];
    let curMaster = 'food';
    let curGuest = 'resident';
    const activeBookings = {!! json_encode($activeBookings) !!};

    $(document).ready(function() {
        $('.select2-mobile').select2({ placeholder: "Room Search..." });

        // Room Selection Change - Removed summary box update logic
        $('#booking_id').on('change', function() {
            // Summary logic removed as requested
        });

        // Search Logic
        $('#globalSearch').on('input', function() {
            const term = $(this).val().toLowerCase();
            const selector = curMaster === 'food' ? '.food-item' : '.drink-item';
            
            $(selector).each(function() {
                $(this).toggle($(this).data('name').includes(term));
            });
        });

        // Handle pre-fill from query params (Add Items flow)
        const urlParams = new URLSearchParams(window.location.search);
        const roomId = urlParams.get('room_id');
        const walkIn = urlParams.get('walk_in');

        if (roomId || walkIn) {
            if (roomId) {
                setGuest('resident');
                $('#booking_id').val(roomId).trigger('change');
                
                // Visual feedback without opening cart
                const roomText = $(`#booking_id option[value="${roomId}"]`).text() || 'Selected Room';
                Swal.fire({
                    toast: true, position: 'top', timer: 3000, showConfirmButton: false,
                    icon: 'info', title: `Adding to ${roomText}`, background: '#2d3436', color: '#fff'
                });
            } else if (walkIn) {
                setGuest('walk_in');
                $('#walk_in_name').val(walkIn);
                
                Swal.fire({
                    toast: true, position: 'top', timer: 3000, showConfirmButton: false,
                    icon: 'info', title: `Adding to Guest: ${walkIn}`, background: '#2d3436', color: '#fff'
                });
            }
        }

        // Remove payment flow change handler as it's gone
    });

    function setMasterTab(tab, el) {
        curMaster = tab;
        $('.cat-item').not($(el).siblings()).removeClass('active');
        $(el).addClass('active');
        
        if (tab === 'food') {
            $('.food-item').show();
            $('.drink-item').hide().css('display', 'none'); // Force hide drinks
            $('#drinkSubCats').hide();
        } else {
            $('.food-item').hide().css('display', 'none'); // Force hide food
            $('.drink-item').show();
            $('#drinkSubCats').show();
        }
    }

    function setDrinkSub(sub, el) {
        $(el).siblings().removeClass('active');
        $(el).addClass('active');
        
        if (sub === 'all') {
            $('.drink-item').show();
        } else {
            $('.drink-item').hide();
            $(`.drink-item[data-cat*="${sub}"]`).show();
            // Handle specific mappings
            if (sub === 'spirits') $(`.drink-item[data-cat="whiskey"], .drink-item[data-cat="liquor"]`).show();
            if (sub === 'soft_drinks') $(`.drink-item[data-cat="non_alcoholic_beverage"], .drink-item[data-cat="water"], .drink-item[data-cat="juices"]`).show();
        }
    }

    function setGuest(type) {
        curGuest = type;
        $('.type-pill').removeClass('active');
        
        if (type === 'resident') {
            $('#pillResident').addClass('active');
            $('#boxResident').show();
            $('#boxWalkIn').hide();
            // $('#walk_in_name').val(''); // Don't clear unnecessarily
            $('#btnPlaceOrder').text('SEND TO PREPARATION');
        } else {
            $('#pillWalkIn').addClass('active');
            $('#boxResident').hide();
            $('#boxWalkIn').show();
            // $('#booking_id').val(null).trigger('change'); // Don't clear unnecessarily
            $('#btnPlaceOrder').text('SEND TO PREPARATION');
        }
        updateServingIndicator();
    }

    function updateServingIndicator() {
        let name = '';
        if (curGuest === 'resident') {
            const selected = $('#booking_id option:selected');
            if (selected.val()) {
                name = selected.text().split('(')[0].trim(); // Get Room bit
            }
        } else {
            name = $('#walk_in_name').val().trim() || 'New Walk-in';
        }

        if (name) {
            $('#servingName').text(name);
            $('#servingIndicator').fadeIn();
        } else {
            $('#servingIndicator').hide();
        }
    }

    $(document).on('change input', '#booking_id, #walk_in_name', updateServingIndicator);

    function toggleCart() {
        $('#cartDrawer').toggleClass('open');
        $('#backdrop').toggle();
        
        if ($('#cartDrawer').hasClass('open')) {
            // Re-initialize select2 with proper parent when drawer opens
            $('.select2-mobile').select2({
                placeholder: "Type Room or Name...",
                dropdownParent: $('#cartDrawer'),
                allowClear: true
            });
        }
    }

    function fastAdd(type, data, optName = '', optPrice = 0, method = '') {
        console.log('fastAdd called:', {type, data, optName, optPrice, method});
        
        const cartId = type === 'food' ? `f_${data.id}` : `d_${data.variant_id}_${method}`;
        const existing = posItems.find(i => i.cartId === cartId);
        
        if (existing) {
            existing.qty++;
        } else {
            const newItem = {
                cartId,
                id: data.id,
                name: type === 'food' ? data.name : `${data.name} (${optName})`,
                price: type === 'food' ? data.price : optPrice,
                qty: 1,
                isFood: type === 'food',
                variantId: type === 'food' ? null : data.variant_id,
                productId: type === 'food' ? null : data.id,
                method: type === 'food' ? null : method,
                note: ''
            };
            console.log('Adding new item to cart:', newItem);
            posItems.push(newItem);
        }
        renderCart();
        
        // Visual Feedback
        Swal.fire({
            toast: true, position: 'top', timer: 1000, showConfirmButton: false,
            icon: 'success', title: 'Added: ' + data.name, background: '#2d3436', color: '#fff'
        });
    }

    function renderCart() {
        const list = $('#cartList');
        list.empty();
        
        let total = 0;
        let count = 0;

        posItems.forEach((item, idx) => {
            total += (item.price * item.qty);
            count += item.qty;
            list.append(`
                <div class="mb-3 p-3 bg-light rounded" style="border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="flex: 1">
                            <span class="d-block font-weight-bold" style="font-size: 0.9rem;">${item.name}</span>
                            <span class="text-primary font-weight-bold">${item.price.toLocaleString()} TSH</span>
                        </div>
                        <div class="qty-box">
                            <i class="fa fa-minus-circle fa-lg text-muted" onclick="changeQty(${idx}, -1)"></i>
                            <span class="font-weight-bold">${item.qty}</span>
                            <i class="fa fa-plus-circle fa-lg text-primary" onclick="changeQty(${idx}, 1)"></i>
                        </div>
                    </div>
                    <input type="text" class="form-control form-control-sm mt-2" placeholder="Item note..." 
                        value="${item.note}" onchange="posItems[${idx}].note = this.value"
                        style="border-radius: 10px; border: 1px dashed #ccc; font-size: 0.8rem;">
                </div>
            `);
        });

        $('#floatTotal, #drawerTotal').text(total.toLocaleString() + ' TSH');
        $('#cartCount').text(count);
        $('#btnPlaceOrder').prop('disabled', posItems.length === 0);
    }

    function changeQty(idx, delta) {
        posItems[idx].qty += delta;
        if (posItems[idx].qty <= 0) posItems.splice(idx, 1);
        renderCart();
    }

    function submitOrder() {
        const bookingId = $('#booking_id').val();
        const walkIn = $('#walk_in_name').val();
        
        if (curGuest === 'resident' && !bookingId) return Swal.fire("Required", "Please select a room guest.", "warning");
        // Removed mandatory walk-in name check

        const flow = $('#payment_flow').val();
        
        Swal.fire({
            title: "Confirm Order",
            text: "Order will be sent for preparation and added to guest usage.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: '#e77a31',
            confirmButtonText: "YES, SEND TO PREPARATION"
        }).then((res) => {
            if (res.isConfirmed) {
                Swal.fire({ title: "Sending...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                
                const now = new Date();
                const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                const finalWalkInName = walkIn || 'Walk-in (' + timeStr + ')';

                const payload = {
                    _token: "{{ csrf_token() }}",
                    order_type: curGuest,
                    booking_id: bookingId,
                    walk_in_name: finalWalkInName,
                    items: posItems,
                    payment_status: 'pending', 
                    payment_intent: 'later', 
                };
                
                console.log('Submitting order with payload:', payload);
                console.log('Items being sent:', JSON.stringify(posItems, null, 2));

                $.ajax({
                    url: "{{ route('waiter.order.store') }}",
                    method: "POST",
                    data: payload,
                    success: function(r) {
                        if (r.success) {
                            Swal.fire("Sent!", r.message, "success").then(() => {
                                window.location.href = "{{ route('waiter.orders') }}";
                            });
                        }
                    },
                    error: function(e) {
                        Swal.fire("Error", "Check your inputs or network.", "error");
                    }
                });
            }
        });
    }

    function printGuestBill() {
        const bid = $('#booking_id').val();
        if (!bid) return;
        const url = `/customer/bookings/${bid}/checkout-bill`;
        window.open(url, 'BillPrint', 'width=800,height=600');
    }
</script>
@endsection

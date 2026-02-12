@extends('dashboard.layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #e77a31;
        --primary-light: #fff3e0;
        --primary-dark: #cc6a27;
        --text-dark: #2d3436;
        --text-muted: #636e72;
    }

    /* Main Navigation Tabs */
    .main-nav-tabs {
        border-bottom: 2px solid #eee;
        margin-bottom: 30px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .main-nav-tabs .nav-link {
        border: none;
        background: none;
        color: var(--text-muted);
        font-size: 1.25rem;
        font-weight: 800;
        padding: 12px 45px;
        position: relative;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .main-nav-tabs .nav-link.active {
        color: var(--primary-color);
    }
    .main-nav-tabs .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 4px;
        background: var(--primary-color);
        border-radius: 10px;
        transition: width 0.3s;
    }
    .main-nav-tabs .nav-link.active::after {
        width: 100%;
    }

    /* Sub-category Navigation (for Drinks) */
    .sub-cat-wrapper {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 5px 25px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .sub-cat-wrapper::-webkit-scrollbar { display: none; }
    
    .sub-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 22px;
        background: white;
        border: 2px solid #f0f0f0;
        border-radius: 50px;
        color: var(--text-muted);
        font-weight: 700;
        cursor: pointer;
        margin-right: 12px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .sub-cat-pill:hover {
        transform: translateY(-2px);
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    .sub-cat-pill.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 10px 20px rgba(231, 122, 49, 0.3);
    }

    /* Search Bar */
    .search-box {
        position: relative;
        margin-bottom: 30px;
    }
    .search-box i {
        position: absolute;
        left: 25px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        font-size: 1.2rem;
    }
    .search-box input {
        width: 100%;
        padding: 18px 20px 18px 65px;
        border-radius: 20px;
        border: 2px solid #eee;
        font-size: 1.1rem;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
    }
    .search-box input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 10px 25px rgba(231, 122, 49, 0.1);
    }

    /* Menu Cards */
    .menu-card {
        border: none;
        border-radius: 25px;
        overflow: hidden;
        background: white;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .menu-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }
    .card-image-holder {
        height: 160px;
        position: relative;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .card-image-holder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .menu-card:hover .card-image-holder img {
        transform: scale(1.1);
    }
    
    .category-label {
        position: absolute;
        top: 20px;
        left: 20px;
        padding: 6px 15px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(5px);
        color: var(--text-dark);
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .card-body {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .item-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 10px;
        line-height: 1.2;
    }

    /* Selling Options Area */
    .options-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: auto;
    }

    .option-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: var(--primary-light);
        border: 1.5px solid var(--primary-color);
        border-radius: 12px;
        transition: all 0.2s;
    }

    .option-info {
        display: flex;
        flex-direction: column;
    }
    .option-type {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }
    .option-price {
        font-size: 1rem;
        font-weight: 900;
        color: var(--primary-dark);
    }

    .add-btn-small {
        background: var(--primary-color);
        color: white;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 4px 8px rgba(231, 122, 49, 0.2);
    }
    .add-btn-small:hover {
        background: var(--primary-dark);
        transform: scale(1.1);
        color: white;
    }

    /* Food Specific Price */
    .food-price-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding: 8px 12px;
        background: var(--primary-light);
        border: 1.5px solid var(--primary-color);
        border-radius: 12px;
    }
    .food-price {
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--primary-color);
    }

    /* Floating Cart FAB */
    #cartFAB {
        position: fixed;
        bottom: 40px;
        right: 40px;
        width: 75px;
        height: 75px;
        border-radius: 25px;
        background: var(--primary-color);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        cursor: pointer;
        box-shadow: 0 15px 35px rgba(231, 122, 49, 0.4);
        transition: all 0.3s cubic-bezier(0.68, -0.6, 0.32, 1.6);
    }
    #cartFAB:hover { transform: scale(1.1) rotate(-5deg); }
    #trayCount {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #d63031;
        color: white;
        min-width: 30px;
        height: 30px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 900;
        border: 3px solid white;
    }

    /* Tray Modal Customization */
    .modal-content {
        border-radius: 35px;
        border: none;
    }
    .tray-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 20px;
        margin-bottom: 12px;
    }
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        background: white;
        padding: 5px 12px;
        border-radius: 12px;
        border: 1.5px solid #eee;
    }
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .app-title h1 { font-size: 1.5rem; }
        .main-nav-tabs .nav-link { padding: 10px 15px; font-size: 1rem; }
        .card-image-holder { height: 120px; }
        .item-title { font-size: 0.9rem; margin-bottom: 8px; }
        .option-row { padding: 6px 8px; border-radius: 10px; }
        .option-type { font-size: 0.65rem; }
        .option-price { font-size: 0.85rem; }
        .add-btn-small { width: 28px; height: 28px; border-radius: 8px; }
        .food-price { font-size: 0.95rem; }
        .btn-food-add { padding: 6px 12px !important; font-size: 0.8rem !important; }
        .search-box input { padding: 14px 15px 14px 50px; font-size: 0.95rem; }
        #cartFAB { right: 20px; bottom: 20px; width: 60px; height: 60px; border-radius: 20px; }
    }
</style>

<div class="app-title">
    <div>
        <h1 style="color: var(--primary-color);"><i class="fa fa-cutlery"></i> Gourmet Dining</h1>
        <p>Expertly crafted food & drinks delivered to your room</p>
    </div>
</div>

<!-- Main Tabs -->
<ul class="nav main-nav-tabs" id="mainTabs">
  <li class="nav-item">
    <a class="nav-link active" id="drinks-tab" data-toggle="tab" href="#drinks-section">Drinks & Bar</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="food-tab" data-toggle="tab" href="#food-section">Food & Kitchen</a>
  </li>
</ul>

<!-- Shared Search -->
<div class="search-box">
    <i class="fa fa-search"></i>
    <input type="text" id="globalSearch" placeholder="What are you craving today?">
</div>

<div class="tab-content" id="mainContent">
    <!-- DRINKS SECTION -->
    <div class="tab-pane fade show active" id="drinks-section">
        @php
            $drinkSubCats = [
                'all' => ['label' => 'All Drinks', 'icon' => 'fa-th-large'],
                'spirits' => ['label' => 'Spirits', 'icon' => 'fa-beer'],
                'alcoholic_beverage' => ['label' => 'Beers', 'icon' => 'fa-beer'],
                'wines' => ['label' => 'Wines', 'icon' => 'fa-glass'],
                'non_alcoholic_beverage' => ['label' => 'Sodas', 'icon' => 'fa-coffee'],
                'water' => ['label' => 'Water', 'icon' => 'fa-tint'],
                'juices' => ['label' => 'Juices', 'icon' => 'fa-lemon-o'],
                'cocktails' => ['label' => 'Cocktails', 'icon' => 'fa-glass'],
                'energy_drinks' => ['label' => 'Energy', 'icon' => 'fa-bolt'],
            ];
        @endphp

        <div class="sub-cat-wrapper">
            @foreach($drinkSubCats as $key => $cat)
                <div class="sub-cat-pill {{ $key === 'all' ? 'active' : '' }}" onclick="filterDrinks('{{ $key }}', this)">
                    <i class="fa {{ $cat['icon'] }}"></i> {{ $cat['label'] }}
                </div>
            @endforeach
        </div>

        <div class="row px-2" id="drinksGrid">
            @foreach($drinks as $drink)
                <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3 px-1 drink-item-card" data-category="{{ $drink->category }}" data-name="{{ strtolower($drink->name) }}">
                    <div class="card menu-card shadow-sm">
                        <div class="card-image-holder">
                            {{-- <span class="category-label">{{ ucfirst(str_replace('_', ' ', $drink->category)) }}</span> --}}
                            @if(!($drink->in_stock ?? true))
                                <div class="out-of-stock-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; z-index: 2;">
                                    <span class="badge badge-danger" style="font-size: 14px; padding: 8px 15px; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">Out of Stock</span>
                                </div>
                            @endif
                            @if(isset($drink->image) && $drink->image)
                                <img src="{{ asset('storage/' . $drink->image) }}" alt="{{ $drink->name }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($drink->name) }}&background=fff3e0&color=e77a31&size=200'" style="{{ !($drink->in_stock ?? true) ? 'filter: grayscale(1) blur(2px);' : '' }}">
                            @else
                                <div class="opacity-25"><i class="fa fa-glass fa-5x"></i></div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="item-title">{{ $drink->name }}</h5>
                            
                            <div class="options-container">
                                @foreach($drink->options as $opt)
                                    @php
                                        $isDisabled = !($drink->in_stock ?? true);
                                        // Specific deactivation: if it's a Bottle (pic) but we have less than 1.0 bottles left
                                        if (!$isDisabled && $opt['method'] === 'pic' && ($drink->current_stock ?? 0) < 0.99) {
                                            $isDisabled = true;
                                        }
                                    @endphp
                                    <div class="option-row">
                                        <div class="option-info">
                                            <span class="option-type">{{ $opt['type'] }}</span>
                                            <span class="option-price">{{ number_format($opt['price']) }} <small>TSH</small></span>
                                        </div>
                                        <button 
                                            onclick="addToTrayFromOptions({{ $drink->variant_id ?? 0 }}, '{{ $drink->name }}', '{{ $opt['type'] }}', {{ $opt['price'] }}, '{{ $opt['method'] }}', {{ $drink->id }}, {{ $drink->current_stock ?? 0 }}, {{ $drink->servings_per_pic ?? 1 }})" 
                                            class="add-btn-small"
                                            {{ $isDisabled ? 'disabled style=opacity:0.3;cursor:not-allowed;' : '' }}>
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- FOOD SECTION -->
    <div class="tab-pane fade" id="food-section">
        <div class="row px-2" id="foodGrid">
            @foreach($foodItems as $food)
                <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3 px-1 food-item-card" data-name="{{ strtolower($food['name']) }}">
                    <div class="card menu-card shadow-sm">
                        <div class="card-image-holder">
                            <span class="category-label">{{ $food['category'] }}</span>
                            @if(isset($food['image']) && $food['image'])
                                <img src="{{ Storage::url($food['image']) }}" alt="{{ $food['name'] }}">
                            @else
                                <div class="opacity-25"><i class="fa fa-cutlery fa-5x"></i></div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="item-title">{{ $food['name'] }}</h5>
                            <p class="text-muted small mb-3">{{ Str::limit($food['description'], 60) }}</p>
                            
                            <div class="food-price-area">
                                <span class="food-price">{{ number_format($food['price']) }} <small>TSH</small></span>
                                <button onclick="addFoodToTray({{ $food['id'] }}, '{{ $food['name'] }}', {{ $food['price'] }})" class="btn btn-food-add" style="background: var(--primary-color); color: white; border-radius: 12px; padding: 8px 15px; font-weight: 800;">
                                     <i class="fa fa-plus mr-2"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<div id="cartFAB" onclick="openTray()">
    <i class="fa fa-shopping-basket fa-2x text-white"></i>
    <span id="trayCount">0</span>
</div>

<!-- Tray Modal -->
<div class="modal fade" id="trayModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 px-4 pt-4">
                <h3 class="modal-title font-weight-bold" style="color: var(--primary-color);">Your Tray</h3>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body px-4">
                <div id="trayList"></div>
                
                <!-- Items will be listed here with individual notes -->

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h5 font-weight-bold text-muted">Total Amount</span>
                        <span class="h3 font-weight-bold" id="trayTotal" style="color: var(--primary-color);">0 TSH</span>
                    </div>
                    <button class="btn btn-block btn-lg py-3 text-white font-weight-bold" style="background: var(--primary-color); border-radius: 20px; font-size: 1.2rem;" onclick="processOrderCheckout()">
                        Complete Order
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 p-3"></div>
        </div>
    </div>
</div>

<input type="hidden" id="booking_id" value="{{ $activeBooking->id ?? '' }}">
@endsection

@section('scripts')
<script>
    let tray = [];
    let currentDrinkFilter = 'all';

    // Global Search Logic
    document.getElementById('globalSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        
        // Filter Drinks by Search + Category
        document.querySelectorAll('.drink-item-card').forEach(card => {
            const name = card.getAttribute('data-name');
            const cat = card.getAttribute('data-category');
            const matchesSearch = name.includes(term);
            const matchesCat = currentDrinkFilter === 'all' || cat === currentDrinkFilter;
            card.style.display = (matchesSearch && matchesCat) ? '' : 'none';
        });

        // Filter Food by Search
        document.querySelectorAll('.food-item-card').forEach(card => {
            const name = card.getAttribute('data-name');
            card.style.display = name.includes(term) ? '' : 'none';
        });
    });

    // Drink Sub-category Filter
    function filterDrinks(cat, el) {
        currentDrinkFilter = cat;
        document.querySelectorAll('.sub-cat-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        
        const term = document.getElementById('globalSearch').value.toLowerCase();
        document.querySelectorAll('.drink-item-card').forEach(card => {
            const name = card.getAttribute('data-name');
            const cardCat = card.getAttribute('data-category');
            card.style.display = (name.includes(term) && (cat === 'all' || cardCat === cat)) ? '' : 'none';
        });
    }

    // Add Drink Option to Tray
    function addToTrayFromOptions(variantId, name, typeName, price, method, productId, currentStock, servingsPerPic) {
        if (!document.getElementById('booking_id').value) {
            Swal.fire("Check-in Required", "Please ensure your room check-in is complete before ordering.", "warning");
            return;
        }

        // Stock validation: Calculate current tray consumption for this variant
        let consumption = method === 'pic' ? 1 : (1 / servingsPerPic);
        let currentTrayPICs = 0;
        tray.forEach(item => {
            if (item.variantId === variantId) {
                let itemCons = item.method === 'pic' ? 1 : (1 / item.servingsPerPic);
                currentTrayPICs += (item.qty * itemCons);
            }
        });

        if (currentTrayPICs + consumption > currentStock + 0.001) { // small epsilon for float
            Swal.fire({
                icon: 'error',
                title: 'Stock limit reached',
                text: `Sorry, there only ${currentStock} bottles available for ${name}.`,
                confirmButtonColor: '#e77a31'
            });
            return;
        }

        const trayId = `d_${productId}_${variantId}_${method}`;
        const displayName = `${name} (${typeName})`;
        
        const existing = tray.find(i => i.trayId === trayId);
        if (existing) {
            existing.qty++;
        } else {
            tray.push({
                trayId, productId, variantId, qty: 1, price, method, 
                name: displayName, isFood: false, currentStock, servingsPerPic,
                note: ''
            });
        }
        
        refreshTrayUI();
        notifyAdd(displayName);
    }

    // Add Food to Tray
    function addFoodToTray(id, name, price) {
        if (!document.getElementById('booking_id').value) {
            Swal.fire("Check-in Required", "Please ensure your room check-in is complete.", "warning");
            return;
        }

        const trayId = `f_${id}`;
        const existing = tray.find(i => i.trayId === trayId);
        if (existing) {
            existing.qty++;
        } else {
            tray.push({
                trayId, id, name, price, qty: 1, isFood: true,
                note: ''
            });
        }
        
        refreshTrayUI();
        notifyAdd(name);
    }

    function refreshTrayUI() {
        const list = document.getElementById('trayList');
        const countBadge = document.getElementById('trayCount');
        const totalDisp = document.getElementById('trayTotal');
        const fab = document.getElementById('cartFAB');
        
        let totalItems = 0;
        let totalCash = 0;
        list.innerHTML = '';
        
        tray.forEach((item, idx) => {
            totalItems += item.qty;
            totalCash += (item.price * item.qty);
            
            list.innerHTML += `
                <div class="tray-item" style="flex-direction: column; align-items: stretch;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-dark" style="font-size: 1.05rem;">${item.name}</div>
                            <div class="text-primary font-weight-bold">${item.price.toLocaleString()} <small>TSH</small></div>
                        </div>
                        <div class="qty-controls">
                            <button class="btn btn-link p-0 text-muted" onclick="changeTrayQty(${idx}, -1)"><i class="fa fa-minus-circle"></i></button>
                            <span class="font-weight-bold" style="min-width: 20px; text-align: center;">${item.qty}</span>
                            <button class="btn btn-link p-0 text-primary" onclick="changeTrayQty(${idx}, 1)"><i class="fa fa-plus-circle"></i></button>
                        </div>
                    </div>
                    <div class="item-note-container">
                        <input type="text" class="form-control form-control-sm" 
                            placeholder="Add a note (e.g. Extra ice, No onions...)" 
                            value="${item.note || ''}" 
                            onchange="updateItemNote(${idx}, this.value)"
                            style="border-radius: 10px; background: #fff; border: 1px solid #ddd; font-size: 0.85rem;">
                    </div>
                </div>
            `;
        });
        
        countBadge.innerText = totalItems;
        totalDisp.innerText = totalCash.toLocaleString() + ' TSH';
        fab.style.display = tray.length > 0 ? 'flex' : 'none';
    }

    function updateItemNote(idx, note) {
        if (tray[idx]) {
            tray[idx].note = note;
        }
    }

    function changeTrayQty(idx, amt) {
        const item = tray[idx];
        if (amt > 0 && !item.isFood) {
            // Re-validate stock for tray increases
            let consumption = item.method === 'pic' ? 1 : (1 / item.servingsPerPic);
            let currentTrayPICs = 0;
            tray.forEach(i => {
                if (i.variantId === item.variantId) {
                    let iCons = i.method === 'pic' ? 1 : (1 / i.servingsPerPic);
                    currentTrayPICs += (i.qty * iCons);
                }
            });

            if (currentTrayPICs + consumption > item.currentStock + 0.001) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock limit reached',
                    text: `Sorry, only ${item.currentStock} units available.`,
                    confirmButtonColor: '#e77a31'
                });
                return;
            }
        }
        
        item.qty += amt;
        if (item.qty <= 0) tray.splice(idx, 1);
        refreshTrayUI();
        if (tray.length === 0) $('#trayModal').modal('hide');
    }

    function notifyAdd(name) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: 'success',
            title: `Added to tray`,
            text: name,
            background: '#2d3436',
            color: '#fff',
            iconColor: '#e77a31'
        });
    }

    function openTray() { $('#trayModal').modal('show'); }

    async function processOrderCheckout() {
        if (tray.length === 0) return;
        
        const result = await Swal.fire({
            title: "Ready to Order?",
            text: "This will be charged to your room account. Our staff will prioritize your delivery.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Place Order",
            confirmButtonColor: '#e77a31',
            cancelButtonColor: '#96a5a6'
        });

        if (result.isConfirmed) {
            Swal.fire({ title: "Sending...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            const bookingId = document.getElementById('booking_id').value;
            let successHits = 0;
            for (let item of tray) {
                const payload = {
                    booking_id: bookingId,
                    quantity: item.qty,
                    guest_request: item.note || ''
                };

                if (item.isFood) {
                    payload.service_id = 4; // Generic Food Order (ID from DB)
                    payload.service_specific_data = { food_id: item.id };
                } else {
                    payload.service_id = 3; // Generic Bar Order (ID from DB)
                    payload.product_id = item.productId;
                    payload.product_variant_id = item.variantId;
                    payload.selling_method = item.method; 
                }

                try {
                    const response = await fetch('{{ route("customer.services.request") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();
                    if (data.success) successHits++;
                } catch (err) { console.error(err); }
            }

            if (successHits > 0) {
                await Swal.fire({
                    title: "Bon Appétit!",
                    html: `
                        <p>Your order is being prepared and will be delivered shortly.</p>
                    `,
                    icon: "success",
                    confirmButtonColor: '#e77a31'
                });
                tray = [];
                window.location.reload();
            }
        }
    }
</script>
@endsection

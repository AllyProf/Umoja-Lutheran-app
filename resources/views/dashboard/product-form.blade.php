@extends('dashboard.layouts.app')

@section('content')
    @php
        $isEdit = isset($product);
        $userRole = strtolower(Auth::guard('staff')->user()->role ?? 'manager');
        $routePrefix = ($userRole === 'bar_keeper' || $userRole === 'barkeeper' || $userRole === 'bartender') ? 'bar-keeper' : 'admin';

        // If explicitly passed role variable is available, use it to refine
        if (isset($role)) {
            if ($role === 'bar_keeper' || $role === 'barkeeper')
                $routePrefix = 'bar-keeper';
            elseif ($role === 'manager' || $role === 'admin')
                $routePrefix = 'admin';
        }

        $title = $isEdit ? 'Edit Product' : 'Register New Product';
        $action = $isEdit ? route($routePrefix . '.products.update', $product->id) : route($routePrefix . '.products.store');
    @endphp

    <div class="app-title">
        <div>
            <h1><i class="fa fa-cubes"></i> {{ $title }}</h1>
            <p>{{ $isEdit ? 'Update product and packaging details' : 'Register a new product and its packaging/sizes' }}</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route($routePrefix . '.products.index') }}">Products</a></li>
            <li class="breadcrumb-item active"><a href="#">{{ $title }}</a></li>
        </ul>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-11">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- 1. Brand / Family Details -->
                <div class="card shadow-sm mb-4 border-top-primary">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa fa-tag text-primary mr-2"></i> Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="control-label font-weight-bold text-uppercase small text-primary">1.
                                        Select Category <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center">
                                        <select
                                            class="form-control form-control-lg border-primary shadow-sm mr-3 @error('category') is-invalid @enderror"
                                            name="category" id="categorySelect" required onchange="checkFoodCategory()"
                                            style="flex: 1;">
                                            <option value="">Select Category (Drink / Food / Housekeeping)</option>
                                            <option value="vinywaji" {{ (old('category', $product->category ?? '') == 'vinywaji') ? 'selected' : '' }}>Vinywaji (Drinks)</option>
                                            <option value="chakula" {{ (old('category', $product->category ?? '') == 'chakula') ? 'selected' : '' }}>Chakula (Food)</option>
                                            <option value="housekeeping" {{ (old('category', $product->category ?? '') == 'housekeeping') ? 'selected' : '' }}>Housekeeping</option>
                                            <option value="general">General / Shared</option>
                                        </select>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="showManualDept"
                                                onchange="checkFoodCategory()">
                                            <label
                                                class="custom-control-label small font-weight-bold text-muted text-uppercase"
                                                for="showManualDept">Manual Departments</label>
                                        </div>
                                    </div>
                                    @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <!-- Bulk name placeholder for controller -->
                            <input type="hidden" name="name" value="Bulk Registration">
                            <div class="col-md-12" id="returnableSection" style="display: none;">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_returnable"
                                            name="is_returnable" value="1" {{ old('is_returnable', $product->is_returnable ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_returnable">
                                            <i class="fa fa-undo text-info"></i> Is Returnable?
                                            <span class="text-muted small ml-1">(Check if this item is used and then
                                                returned to store, e.g. Brooms, Linens)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea class="form-control" name="description" rows="2"
                                        placeholder="Brief description of this product family...">{{ old('description', $product->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Pricing & Packaging Details -->
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="text-secondary mb-0"><i class="fa fa-money-bill"></i> Pricing & Unit Details</h4>
                </div>

                <div id="variants-container">
                    @if($isEdit && $product->variants->count() > 0)
                        @foreach($product->variants as $index => $variant)
                            @include('dashboard.partials.variant-form-item', ['index' => $index, 'variant' => $variant])
                        @endforeach
                    @else
                        <div class="alert alert-info text-center shadow-sm" id="no-variants-msg">
                            <i class="fa fa-info-circle fa-2x mb-2"></i><br>
                            Start by adding the first variant (e.g. 350ml bottle) below.
                        </div>
                    @endif
                </div>

                <div class="text-right mt-3 mb-4">
                    <button class="btn btn-primary btn-sm shadow-sm rounded-pill px-4" type="button" onclick="addVariant()">
                        <i class="fa fa-plus-circle"></i> Add another Item
                    </button>
                </div>

                <!-- Hidden Departments Section (Handled automatically) -->
                <!-- Departments Section -->
                <div class="card shadow-sm mb-4 border-top-primary mt-4" id="departmentsCard" style="display: none;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa fa-sitemap text-primary mr-2"></i> Departments & Placement</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="text-muted small mb-0">Select which departments this product belongs to and assign a
                            department-specific category.</p>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectAllDepartments()">
                            <i class="fa fa-check-square"></i> Mark as Shared (All)
                        </button>
                    </div>
                    <div class="row">
                        @foreach($departments as $dept)
                            @php
                                $isChecked = false;
                                $assignedCategory = '';
                                if ($isEdit && $product->departments) {
                                    $pivot = $product->departments->firstWhere('id', $dept->id);
                                    if ($pivot) {
                                        $isChecked = true;
                                        $assignedCategory = $pivot->pivot->category;
                                    }
                                }
                            @endphp
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 {{ $isChecked ? 'bg-light border-primary' : '' }}"
                                    id="dept-card-{{ $dept->id }}">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input dept-checkbox"
                                            id="dept_{{ $dept->id }}" name="departments[]" value="{{ $dept->id }}" {{ $isChecked ? 'checked' : '' }} onchange="toggleDeptCategory({{ $dept->id }})">
                                        <label class="custom-control-label font-weight-bold"
                                            for="dept_{{ $dept->id }}">{{ $dept->name }}</label>
                                    </div>
                                    <div id="dept-category-{{ $dept->id }}"
                                        style="display: {{ $isChecked ? 'block' : 'none' }};">
                                        <label class="small text-muted mb-1">Category in {{ $dept->name }}</label>
                                        <select class="form-control form-control-sm"
                                            name="department_categories[{{ $dept->id }}]" {{ $isChecked ? 'required' : '' }}>
                                            <option value="">Select...</option>
                                            @if($dept->code === 'bar')
                                                <option value="alcoholic_beverage" {{ $assignedCategory == 'alcoholic_beverage' ? 'selected' : '' }}>Beers / Ciders</option>
                                                <option value="spirits" {{ $assignedCategory == 'spirits' ? 'selected' : '' }}>
                                                    Spirits</option>
                                                <option value="wines" {{ $assignedCategory == 'wines' ? 'selected' : '' }}>Wines
                                                </option>
                                                <option value="water" {{ $assignedCategory == 'water' ? 'selected' : '' }}>Water
                                                </option>
                                                <option value="non_alcoholic_beverage" {{ $assignedCategory == 'non_alcoholic_beverage' ? 'selected' : '' }}>Soft Drinks
                                                </option>
                                                <option value="cleaning_supplies" {{ $assignedCategory == 'cleaning_supplies' ? 'selected' : '' }}>Housekeeping
                                                </option>
                                                <option value="juices" {{ $assignedCategory == 'juices' ? 'selected' : '' }}>
                                                    Juices</option>
                                            @elseif($dept->code === 'kitchen')
                                                <option value="food" {{ $assignedCategory == 'food' ? 'selected' : '' }}>General
                                                    Food</option>
                                                <option value="meat_poultry" {{ $assignedCategory == 'meat_poultry' ? 'selected' : '' }}>Meat & Poultry</option>
                                                <option value="seafood" {{ $assignedCategory == 'seafood' ? 'selected' : '' }}>
                                                    Seafood & Fish</option>
                                                <option value="vegetables" {{ $assignedCategory == 'vegetables' ? 'selected' : '' }}>
                                                    Vegetables & Fruits</option>
                                                <option value="dairy" {{ $assignedCategory == 'dairy' ? 'selected' : '' }}>Dairy &
                                                    Eggs</option>
                                                <option value="pantry_baking" {{ $assignedCategory == 'pantry_baking' ? 'selected' : '' }}>Pantry & Baking</option>
                                                <option value="spices_herbs" {{ $assignedCategory == 'spices_herbs' ? 'selected' : '' }}>Spices & Herbs</option>
                                                <option value="oils_fats" {{ $assignedCategory == 'oils_fats' ? 'selected' : '' }}>
                                                    Oils & Fats</option>
                                            @elseif($dept->code === 'housekeeping')
                                                <option value="cleaning_supplies" {{ $assignedCategory == 'cleaning_supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                                                <option value="linens" {{ $assignedCategory == 'linens' ? 'selected' : '' }}>
                                                    Linens / Towels</option>
                                                <option value="amenities" {{ $assignedCategory == 'amenities' ? 'selected' : '' }}>
                                                    Guest Amenities</option>
                                            @else
                                                <option value="other" {{ $assignedCategory == 'other' ? 'selected' : '' }}>Other
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
        </div>

        <div class="card bg-light border-0 mt-4 mb-5">
            <div class="card-body text-center">
                <button class="btn btn-success btn-lg px-5 icon-btn shadow" type="submit">
                    <i class="fa fa-check-circle"></i> {{ $isEdit ? 'Update Products' : 'Save Products' }}
                </button>
                <a class="btn btn-outline-secondary btn-lg ml-3" href="{{ route($routePrefix . '.products.index') }}">
                    <i class="fa fa-times"></i> Cancel
                </a>
            </div>
        </div>
        </form>
        <div class="text-center pb-5">
            <span class="text-muted small">v2.1-beverage-ratio-fix</span>
        </div>
    </div>
    </div>

    <!-- Template for New Variant -->
    <template id="variant-template">
        <div class="variant-card card shadow-sm mb-4 border-left-info animate-fade-in">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 variant-header"
                style="display: none !important;">
                <div>
                    <span class="badge badge-info mr-2">New</span>
                    <strong class="text-primary">Product Unit Configuration</strong>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1"
                    style="width: 30px; height: 30px;" onclick="removeVariant(this)" title="Remove">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Left Column: Basic Info -->
                    <div class="col-md-7 border-right">
                        <div class="form-row">
                            <div class="col-md-12 form-group variant-name-field">
                                <label class="small font-weight-bold text-primary text-uppercase">Item Name / Size</label>
                                <input type="text" class="form-control variant-name-input font-weight-bold"
                                    name="variants[INDEX][name]" placeholder="e.g. Salt, Sugar 1kg, Soda 500ml" required>
                                <small class="text-muted">Enter the full name for this specific item.</small>
                            </div>

                            <div class="registration-units-section bg-light p-3 rounded mb-3"
                                style="border-left: 4px solid #4e73df;">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="small font-weight-bold text-dark text-uppercase">Purchasing Unit (e.g.
                                            Crate/Carton)</label>
                                        <select class="form-control purchasing-unit-select"
                                            name="variants[INDEX][purchasing_unit]" required>
                                            <option value="Crate">Crate</option>
                                            <option value="Carton">Carton</option>
                                            <option value="Box">Box</option>
                                            <option value="Pack">Pack</option>
                                            <option value="Bag">Bag</option>
                                            <option value="Tray">Tray</option>
                                            <option value="Sado">Sado</option>
                                            <option value="Debe">Debe</option>
                                            <option value="Kiroba">Kiroba</option>
                                            <option value="Dozen">Dozen</option>
                                            <option value="Pcs">Piece (Pcs)</option>
                                            <option value="Kg">Kg</option>
                                            <option value="Grams">Grams (g)</option>
                                            <option value="Litres">Litres (L)</option>
                                            <option value="ml">ml</option>
                                            <option value="Bucket">Bucket (Ndoo)</option>
                                            <option value="Bundle">Bundle (Funga)</option>
                                            <option value="Packet">Packet</option>
                                            <option value="Bottle">Bottle</option>
                                            <option value="Glass">Glass</option>
                                            <option value="Shot">Shot / Tot</option>
                                            <option value="Bunch">Bunch</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="small font-weight-bold text-dark text-uppercase">Receiving Unit (e.g.
                                            Bottle/Piece)</label>
                                        <select class="form-control receiving-unit-select"
                                            name="variants[INDEX][receiving_unit]" required>
                                            <option value="Crate">Crate</option>
                                            <option value="Carton">Carton</option>
                                            <option value="Box">Box</option>
                                            <option value="Pack">Pack</option>
                                            <option value="Bag">Bag</option>
                                            <option value="Tray">Tray</option>
                                            <option value="Sado">Sado</option>
                                            <option value="Debe">Debe</option>
                                            <option value="Kiroba">Kiroba</option>
                                            <option value="Dozen">Dozen</option>
                                            <option value="Pcs">Piece (Pcs)</option>
                                            <option value="Kg">Kg</option>
                                            <option value="Grams">Grams (g)</option>
                                            <option value="Litres">Litres (L)</option>
                                            <option value="ml">ml</option>
                                            <option value="Bucket">Bucket (Ndoo)</option>
                                            <option value="Bundle">Bundle (Funga)</option>
                                            <option value="Packet">Packet</option>
                                            <option value="Bottle">Bottle</option>
                                            <option value="Glass">Glass</option>
                                            <option value="Shot">Shot / Tot</option>
                                            <option value="Bunch">Bunch</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Ratio (Shown only for Vinywaji) -->
                                <div class="ratio-entry-section mt-2 border-top pt-2" style="display: none;">
                                    <label class="small font-weight-bold text-primary text-uppercase">
                                        Number of <span class="servings-unit-label">Bottle</span>s in 1 <span
                                            class="purchase-unit-label">Crate</span>
                                    </label>
                                    <input type="number" class="form-control items-per-package-input"
                                        name="variants[INDEX][items_per_package]" value="1" step="any">
                                </div>
                            </div>

                            <!-- Pricing Section -->
                            <div class="selling-type-section border p-3 rounded">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="small font-weight-bold text-dark text-uppercase">Buying Price</label>
                                        <input type="number" step="any" class="form-control font-weight-bold"
                                            name="variants[INDEX][buying_price]" placeholder="TZS (Optional)">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="small font-weight-bold text-dark text-uppercase">Selling Price</label>
                                        <input type="number" step="any" class="form-control font-weight-bold text-primary"
                                            name="variants[INDEX][selling_price_per_pic]" placeholder="TZS (Optional)">
                                    </div>
                                    <div class="col-md-4 form-group drink-only-section serving-price-field"
                                        style="display: none;">
                                        <label class="small font-weight-bold text-dark text-uppercase">Glass/Shot
                                            Price</label>
                                        <input type="number" step="any" class="form-control font-weight-bold text-info"
                                            name="variants[INDEX][selling_price_per_serving]" placeholder="TZS (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Image & Pricing -->
                <div class="col-md-5">
                    <div class="row h-100">
                        <!-- Product Image -->
                        <div class="col-md-12 text-center">
                            <label class="small font-weight-bold text-muted d-block mb-2">IMAGE (OPTIONAL)</label>
                            <div class="image-upload-wrapper">
                                <div class="preview-box mx-auto mb-2 shadow-sm rounded overflow-hidden"
                                    style="width: 120px; height: 120px; background: #fff; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center;">
                                    <img class="img-preview" src="{{ asset('dashboard_assets/img/no-image.png') }}"
                                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                                <label class="btn btn-sm btn-outline-primary btn-file">
                                    <i class="fa fa-camera"></i> Change <input type="file" style="display: none;"
                                        name="variants[INDEX][image]" accept="image/*" onchange="previewVariantImage(this)">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </template>

    <style>
        .border-top-primary {
            border-top: 5px solid #4e73df !important;
        }

        .form-control-lg {
            border-radius: 8px;
            font-size: 1.1rem;
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .bg-light {
            background-color: #f8f9fc !important;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        let variantCount = {{ $isEdit ? $product->variants->count() : 0 }};

        function addVariant() {
            const container = document.getElementById('variants-container');
            const template = document.getElementById('variant-template').innerHTML;
            const newHtml = template.replace(/INDEX/g, variantCount);

            const div = document.createElement('div');
            div.innerHTML = newHtml;
            const card = div.firstElementChild;
            container.appendChild(card);

            variantCount++;
            const msg = document.getElementById('no-variants-msg');
            if (msg) msg.style.display = 'none';

            checkFoodCategory();
        }

        function removeVariant(btn) {
            if (confirm('Remove this variant?')) {
                btn.closest('.variant-card').remove();
            }
        }


        function previewVariantImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    input.closest('.image-upload-wrapper').querySelector('.img-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Auto-update first variant name to 'Standard' if brand is typed and variant is empty
        document.getElementById('brandNameInput').addEventListener('input', function () {
            const brandName = this.value;
            const firstVariantInput = document.querySelector('.variant-name-input');
            if (firstVariantInput && (firstVariantInput.value.trim() === '' || firstVariantInput.value === 'Standard')) {
                if (brandName.trim() !== '') {
                    firstVariantInput.value = 'Standard';
                } else {
                    firstVariantInput.value = '';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (variantCount === 0) {
                addVariant(); // Add default variant
            }
        });

        function selectAllDepartments() {
            document.querySelectorAll('.dept-checkbox').forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    toggleDeptCategory(checkbox.value);
                }
            });
        }

        function toggleDeptCategory(deptId) {
            const checkbox = document.getElementById('dept_' + deptId);
            const card = document.getElementById('dept-card-' + deptId);
            const categoryDiv = document.getElementById('dept-category-' + deptId);
            const select = categoryDiv.querySelector('select');

            if (checkbox.checked) {
                card.classList.add('bg-light', 'border-primary');
                categoryDiv.style.display = 'block';
                select.setAttribute('required', 'required');
            } else {
                card.classList.remove('bg-light', 'border-primary');
                categoryDiv.style.display = 'none';
                select.removeAttribute('required');
                select.value = ''; // Reset selection
            }

            checkFoodCategory();
        }

        function checkFoodCategory() {
            const mainCategory = document.getElementById('categorySelect').value;
            const isBeverage = mainCategory === 'vinywaji';
            const isChakula = mainCategory === 'chakula';
            const isHousekeeping = mainCategory === 'housekeeping';
            const isGeneral = mainCategory === 'general';

            const showManualSwitch = document.getElementById('showManualDept');
            const showManual = showManualSwitch.checked || isGeneral;
            const deptCard = document.getElementById('departmentsCard');

            // 1. Show/Hide manual departments card
            if (deptCard) deptCard.style.display = showManual ? 'block' : 'none';

            // 2. Auto-Department Assignment (Only if NOT manual)
            if (!showManual) {
                const deptCheckboxes = document.querySelectorAll('.dept-checkbox');
                const findDeptByCode = (name) => {
                    let found = null;
                    deptCheckboxes.forEach(cb => {
                        const label = cb.nextElementSibling.textContent.toLowerCase();
                        if (label.includes(name)) found = cb;
                    });
                    return found;
                };

                const barCb = findDeptByCode('bar');
                const kitchenCb = findDeptByCode('kitchen');
                const hkCb = findDeptByCode('housekeeping');

                // Reset all
                document.querySelectorAll('.dept-checkbox').forEach(cb => {
                    cb.checked = false;
                    toggleDeptCategory(cb.value);
                });

                if (isBeverage && barCb) {
                    barCb.checked = true;
                    toggleDeptCategory(barCb.value);
                    const sel = document.querySelector('select[name="department_categories[' + barCb.value + ']"]');
                    if (sel) sel.value = 'non_alcoholic_beverage';
                } else if (isChakula && kitchenCb) {
                    kitchenCb.checked = true;
                    toggleDeptCategory(kitchenCb.value);
                    const sel = document.querySelector('select[name="department_categories[' + kitchenCb.value + ']"]');
                    if (sel) sel.value = 'food';
                } else if (isHousekeeping && hkCb) {
                    hkCb.checked = true;
                    toggleDeptCategory(hkCb.value);
                    const sel = document.querySelector('select[name="department_categories[' + hkCb.value + ']"]');
                    if (sel) sel.value = 'cleaning_supplies';
                }
            }

            // 3. Show/Hide Returnable Section
            const returnableSection = document.getElementById('returnableSection');
            if (returnableSection) {
                if (isHousekeeping || isGeneral) {
                    returnableSection.style.display = 'block';
                } else {
                    returnableSection.style.display = 'none';
                    const retCheckbox = document.getElementById('is_returnable');
                    if (retCheckbox) retCheckbox.checked = false;
                }
            }

            document.querySelectorAll('.variant-card').forEach(card => {
                const ratioSection = card.querySelector('.ratio-entry-section');
                const ratioInput = card.querySelector('.items-per-package-input');
                const servingPriceField = card.querySelector('.serving-price-field');
                const receivingSelect = card.querySelector('.receiving-unit-select');

                // 1. Ratio ONLY for Beverages (Drinks)
                if (ratioSection) {
                    const showManual = document.getElementById('showManualDept').checked;
                    const deptCard = document.getElementById('departmentsCard');

                    if (showManual || category === 'general') {
                        deptCard.style.display = 'block';
                    } else {
                        deptCard.style.display = 'none';
                    }

                    if (isBeverage && !showManual) {
                        ratioSection.style.setProperty('display', 'block', 'important');
                        if (ratioInput && (ratioInput.value === '1' || ratioInput.value === '')) ratioInput.value = '24';
                    } else {
                        ratioSection.style.display = 'none';
                        if (ratioInput) ratioInput.value = '1';
                    }
                }

                // 2. Pricing details based on Category
                if (isHousekeeping) {
                    // Hide pricing section if needed? User didn't ask for this, but usually HK items have value
                    card.querySelectorAll('.drink-only-section').forEach(el => el.style.display = 'none');
                } else if (isBeverage) {
                    card.querySelectorAll('.drink-only-section').forEach(el => el.style.setProperty('display', 'block', 'important'));
                    if (servingPriceField) servingPriceField.style.setProperty('display', 'block', 'important');
                } else {
                    // Chakula/Food
                    card.querySelectorAll('.drink-only-section').forEach(el => el.style.display = 'none');
                }

                // 3. Update labels dynamically
                const updateLabels = () => {
                    const servingLabel = card.querySelector('.servings-unit-label');
                    const purchaseLabel = card.querySelector('.purchase-unit-label');
                    const receivingSelect = card.querySelector('.receiving-unit-select');
                    const purchasingSelect = card.querySelector('.purchasing-unit-select');

                    if (servingLabel && receivingSelect) {
                        servingLabel.textContent = receivingSelect.value || 'Bottle';
                    }
                    if (purchaseLabel && purchasingSelect) {
                        purchaseLabel.textContent = purchasingSelect.value || 'Crate';
                    }
                };

                if (receivingSelect) receivingSelect.addEventListener('change', updateLabels);
                updateLabels();
            });
        }

        // Attach event listener to all department category selects and receiving units
        document.querySelectorAll('select[name^="department_categories"], .receiving-unit-select').forEach(select => {
            select.addEventListener('change', checkFoodCategory);
        });

        // Attach event listener to main category select
        document.getElementById('categorySelect').addEventListener('change', checkFoodCategory);

        // Initial check on load for edit mode
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(checkFoodCategory, 500); // Small delay to ensure variants are loaded
        });
    </script>
@endsection

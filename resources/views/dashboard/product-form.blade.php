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

        $title = $isEdit ? 'Edit Brand Family' : 'Register New Brand Family';
        $action = $isEdit ? route($routePrefix . '.products.update', $product->id) : route($routePrefix . '.products.store');
    @endphp

    <div class="app-title">
        <div>
            <h1><i class="fa fa-cubes"></i> {{ $title }}</h1>
            <p>{{ $isEdit ? 'Update brand and variant details' : 'Register a new product family and its variants' }}</p>
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
                        <h5 class="mb-0"><i class="fa fa-tag text-primary mr-2"></i> Brand Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label font-weight-bold">Brand Name <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        type="text" id="brandNameInput" name="name"
                                        value="{{ old('name', $product->name ?? '') }}" placeholder="e.g. COCA COLA"
                                        required>
                                    <small class="text-muted">Enter the main brand name here.</small>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label font-weight-bold">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-lg @error('category') is-invalid @enderror"
                                        name="category" id="categorySelect" required>
                                        <option value="">Select Category</option>
                                        <option value="spirits" {{ (old('category', $product->category ?? '') == 'spirits') ? 'selected' : '' }}>Spirits (Whisky, Vodka, Gin)</option>
                                        <option value="wines" {{ (old('category', $product->category ?? '') == 'wines') ? 'selected' : '' }}>Wines</option>
                                        <option value="alcoholic_beverage" {{ (old('category', $product->category ?? '') == 'alcoholic_beverage') ? 'selected' : '' }}>Beers / Ciders</option>
                                        <option value="cocktails" {{ (old('category', $product->category ?? '') == 'cocktails') ? 'selected' : '' }}>Cocktails</option>
                                        <option value="non_alcoholic_beverage" {{ (old('category', $product->category ?? '') == 'non_alcoholic_beverage') ? 'selected' : '' }}>Soft Drinks / Sodas</option>
                                        <option value="cleaning_supplies" {{ (old('category', $product->category ?? '') == 'cleaning_supplies') ? 'selected' : '' }}>Housekeeping (Cleaning materials)</option>
                                        <option value="energy_drinks" {{ (old('category', $product->category ?? '') == 'energy_drinks') ? 'selected' : '' }}>Energy Drinks</option>
                                        <option value="water" {{ (old('category', $product->category ?? '') == 'water') ? 'selected' : '' }}>Water</option>
                                        <option value="juices" {{ (old('category', $product->category ?? '') == 'juices') ? 'selected' : '' }}>Juices</option>
                                        <option value="hot_beverages" {{ (old('category', $product->category ?? '') == 'hot_beverages') ? 'selected' : '' }}>Hot Beverages</option>
                                        <option value="food" {{ (old('category', $product->category ?? '') == 'food') ? 'selected' : '' }}>Food / Snacks</option>
                                        <option value="other" {{ (old('category', $product->category ?? '') == 'other') ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-12" id="returnableSection" style="display: none;">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_returnable" name="is_returnable" value="1" {{ old('is_returnable', $product->is_returnable ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold" for="is_returnable">
                                            <i class="fa fa-undo text-info"></i> Is Returnable? 
                                            <span class="text-muted small ml-1">(Check if this item is used and then returned to store, e.g. Brooms, Linens)</span>
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
                            <input type="hidden" name="type" value="bar">
                        </div>
                    </div>
                </div>

                <!-- 2. Product Variants -->
                <div class="mb-3">
                    <h4 class="text-secondary mb-0"><i class="fa fa-cubes"></i> Product Variants</h4>
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
                    <button class="btn btn-primary btn-sm shadow-sm" type="button" onclick="addVariant()">
                        <i class="fa fa-plus-circle"></i> Add Another Variant
                    </button>
                </div>

                <!-- 3. Departments & Placement -->
                <div class="card shadow-sm mb-4 border-top-primary mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa fa-sitemap text-primary mr-2"></i> Departments & Placement</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Select which departments this product belongs to and assign a
                            department-specific category.</p>
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
                                                    <option value="vegetables" {{ $assignedCategory == 'vegetables' ? 'selected' : '' }}>Vegetables & Fruits</option>
                                                    <option value="dairy" {{ $assignedCategory == 'dairy' ? 'selected' : '' }}>Dairy &
                                                        Eggs</option>
                                                    <option value="pantry_baking" {{ $assignedCategory == 'pantry_baking' ? 'selected' : '' }}>Pantry & Baking</option>
                                                    <option value="spices_herbs" {{ $assignedCategory == 'spices_herbs' ? 'selected' : '' }}>Spices & Herbs</option>
                                                    <option value="oils_fats" {{ $assignedCategory == 'oils_fats' ? 'selected' : '' }}>Oils & Fats</option>
                                                @elseif($dept->code === 'housekeeping')
                                                    <option value="cleaning_supplies" {{ $assignedCategory == 'cleaning_supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                                                    <option value="linens" {{ $assignedCategory == 'linens' ? 'selected' : '' }}>
                                                        Linens / Towels</option>
                                                    <option value="amenities" {{ $assignedCategory == 'amenities' ? 'selected' : '' }}>Guest Amenities</option>
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
                        <a class="btn btn-outline-secondary btn-lg ml-3"
                            href="{{ route($routePrefix . '.products.index') }}">
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
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                <div>
                    <span class="badge badge-info mr-2">New</span>
                    <strong class="text-primary">Variant Details</strong>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1"
                    style="width: 30px; height: 30px;" onclick="removeVariant(this)" title="Remove Variant">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Left Column: Basic Info -->
                    <div class="col-md-7 border-right">
                        <div class="form-row">
                            <div class="col-md-12 form-group">
                                <label class="small font-weight-bold text-muted">VARIANT NAME (e.g. Coca Cola 350ml)</label>
                                <input type="text" class="form-control variant-name-input font-weight-bold"
                                    name="variants[INDEX][name]" placeholder="Full Product Name" required>
                            </div>

                            <div class="col-md-6 form-group volume-weight-section">
                                <label class="small font-weight-bold text-muted">VOLUME/WEIGHT</label>
                                <div class="input-group">
                                    <input type="number" class="form-control measurement-input" name="variants[INDEX][measurement]"
                                        placeholder="e.g. 500" step="any">
                                    <div class="input-group-append">
                                        <select class="form-control bg-light unit-select" name="variants[INDEX][unit]"
                                            style="max-width: 80px;">
                                            <option value="ml">ml</option>
                                            <option value="l">L</option>
                                            <option value="kg">kg</option>
                                            <option value="g">g</option>
                                            <option value="pcs">pcs</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 form-group selling-type-section">
                                <label class="small font-weight-bold text-muted">SELLING TYPE</label>
                                <select class="form-control selling-method-select" name="variants[INDEX][selling_method]"
                                    onchange="togglePricing(this)" required>
                                    <option value="pic">By Item/Bottle Only</option>
                                    <option value="glass">By Glass/Tot Only</option>
                                    <option value="mixed">Mixed (Bottle & Glass)</option>
                                </select>
                            </div>

                            <!-- Food Units (Hidden by default, shown for food items) -->
                            <div class="col-md-12 form-group food-units-section" style="display: none; border-left: 3px solid #ff9800; padding-left: 10px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-muted">PURCHASING UNIT</label>
                                        <select class="form-control purchasing-unit-select" name="variants[INDEX][purchasing_unit]">
                                            <option value="">Select Unit</option>
                                            <option value="Sado">Sado</option>
                                            <option value="Debe">Debe</option>
                                            <option value="Kiroba">Kiroba</option>
                                            <option value="Carton">Carton</option>
                                            <option value="Crate">Crate</option>
                                            <option value="Kg">Kg</option>
                                            <option value="L">L (Litre)</option>
                                            <option value="Dozen">Dozen</option>
                                            <option value="Piece">Piece</option>
                                            <option value="Packet">Packet</option>
                                            <option value="Bunch">Bunch</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-bold text-muted">RECEIVING UNIT</label>
                                        <select class="form-control receiving-unit-select" name="variants[INDEX][receiving_unit]">
                                            <option value="">Select Unit</option>
                                            <option value="Kg">Kg</option>
                                            <option value="Grams">Grams</option>
                                            <option value="Litres">Litres</option>
                                            <option value="Pieces">Pieces</option>
                                            <option value="Tray">Tray</option>
                                            <option value="Sado">Sado</option>
                                            <option value="Debe">Debe</option>
                                            <option value="Kiroba">Kiroba</option>
                                            <option value="Carton">Carton</option>
                                            <option value="Crate">Crate</option>
                                            <option value="Dozen">Dozen</option>
                                            <option value="Packet">Packet</option>
                                            <option value="Bunch">Bunch</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-2 ratio-entry-section">
                                        <label class="small font-weight-bold text-warning"><i class="fa fa-balance-scale"></i> RATIO: How many Receiving Units in 1 Purchasing Unit?</label>
                                        <input type="number" class="form-control items-per-package-input" name="variants[INDEX][items_per_package]" placeholder="e.g. 5 (5 Kg per 1 Sado)" step="0.01" value="1">
                                        <small class="text-muted">Used to automatically calculate total received inventory.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Servings (Hidden by default) -->
                            <div class="col-md-12 pricing-glass bg-light p-2 rounded mb-3 servings-section"
                                style="display: none; border: 1px dashed #d6d8db;">
                                <label class="small font-weight-bold text-warning"><i class="fa fa-glass"></i> SERVINGS PER
                                    <span class="servings-unit-label">BOTTLE</span></label>
                                <input type="number" class="form-control servings-input" name="variants[INDEX][servings]"
                                    placeholder="How many glasses/servings in one unit?">
                                <small class="text-muted">Required for tracking stock when selling by serving/glass.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Image & Pricing -->
                    <div class="col-md-5">
                        <div class="row h-100">
                             <div class="col-md-12 d-flex flex-column justify-content-center align-items-center border-bottom pb-3 mb-3">
                                <label class="small font-weight-bold text-muted w-100 text-center">PRODUCT IMAGE</label>
                                <div class="image-upload-wrapper text-center">
                                    <div class="preview-box mb-2 shadow-sm rounded overflow-hidden"
                                        style="width: 100px; height: 100px; background: #f8f9fa; border: 2px solid #eaecf4; display: flex; align-items: center; justify-content: center;">
                                        <img class="img-preview" src="{{ asset('dashboard_assets/img/no-image.png') }}"
                                            style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                    <label class="btn btn-sm btn-outline-primary btn-file mb-0">
                                        <i class="fa fa-camera"></i> Choose <input type="file" style="display: none;"
                                            name="variants[INDEX][image]" accept="image/*" onchange="previewVariantImage(this)">
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Initial Pricing (Drinks Only) -->
                            <div class="col-md-12 drink-only-section">
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-primary"><i class="fa fa-money-bill"></i> SELLING PRICE / PIC (TSH)</label>
                                    <input type="number" class="form-control border-primary" name="variants[INDEX][selling_price_per_pic]" placeholder="Set price per bottle" min="0">
                                </div>
                                <div class="form-group mb-0 pricing-glass" style="display: none;">
                                    <label class="small font-weight-bold text-info"><i class="fa fa-glass"></i> SELLING PRICE / GLASS (TSH)</label>
                                    <input type="number" class="form-control border-info" name="variants[INDEX][selling_price_per_serving]" placeholder="Set price per glass" min="0">
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
            border-top: 4px solid #007bff;
        }

        .border-left-info {
            border-left: 5px solid #17a2b8 !important;
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
            container.appendChild(div.firstElementChild); // Extract the card from the wrapper div

            // Auto-fill name if brand exists
            const brandName = document.getElementById('brandNameInput').value;
            if (brandName) {
                container.lastElementChild.querySelector('.variant-name-input').value = brandName + ' ';
            }

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

        function togglePricing(select) {
            const card = select.closest('.variant-card');
            const glassSections = card.querySelectorAll('.pricing-glass');
            const val = select.value;

            if (val === 'pic') {
                glassSections.forEach(el => el.style.display = 'none');
            } else {
                glassSections.forEach(el => el.style.display = 'block');
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

        // Auto-update variant names when brand name changes (for new variants only)
        document.getElementById('brandNameInput').addEventListener('input', function () {
            const brandName = this.value;
            document.querySelectorAll('.variant-name-input').forEach(input => {
                // Only update if it looks like a default or empty value, don't overwrite user custom text
                if (input.value.trim() === '' || input.value.trim() === brandName.substring(0, brandName.length - 1)) {
                    input.value = brandName + ' ';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            if (variantCount === 0) {
                addVariant(); // Add default variant
            }
        });

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
            let isFood = false;
            let isBeverage = false;
            let isHousekeeping = false;
            
            // 1. Check main Category dropdown
            const mainCategory = document.getElementById('categorySelect').value;
            const foodCategories = ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'snacks', 'kitchen'];
            const beverageCategories = ['spirits', 'wines', 'alcoholic_beverage', 'non_alcoholic_beverage', 'energy_drinks', 'water', 'juices', 'hot_beverages', 'cocktails', 'soda', 'soft_drinks'];
            
            console.log("Checking categories... Main:", mainCategory);

            if (foodCategories.includes(mainCategory)) {
                isFood = true;
            }
            if (beverageCategories.includes(mainCategory) || mainCategory.includes('drink') || mainCategory.includes('beverage') || mainCategory.includes('soda')) {
                isBeverage = true;
            }
            if (mainCategory === 'cleaning_supplies' || mainCategory === 'linens') {
                isHousekeeping = true;
            }
            
            // 2. Check if any active department select has a 'food' related category selected (for overrides)
            const selects = document.querySelectorAll('.dept-checkbox:checked ~ div select');
            selects.forEach(select => {
                if (foodCategories.includes(select.value)) {
                    isFood = true;
                }
                if (beverageCategories.includes(select.value) || select.value.includes('drink') || select.value.includes('beverage') || select.value.includes('soda')) {
                    isBeverage = true;
                }
                if (select.value === 'cleaning_supplies' || select.value === 'linens') {
                    isHousekeeping = true;
                }
            });

            // Show/Hide Returnable Section
            const returnableSection = document.getElementById('returnableSection');
            if (isHousekeeping) {
                returnableSection.style.display = 'block';
            } else {
                returnableSection.style.display = 'none';
                document.getElementById('is_returnable').checked = false;
            }

            document.querySelectorAll('.variant-card').forEach(card => {
                const sellingTypeSection = card.querySelector('.selling-type-section');
                const volumeWeightSection = card.querySelector('.volume-weight-section');
                const foodUnitsSection = card.querySelector('.food-units-section');
                const servingsSection = card.querySelector('.servings-section');
                
                // Inputs
                const measurementInput = card.querySelector('.measurement-input');
                const unitSelect = card.querySelector('.unit-select');
                const sellingMethodSelect = card.querySelector('.selling-method-select');
                const purchasingSelect = card.querySelector('.purchasing-unit-select');
                const receivingSelect = card.querySelector('.receiving-unit-select');
                const ratioInput = card.querySelector('.items-per-package-input');

                // Selling details (Pricing & Type) - Hidden for Housekeeping, Optional for Food
                if (isHousekeeping) {
                    sellingTypeSection.style.display = 'none';
                    sellingMethodSelect.removeAttribute('required');
                    if (servingsSection) servingsSection.style.display = 'none';
                    card.querySelectorAll('.drink-only-section').forEach(el => el.style.display = 'none');
                } else if (isFood) {
                    // ALLOW Selling Logic for food (e.g. Fruit -> Juice)
                    sellingTypeSection.style.display = 'block';
                    sellingMethodSelect.removeAttribute('required'); // Keep optional for general food
                    
                    // Show Pricing (so they can set Glass Price)
                    card.querySelectorAll('.drink-only-section').forEach(el => el.style.display = 'block');
                    
                    togglePricing(sellingMethodSelect);
                } else {
                    // Beverages
                    sellingTypeSection.style.display = 'block';
                    sellingMethodSelect.setAttribute('required', 'required');
                    
                    // Show Drink Pricing
                    card.querySelectorAll('.drink-only-section').forEach(el => el.style.display = 'block');
                    
                    togglePricing(sellingMethodSelect); // Reset servings display based on selling method
                }

                // Update Servings Label based on unit
                const updateServingsLabel = () => {
                    const label = card.querySelector('.servings-unit-label');
                    if (!label) return;
                    
                    if (isFood || isBeverage) {
                        const unitValue = receivingSelect.value || 'Unit';
                        label.textContent = unitValue.charAt(0).toUpperCase() + unitValue.slice(1).toLowerCase();
                    } else {
                        label.textContent = 'Bottle';
                    }
                };

                if (receivingSelect) {
                    receivingSelect.addEventListener('change', updateServingsLabel);
                    updateServingsLabel(); // Initial call
                }

                // Volume/Weight vs Food Units
                if (isFood || isBeverage) {
                    volumeWeightSection.style.display = 'none';
                    if (measurementInput) measurementInput.removeAttribute('required');
                    
                    foodUnitsSection.style.display = 'block';
                    purchasingSelect.setAttribute('required', 'required');
                    receivingSelect.setAttribute('required', 'required');
                    
                    // Ratio Entry
                    const ratioSection = card.querySelector('.ratio-entry-section');
                    if (ratioSection) {
                        // For food, ratio is 1:1 behind scenes (per user request 48d97bab)
                        // For beverages, ratio is visible and required (e.g. 24 bottles per crate)
                        if (isFood) {
                            ratioSection.style.display = 'none';
                            if (ratioInput) ratioInput.value = '1';
                        } else {
                            ratioSection.style.display = 'block';
                            if (ratioInput && ratioInput.value === '1') ratioInput.value = '24'; // Default for beverages
                        }
                    }
                } else {
                    // Show Volume/Weight (Normal drinks AND Housekeeping)
                    volumeWeightSection.style.display = 'block';
                    if (measurementInput) measurementInput.removeAttribute('required');

                    foodUnitsSection.style.display = 'none';
                    purchasingSelect.removeAttribute('required');
                    receivingSelect.removeAttribute('required');
                    if (ratioInput) ratioInput.removeAttribute('required');
                }
            });
        }

        // Attach event listener to all department category selects
        document.querySelectorAll('select[name^="department_categories"]').forEach(select => {
            select.addEventListener('change', checkFoodCategory);
        });
        
        // Attach event listener to main category select
        document.getElementById('categorySelect').addEventListener('change', checkFoodCategory);
        
        // Initial check on load for edit mode
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(checkFoodCategory, 500); // Small delay to ensure variants are loaded
        });
    </script>
@endsection
@extends('dashboard.layouts.app')

@section('content')
@php
    $isEdit = isset($product);
    $userRole = strtolower($role ?? Auth::guard('staff')->user()->role ?? 'manager');

    if (in_array($userRole, ['bar_keeper', 'barkeeper', 'bartender'])) {
        $routePrefix = 'bar-keeper';
    } elseif ($userRole === 'storekeeper') {
        $routePrefix = 'storekeeper';
    } else {
        $routePrefix = 'admin';
    }

    $title = $isEdit ? 'Edit Product' : 'Register Product';
    $action = $isEdit
        ? route($routePrefix . '.products.update', $product->id)
        : route($routePrefix . '.products.store');
    $indexRoute = route($routePrefix . '.products.index');

    $selectedCategory = old('category', $product->category ?? '');
    $selectedUsage = old('usage', $product->usage ?? 'for_sale');
    $variant = $isEdit ? $product->variants->first() : null;

    $unitOptions = ['Pcs', 'Bottle', 'Carton', 'Crate', 'Box', 'Pack', 'Bag', 'Kg', 'Grams', 'Litres', 'ml', 'Glass', 'Shot', 'Dozen', 'Packet', 'Bundle', 'Bunch', 'Tray', 'Sado', 'Debe', 'Bucket', 'Kiroba'];

    // Normalize legacy selling units (glass/pic/serving) to Pcs
    $normalizedSellingUnit = old('variants.0.selling_unit', $variant->selling_unit ?? 'Pcs');
    if (in_array(strtolower((string) $normalizedSellingUnit), ['glass', 'pic', 'serving', 'tot', 'shot', 'cocktail'])) {
        $normalizedSellingUnit = 'Pcs';
    }

    $selectedDeptGroup = old('department_group', '');
    if (!$selectedDeptGroup && $isEdit) {
        $deptCodes = collect($product->departments ?? [])->pluck('code')->filter()->map(fn ($c) => strtolower($c))->all();
        if (in_array('shared', $deptCodes) || count(array_intersect($deptCodes, ['bar', 'kitchen', 'housekeeping'])) >= 3) {
            $selectedDeptGroup = 'shared';
        } elseif (in_array('housekeeping', $deptCodes) || $selectedCategory === 'housekeeping') {
            $selectedDeptGroup = 'housekeeping';
        } else {
            $selectedDeptGroup = 'restaurant';
        }
    }
    if (!$selectedDeptGroup && $selectedCategory) {
        $selectedDeptGroup = match ($selectedCategory) {
            'housekeeping' => 'housekeeping',
            'general' => 'shared',
            default => 'restaurant',
        };
    }
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-cube"></i> {{ $title }}</h1>
        <p>{{ $isEdit ? 'Update item details' : 'Register one store item with clear department and usage' }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ $indexRoute }}">Products</a></li>
        <li class="breadcrumb-item active">{{ $title }}</li>
    </ul>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<form action="{{ $action }}" method="POST" id="productForm">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="tile shadow-sm mb-4">
                <h3 class="tile-title">Item details</h3>

                <div class="form-group">
                    <label class="font-weight-bold">Item name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" name="item_name" id="item_name"
                        value="{{ old('item_name', $isEdit ? $product->name : '') }}"
                        placeholder="e.g. Safari Lager 500ml, Sugar, Cleaning soap" required autofocus>
                    <input type="hidden" name="name" id="productNameField" value="{{ old('name', $isEdit ? $product->name : '') }}">
                    @if($isEdit && $variant)
                        <input type="hidden" name="variants[0][id]" value="{{ $variant->id }}">
                    @endif
                    <input type="hidden" name="variants[0][name]" id="variantNameField" value="{{ old('item_name', $isEdit ? ($variant->variant_name ?? $product->name) : '') }}">
                </div>

                <div class="form-group">
                    <label class="font-weight-bold d-block mb-2">Department <span class="text-danger">*</span></label>
                    <div class="reg-choice-grid">
                        <label class="reg-choice {{ $selectedDeptGroup === 'restaurant' ? 'is-selected' : '' }}">
                            <input type="radio" name="department_group" value="restaurant" required
                                {{ $selectedDeptGroup === 'restaurant' ? 'checked' : '' }} onchange="onDepartmentChange()">
                            <strong>Restaurant</strong>
                            <small>Food & drinks</small>
                        </label>
                        <label class="reg-choice {{ $selectedDeptGroup === 'housekeeping' ? 'is-selected' : '' }}">
                            <input type="radio" name="department_group" value="housekeeping" required
                                {{ $selectedDeptGroup === 'housekeeping' ? 'checked' : '' }} onchange="onDepartmentChange()">
                            <strong>Housekeeping</strong>
                            <small>Cleaning & linens</small>
                        </label>
                        <label class="reg-choice {{ $selectedDeptGroup === 'shared' ? 'is-selected' : '' }}">
                            <input type="radio" name="department_group" value="shared" required
                                {{ $selectedDeptGroup === 'shared' ? 'checked' : '' }} onchange="onDepartmentChange()">
                            <strong>Shared</strong>
                            <small>Restaurant + Housekeeping (e.g. soap)</small>
                        </label>
                    </div>
                </div>

                <div class="alert alert-info py-2" id="sharedHint" style="display:none;">
                    <i class="fa fa-info-circle mr-1"></i>
                    <strong>Shared</strong> means this item can be used by both
                    <strong>Restaurant</strong> and <strong>Housekeeping</strong>
                    (example: soap for washing in kitchen and rooms).
                </div>

                <input type="radio" name="category" value="restaurant" id="catRestaurant" style="display:none;"
                    {{ in_array($selectedCategory, ['restaurant', 'vinywaji', 'chakula']) || $selectedDeptGroup === 'restaurant' ? 'checked' : '' }}>
                <input type="radio" name="category" value="housekeeping" id="catHousekeeping" style="display:none;"
                    {{ $selectedCategory === 'housekeeping' ? 'checked' : '' }}>
                <input type="radio" name="category" value="general" id="catGeneral" style="display:none;"
                    {{ $selectedCategory === 'general' ? 'checked' : '' }}>

                <div class="form-group">
                    <label class="font-weight-bold d-block mb-2">Usage <span class="text-danger">*</span></label>
                    <div class="reg-choice-grid reg-choice-grid-2">
                        <label class="reg-choice usage-card {{ $selectedUsage === 'internal' ? 'is-selected' : '' }}">
                            <input type="radio" name="usage" value="internal" required
                                {{ $selectedUsage === 'internal' ? 'checked' : '' }} onchange="onUsageChange()">
                            <strong>Internal use</strong>
                            <small>Store / department use only</small>
                        </label>
                        <label class="reg-choice usage-card {{ $selectedUsage === 'for_sale' ? 'is-selected' : '' }}">
                            <input type="radio" name="usage" value="for_sale" required
                                {{ $selectedUsage === 'for_sale' ? 'checked' : '' }} onchange="onUsageChange()">
                            <strong>For sale</strong>
                            <small>Sold to customers</small>
                        </label>
                    </div>
                </div>

                @php
                    $isReturnable = (bool) old('is_returnable', $isEdit ? ($product->is_returnable ?? false) : false);
                @endphp
                <div class="form-group" id="returnableWrap">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_returnable" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_returnable" name="is_returnable"
                            value="1" {{ $isReturnable ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="is_returnable">
                            Returnable to store after use
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">
                        Enable for reusable tools (broom, mop, bucket, etc.). Housekeeping can return them to the storekeeper when finished.
                    </small>
                </div>
            </div>

            <div class="tile shadow-sm mb-4">
                <h3 class="tile-title">Units & prices</h3>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Receiving package / unit <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg" name="variants[0][receiving_unit]" id="receiving_unit" required>
                            @foreach($unitOptions as $unit)
                                <option value="{{ $unit }}"
                                    {{ old('variants.0.receiving_unit', $variant->receiving_unit ?? 'Pcs') === $unit ? 'selected' : '' }}>
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Unit used when receiving into store stock</small>
                    </div>
                    <div class="col-md-6 form-group" id="sellingUnitWrap">
                        <label class="font-weight-bold">Selling package / unit <span class="text-danger">*</span></label>
                        <select class="form-control form-control-lg" name="variants[0][selling_unit]" id="selling_unit">
                            @foreach($unitOptions as $unit)
                                <option value="{{ $unit }}"
                                    {{ $normalizedSellingUnit === $unit ? 'selected' : '' }}>
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Unit used when selling this item</small>
                    </div>
                </div>

                <div class="row" id="ratioWrap" style="display:none;">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">How many selling units in 1 receiving unit?</label>
                        <input type="number" class="form-control" name="variants[0][items_per_package]" id="items_per_package"
                            min="0.01" step="any"
                            value="{{ old('variants.0.items_per_package', $variant->items_per_package ?? 1) }}">
                        <small class="text-muted">e.g. 24 bottles in 1 carton</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Buying price (TZS) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-lg" name="variants[0][buying_price]"
                            id="buying_price" min="0" step="0.01" required
                            value="{{ old('variants.0.buying_price', $variant->buying_price ?? '') }}"
                            placeholder="Price per receiving unit">
                    </div>
                    <div class="col-md-6 form-group" id="sellingPriceWrap">
                        <label class="font-weight-bold">Selling price (TZS) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-lg" name="variants[0][selling_price_per_pic]"
                            id="selling_price" min="0" step="0.01"
                            value="{{ old('variants.0.selling_price_per_pic', $variant->selling_price_per_pic ?? '') }}"
                            placeholder="Price per selling unit">
                    </div>
                </div>

                {{-- purchasing_unit mirrors receiving for stock compatibility --}}
                <input type="hidden" name="variants[0][purchasing_unit]" id="purchasing_unit" value="{{ old('variants.0.purchasing_unit', $variant->purchasing_unit ?? 'Pcs') }}">
            </div>

            {{-- Hidden departments for auto-assign --}}
            <div id="departmentsCard" style="display:none;">
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
                    <input type="checkbox" class="dept-checkbox" id="dept_{{ $dept->id }}"
                        name="departments[]" value="{{ $dept->id }}" {{ $isChecked ? 'checked' : '' }}
                        data-dept-code="{{ $dept->code }}">
                    <select name="department_categories[{{ $dept->id }}]" id="dept_cat_{{ $dept->id }}">
                        <option value="">Select...</option>
                        <option value="beverage" {{ $assignedCategory == 'beverage' ? 'selected' : '' }}>Drinks</option>
                        <option value="non_alcoholic_beverage" {{ $assignedCategory == 'non_alcoholic_beverage' ? 'selected' : '' }}>Soft Drinks</option>
                        <option value="food" {{ $assignedCategory == 'food' ? 'selected' : '' }}>Food</option>
                        <option value="cleaning_supplies" {{ $assignedCategory == 'cleaning_supplies' ? 'selected' : '' }}>Cleaning</option>
                        <option value="shared" {{ $assignedCategory == 'shared' ? 'selected' : '' }}>Shared</option>
                        <option value="other" {{ $assignedCategory == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                @endforeach
            </div>

            <div class="tile shadow-sm mb-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center" style="gap:10px;">
                    <a href="{{ $indexRoute }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="fa fa-check"></i> {{ $isEdit ? 'Save Changes' : 'Save Product' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<style>
    .reg-choice-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .reg-choice-grid-2 { grid-template-columns: repeat(2, 1fr); }
    @media (max-width: 576px) {
        .reg-choice-grid, .reg-choice-grid-2 { grid-template-columns: 1fr; }
    }
    .reg-choice {
        display: block;
        border: 2px solid #e3e6f0;
        border-radius: 10px;
        padding: 14px;
        cursor: pointer;
        margin: 0;
        background: #fff;
        transition: border-color .15s, background .15s;
    }
    .reg-choice input { display: none; }
    .reg-choice strong { display: block; color: #3a3b45; }
    .reg-choice small { color: #858796; }
    .reg-choice:hover { border-color: #4e73df; }
    .reg-choice.is-selected {
        border-color: #4e73df;
        background: #f0f4ff;
    }
    .reg-choice.is-selected strong { color: #4e73df; }
</style>
<script>
    function getDepartmentGroup() {
        const el = document.querySelector('input[name="department_group"]:checked');
        return el ? el.value : '';
    }

    function getUsage() {
        const el = document.querySelector('input[name="usage"]:checked');
        return el ? el.value : 'for_sale';
    }

    function getCategory() {
        const group = getDepartmentGroup();
        if (group === 'restaurant') return 'restaurant';
        if (group === 'housekeeping') return 'housekeeping';
        if (group === 'shared') return 'general';
        if (document.getElementById('catRestaurant')?.checked) return 'restaurant';
        if (document.getElementById('catHousekeeping')?.checked) return 'housekeeping';
        if (document.getElementById('catGeneral')?.checked) return 'general';
        return '';
    }

    function markSelected(selector) {
        document.querySelectorAll(selector).forEach(card => {
            const input = card.querySelector('input');
            card.classList.toggle('is-selected', !!(input && input.checked));
        });
    }

    function onDepartmentChange() {
        document.querySelectorAll('input[name="department_group"]').forEach(input => {
            input.closest('.reg-choice')?.classList.toggle('is-selected', input.checked);
        });

        const group = getDepartmentGroup();
        const catRestaurant = document.getElementById('catRestaurant');
        const catHk = document.getElementById('catHousekeeping');
        const catGeneral = document.getElementById('catGeneral');
        const sharedHint = document.getElementById('sharedHint');

        catRestaurant.checked = false;
        catHk.checked = false;
        catGeneral.checked = false;

        if (group === 'restaurant') {
            sharedHint.style.display = 'none';
            catRestaurant.checked = true;
        } else if (group === 'housekeeping') {
            sharedHint.style.display = 'none';
            catHk.checked = true;
        } else if (group === 'shared') {
            sharedHint.style.display = 'block';
            catGeneral.checked = true;
        } else {
            sharedHint.style.display = 'none';
        }

        autoAssignDepartments();
        updateReturnableVisibility();
        updateRatioVisibility();
    }

    function onUsageChange() {
        markSelected('.usage-card');
        const forSale = getUsage() === 'for_sale';
        document.getElementById('sellingUnitWrap').style.display = forSale ? 'block' : 'none';
        document.getElementById('sellingPriceWrap').style.display = forSale ? 'block' : 'none';
        document.getElementById('selling_unit').required = forSale;
        document.getElementById('selling_price').required = forSale;
        if (!forSale) {
            document.getElementById('selling_price').value = '';
        }
        updateReturnableVisibility();
        updateRatioVisibility();
    }

    function updateReturnableVisibility() {
        const wrap = document.getElementById('returnableWrap');
        if (!wrap) return;
        const show = getDepartmentGroup() === 'housekeeping'
            || getDepartmentGroup() === 'shared'
            || getUsage() === 'internal';
        wrap.style.display = show ? 'block' : 'none';
        if (!show) {
            const cb = document.getElementById('is_returnable');
            if (cb) cb.checked = false;
        }
    }

    function updateRatioVisibility() {
        const receive = document.getElementById('receiving_unit').value;
        const sell = document.getElementById('selling_unit').value;
        const forSale = getUsage() === 'for_sale';
        const show = forSale && receive && sell && receive !== sell;
        document.getElementById('ratioWrap').style.display = show ? 'block' : 'none';
        if (!show) {
            document.getElementById('items_per_package').value = 1;
        } else if (!document.getElementById('items_per_package').value || document.getElementById('items_per_package').value === '1') {
            // Suggest 24 for carton/crate drink packaging
            if (['Carton', 'Crate', 'Box', 'Dozen'].includes(receive) && ['Bottle', 'Pcs', 'Glass'].includes(sell)) {
                document.getElementById('items_per_package').value = receive === 'Dozen' ? 12 : 24;
            }
        }
        document.getElementById('purchasing_unit').value = receive;
    }

    function autoAssignDepartments() {
        document.querySelectorAll('.dept-checkbox').forEach(cb => {
            cb.checked = false;
            const sel = document.getElementById('dept_cat_' + cb.value);
            if (sel) sel.value = '';
        });

        const findByCode = (code) => Array.from(document.querySelectorAll('.dept-checkbox'))
            .find(cb => cb.dataset.deptCode === code);

        const mark = (code, cat) => {
            const cb = findByCode(code);
            if (!cb) return;
            cb.checked = true;
            const sel = document.getElementById('dept_cat_' + cb.value);
            if (sel && cat) sel.value = cat;
        };

        const group = getDepartmentGroup();

        if (group === 'restaurant') {
            // Restaurant covers both food and drinks — no separate kind
            mark('restaurant', 'restaurant');
            mark('bar', 'restaurant');
            mark('kitchen', 'restaurant');
        } else if (group === 'housekeeping') {
            mark('housekeeping', 'cleaning_supplies');
        } else if (group === 'shared') {
            mark('shared', 'shared');
            // Shared items belong to BOTH Restaurant and Housekeeping (e.g. soap for washing)
            mark('restaurant', 'shared');
            mark('housekeeping', 'shared');
            mark('bar', 'shared');
            mark('kitchen', 'shared');
        }
    }

    document.getElementById('item_name').addEventListener('input', function () {
        document.getElementById('productNameField').value = this.value;
        document.getElementById('variantNameField').value = this.value;
    });

    document.getElementById('receiving_unit').addEventListener('change', updateRatioVisibility);
    document.getElementById('selling_unit').addEventListener('change', updateRatioVisibility);

    document.getElementById('productForm').addEventListener('submit', function (e) {
        const group = getDepartmentGroup();
        if (!group) {
            e.preventDefault();
            alert('Please select a department.');
            return;
        }
        if (group === 'restaurant') {
            document.getElementById('catRestaurant').checked = true;
        } else if (group === 'shared') {
            document.getElementById('catGeneral').checked = true;
        } else if (group === 'housekeeping') {
            document.getElementById('catHousekeeping').checked = true;
        }

        const name = document.getElementById('item_name').value.trim();
        document.getElementById('productNameField').value = name;
        document.getElementById('variantNameField').value = name;
        document.getElementById('purchasing_unit').value = document.getElementById('receiving_unit').value;

        if (getUsage() === 'internal') {
            document.getElementById('selling_price').value = 0;
            // Keep selling_unit equal to receiving for internal
            document.getElementById('selling_unit').value = document.getElementById('receiving_unit').value;
        }

        autoAssignDepartments();
    });

    document.addEventListener('DOMContentLoaded', function () {
        if (getDepartmentGroup()) onDepartmentChange();
        onUsageChange();
        updateRatioVisibility();
    });
</script>
@endsection

@extends('dashboard.layouts.app')

@section('content')
@php
    $isEdit = isset($product);
    $userRole = strtolower(Auth::guard('staff')->user()->role ?? 'manager');
    $routePrefix = ($userRole === 'bar_keeper' || $userRole === 'barkeeper' || $userRole === 'bartender') ? 'bar-keeper' : 'admin';
    
    // If explicitly passed role variable is available, use it to refine
    if (isset($role)) {
        if ($role === 'bar_keeper' || $role === 'barkeeper') $routePrefix = 'bar-keeper';
        elseif ($role === 'manager' || $role === 'admin') $routePrefix = 'admin';
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
                                <label class="control-label font-weight-bold">Brand Name <span class="text-danger">*</span></label>
                                <input class="form-control form-control-lg @error('name') is-invalid @enderror" type="text" id="brandNameInput" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="e.g. COCA COLA" required>
                                <small class="text-muted">Enter the main brand name here.</small>
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label font-weight-bold">Category <span class="text-danger">*</span></label>
                                <select class="form-control form-control-lg @error('category') is-invalid @enderror" name="category" id="categorySelect" required>
                                    <option value="">Select Category</option>
                                    <option value="spirits" {{ (old('category', $product->category ?? '') == 'spirits') ? 'selected' : '' }}>Spirits (Whisky, Vodka, Gin)</option>
                                    <option value="wines" {{ (old('category', $product->category ?? '') == 'wines') ? 'selected' : '' }}>Wines</option>
                                    <option value="alcoholic_beverage" {{ (old('category', $product->category ?? '') == 'alcoholic_beverage') ? 'selected' : '' }}>Beers / Ciders</option>
                                    <option value="cocktails" {{ (old('category', $product->category ?? '') == 'cocktails') ? 'selected' : '' }}>Cocktails</option>
                                    <option value="non_alcoholic_beverage" {{ (old('category', $product->category ?? '') == 'non_alcoholic_beverage') ? 'selected' : '' }}>Soft Drinks / Sodas</option>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea class="form-control" name="description" rows="2" placeholder="Brief description of this product family...">{{ old('description', $product->description ?? '') }}</textarea>
                            </div>
                        </div>
                        <input type="hidden" name="type" value="bar">
                    </div>
                </div>
            </div>

            <!-- 2. Product Variants -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-secondary mb-0"><i class="fa fa-cubes"></i> Product Variants</h4>
                <button class="btn btn-primary" type="button" onclick="addVariant()">
                    <i class="fa fa-plus-circle"></i> Add Another Variant
                </button>
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
            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1" style="width: 30px; height: 30px;" onclick="removeVariant(this)" title="Remove Variant">
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
                            <input type="text" class="form-control variant-name-input font-weight-bold" name="variants[INDEX][name]" placeholder="Full Product Name" required>
                        </div>
                        
                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold text-muted">VOLUME/WEIGHT</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="variants[INDEX][measurement]" placeholder="e.g. 500" step="any" required>
                                <div class="input-group-append">
                                    <select class="form-control bg-light" name="variants[INDEX][unit]" style="max-width: 80px;">
                                        <option value="ml">ml</option>
                                        <option value="l">L</option>
                                        <option value="kg">kg</option>
                                        <option value="g">g</option>
                                        <option value="pcs">pcs</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="small font-weight-bold text-muted">SELLING TYPE</label>
                            <select class="form-control" name="variants[INDEX][selling_method]" onchange="togglePricing(this)" required>
                                <option value="pic">By Item/Bottle Only</option>
                                <option value="glass">By Glass/Tot Only</option>
                                <option value="mixed">Mixed (Bottle & Glass)</option>
                            </select>
                        </div>
                        
                        <!-- Servings (Hidden by default) -->
                        <div class="col-md-12 pricing-glass bg-light p-2 rounded mb-3" style="display: none; border: 1px dashed #d6d8db;">
                            <label class="small font-weight-bold text-warning"><i class="fa fa-glass"></i> SERVINGS PER BOTTLE</label>
                            <input type="number" class="form-control" name="variants[INDEX][servings]" placeholder="How many shots/glasses in one bottle?">
                            <small class="text-muted">Required for tracking stock when selling by glass.</small>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Image -->
                <div class="col-md-5 d-flex flex-column justify-content-center align-items-center">
                    <label class="small font-weight-bold text-muted w-100 text-center">PRODUCT IMAGE</label>
                    <div class="image-upload-wrapper text-center">
                        <div class="preview-box mb-2 shadow-sm rounded overflow-hidden" style="width: 120px; height: 120px; background: #f8f9fa; border: 2px solid #eaecf4; display: flex; align-items: center; justify-content: center;">
                            <img class="img-preview" src="{{ asset('dashboard_assets/img/no-image.png') }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <label class="btn btn-sm btn-outline-primary btn-file mb-0">
                            <i class="fa fa-camera"></i> Choose Image <input type="file" style="display: none;" name="variants[INDEX][image]" accept="image/*" onchange="previewVariantImage(this)">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .border-top-primary { border-top: 4px solid #007bff; }
    .border-left-info { border-left: 5px solid #17a2b8 !important; }
    .animate-fade-in { animation: fadeIn 0.5s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    let variantCount = {{ $isEdit ? $product->variants->count() : 0 }};

    function addVariant() {
        const container = document.getElementById('variants-container');
        const template = document.getElementById('variant-template').innerHTML;
        const newHtml = template.replace(/INDEX/g, variantCount);
        
        const div = document.createElement('div');
        div.innerHTML = newHtml;
        container.appendChild(div);
        
        // Auto-fill name if brand exists
        const brandName = document.getElementById('brandNameInput').value;
        if(brandName) {
            div.querySelector('.variant-name-input').value = brandName + ' ';
        }
        
        variantCount++;
        document.getElementById('no-variants-msg').style.display = 'none';
    }

    function removeVariant(btn) {
        if(confirm('Remove this variant?')) {
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
            reader.onload = function(e) {
                input.closest('.image-upload-wrapper').querySelector('.img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-update variant names when brand name changes (for new variants only)
    document.getElementById('brandNameInput').addEventListener('input', function() {
        const brandName = this.value;
        document.querySelectorAll('.variant-name-input').forEach(input => {
            // Only update if it looks like a default or empty value, don't overwrite user custom text
            if(input.value.trim() === '' || input.value.trim() === brandName.substring(0, brandName.length-1)) {
                input.value = brandName + ' ';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (variantCount === 0) {
            addVariant(); // Add default variant
        }
    });
</script>
@endsection

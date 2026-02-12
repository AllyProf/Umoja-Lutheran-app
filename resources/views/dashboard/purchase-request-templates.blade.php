@extends('dashboard.layouts.app')

@section('content')
@php
  $templatePlaceholder = "e.g., Weekly Housekeeping Supplies";
  if ($routePrefix === 'chef-master') {
    $templatePlaceholder = "e.g., Kitchen Daily Vegetables";
  } elseif ($routePrefix === 'bar-keeper') {
    $templatePlaceholder = "e.g., Weekly Bar Restock";
  }
@endphp

<div class="app-title">
  <div>
    <h1><i class="fa fa-book"></i> Purchase Request Templates</h1>
    <p>Create and manage templates for frequently requested items</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route($routePrefix . '.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Templates</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title"><i class="fa fa-book"></i> My Templates</h3>
        <div>
          <button type="button" class="btn btn-primary" onclick="openCreateTemplateModal()">
            <i class="fa fa-plus"></i> Create Template
          </button>
          <a href="{{ route($routePrefix . '.purchase-requests.create') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Create Request
          </a>
        </div>
      </div>
      <div class="tile-body">
        @if($templates && $templates->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Template Name</th>
                <th>Description</th>
                <th>Items Count</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($templates as $template)
              <tr>
                <td><strong>{{ $template->name }}</strong></td>
                <td>{{ $template->description ?? 'No description' }}</td>
                <td><span class="badge badge-info">{{ count($template->items) }} item(s)</span></td>
                <td>{{ $template->created_at->format('M d, Y') }}</td>
                <td>
                  <button class="btn btn-sm btn-info" onclick="viewTemplate({{ $template->id }})" title="View Items">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-primary" onclick="editTemplate({{ $template->id }})" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="deleteTemplate({{ $template->id }})" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> You haven't created any templates yet. 
          <button type="button" class="btn btn-sm btn-primary ml-2" onclick="openCreateTemplateModal()">
            Create Your First Template
          </button>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Create/Edit Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templateModalTitle">Create Template</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="templateForm">
          <input type="hidden" id="templateId" name="template_id">
          
          <div class="form-group">
            <label>Template Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="templateName" name="name" required placeholder="{{ $templatePlaceholder }}">
          </div>
          
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" id="templateDescription" name="description" rows="2" placeholder="Optional description for this template"></textarea>
          </div>
          
          <hr>
          <h6><i class="fa fa-cubes"></i> Template Items</h6>
          
          <div id="templateItemsContainer" style="max-height: 450px; overflow-y: auto; padding-right: 5px;">
            <!-- Items will be added here -->
          </div>
          
          <div class="d-flex justify-content-between align-items-center mt-3">
              <button type="button" class="btn btn-sm btn-success" onclick="addTemplateItem()">
                <i class="fa fa-plus"></i> Add Line Item
              </button>
              
              @if($routePrefix === 'bar-keeper' && count($products) > 0)
              <button type="button" class="btn btn-sm btn-info" onclick="openInventoryPicker()">
                <i class="fa fa-list"></i> Pick from Registered Drinks
              </button>
              @endif
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveTemplate()">Save Template</button>
      </div>
    </div>
  </div>
</div>

<!-- View Template Items Modal -->
<div class="modal fade" id="viewTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Template Items</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="templateItemsView">
          <!-- Items will be displayed here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Inventory Product Picker Modal -->
@if($routePrefix === 'bar-keeper')
<div class="modal fade" id="inventoryPickerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content border-info shadow-lg">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-search mr-2"></i> Select Bar Products</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 bg-light border-bottom">
            <input type="text" class="form-control" id="inventorySearch" placeholder="Search by name, brand or category..." onkeyup="filterInventory()">
        </div>
        <div class="table-responsive" style="max-height: 400px;">
          <table class="table table-hover mb-0" id="inventoryTable">
            <thead class="bg-white sticky-top">
              <tr>
                <th>Product / Size</th>
                <th>Category</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $product)
                @foreach($product->variants as $variant)
                @php
                    $vName = $variant->variant_name;
                    $pName = $product->name;
                    $generics = ['small', 'large', 'medium', 'standard', 'regular', 'box', 'carton'];
                    
                    if ($vName && in_array(strtolower($vName), $generics)) {
                        $displayName = $pName . ' ' . $vName;
                    } else {
                        $displayName = $vName ?: $pName;
                    }
                @endphp
                <tr class="inventory-row" data-name="{{ strtolower($displayName . ' ' . $product->name) }}" data-category="{{ strtolower($product->category) }}">
                  <td>
                    <div class="font-weight-bold">{{ $displayName }}</div>
                    <div class="small text-muted">{{ $variant->measurement }}</div>
                  </td>
                  <td><span class="badge badge-light border">{{ ucfirst(str_replace('_', ' ', $product->category)) }}</span></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary" 
                            onclick="selectFromInventory('{{ addslashes($displayName) }}', '{{ $product->category }}', '{{ $variant->measurement }}', this)">
                      <i class="fa fa-plus"></i> Add
                    </button>
                  </td>
                </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
let templateItemIndex = 0;
let editingTemplateId = null;

function openCreateTemplateModal() {
    editingTemplateId = null;
    $('#templateModalTitle').text('Create Template');
    $('#templateForm')[0].reset();
    $('#templateId').val('');
    $('#templateItemsContainer').empty();
    templateItemIndex = 0;
    addTemplateItem();
    $('#templateModal').modal('show');
}

function addTemplateItem(itemData = null) {
    const index = templateItemIndex++;
    const itemRow = `
        <div class="template-item-row mb-3 p-3 border rounded" data-index="${index}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Item ${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeTemplateItem(${index})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control template-item-name" name="items[${index}][item_name]" required value="${itemData ? itemData.item_name : ''}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control template-item-category" name="items[${index}][category]" onchange="toggleTemplateWaterSize(${index}, this.value)">
                            <option value="">Select Category</option>
                            @if($routePrefix === 'admin' || $routePrefix === 'bar-keeper')
                            <optgroup label="Bar & Beverages">
                                <option value="spirits" ${itemData && itemData.category === 'spirits' ? 'selected' : ''}>Spirits</option>
                                <option value="wines" ${itemData && itemData.category === 'wines' ? 'selected' : ''}>Wines</option>
                                <option value="alcoholic_beverage" ${itemData && itemData.category === 'alcoholic_beverage' ? 'selected' : ''}>Beer / Cider</option>
                                <option value="non_alcoholic_beverage" ${itemData && itemData.category === 'non_alcoholic_beverage' ? 'selected' : ''}>Soda / Soft Drinks</option>
                                <option value="energy_drinks" ${itemData && itemData.category === 'energy_drinks' ? 'selected' : ''}>Energy Drinks</option>
                                <option value="juices" ${itemData && itemData.category === 'juices' ? 'selected' : ''}>Juices</option>
                                <option value="water" ${itemData && itemData.category === 'water' ? 'selected' : ''}>Water</option>
                                <option value="hot_beverages" ${itemData && itemData.category === 'hot_beverages' ? 'selected' : ''}>Hot Beverages</option>
                                <option value="cocktails" ${itemData && itemData.category === 'cocktails' ? 'selected' : ''}>Cocktails</option>
                            </optgroup>
                            @endif
                            @if($routePrefix === 'admin' || $routePrefix === 'chef-master')
                            <optgroup label="Kitchen & Food">
                                <option value="meat_poultry" ${itemData && itemData.category === 'meat_poultry' ? 'selected' : ''}>Meat & Poultry</option>
                                <option value="seafood" ${itemData && itemData.category === 'seafood' ? 'selected' : ''}>Seafood & Fish</option>
                                <option value="vegetables" ${itemData && itemData.category === 'vegetables' ? 'selected' : ''}>Vegetables & Fruits</option>
                                <option value="dairy" ${itemData && itemData.category === 'dairy' ? 'selected' : ''}>Dairy & Eggs</option>
                                <option value="pantry_baking" ${itemData && itemData.category === 'pantry_baking' ? 'selected' : ''}>Pantry & Baking</option>
                                <option value="food" ${itemData && itemData.category === 'food' ? 'selected' : ''}>General Food</option>
                            </optgroup>
                            @endif
                            <optgroup label="Other">
                                <option value="kitchen_disposables" ${itemData && itemData.category === 'kitchen_disposables' ? 'selected' : ''}>Kitchen Disposables</option>
                                <option value="cleaning_supplies" ${itemData && itemData.category === 'cleaning_supplies' ? 'selected' : ''}>Cleaning Supplies</option>
                                <option value="linens" ${itemData && itemData.category === 'linens' ? 'selected' : ''}>Linens</option>
                                <option value="other" ${itemData && (itemData.category === 'other' || !itemData.category) ? 'selected' : ''}>Other</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Unit <span class="text-danger">*</span></label>
                        <select class="form-control template-item-unit" name="items[${index}][unit]" required onchange="toggleTemplateCustomUnit(${index}, this.value)">
                            <option value="pcs" ${itemData && itemData.unit === 'pcs' ? 'selected' : ''}>Pieces (pcs)</option>
                            <option value="liters" ${itemData && itemData.unit === 'liters' ? 'selected' : ''}>Liters (L)</option>
                            <option value="ml" ${itemData && itemData.unit === 'ml' ? 'selected' : ''}>Milliliters (ml)</option>
                            <option value="kg" ${itemData && itemData.unit === 'kg' ? 'selected' : ''}>Kilograms (kg)</option>
                            <option value="g" ${itemData && itemData.unit === 'g' ? 'selected' : ''}>Grams (g)</option>
                            <option value="boxes" ${itemData && itemData.unit === 'boxes' ? 'selected' : ''}>Boxes</option>
                            <option value="bottles" ${itemData && itemData.unit === 'bottles' ? 'selected' : ''}>PIC (Bottle)</option>
                            <option value="rolls" ${itemData && itemData.unit === 'rolls' ? 'selected' : ''}>Rolls</option>
                            <option value="packs" ${itemData && itemData.unit === 'packs' ? 'selected' : ''}>Packs</option>
                            <option value="cartons" ${itemData && itemData.unit === 'cartons' ? 'selected' : ''}>Cartons</option>
                            <option value="bags" ${itemData && itemData.unit === 'bags' ? 'selected' : ''}>Bags</option>
                            <option value="custom" ${itemData && itemData.unit && !['pcs','liters','ml','kg','g','boxes','bottles','rolls','packs','cartons','bags'].includes(itemData.unit) ? 'selected' : ''}>Custom Unit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 template-custom-unit-${index}" style="display: ${itemData && itemData.unit && !['pcs','liters','ml','kg','g','boxes','bottles','rolls','packs','cartons','bags'].includes(itemData.unit) ? 'block' : 'none'};">
                    <div class="form-group">
                        <label>Specify Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control template-item-custom-unit" name="items[${index}][custom_unit]" value="${itemData && itemData.unit && !['pcs','liters','ml','kg','g','boxes','bottles','rolls','packs','cartons','bags'].includes(itemData.unit) ? itemData.unit : ''}" placeholder="e.g., gallons">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control template-item-quantity" name="items[${index}][quantity]" required min="1" value="${itemData ? itemData.quantity : ''}">
                    </div>
                </div>
                <div class="col-md-3 template-water-size-${index}" style="display: ${itemData && itemData.category === 'water' && itemData.unit === 'pcs' ? 'block' : 'none'};">
                    <div class="form-group">
                        <label>Water Size</label>
                        <select class="form-control template-item-water-size" name="items[${index}][water_size]">
                            <option value="">Select Size</option>
                            <option value="small" ${itemData && itemData.water_size === 'small' ? 'selected' : ''}>Small</option>
                            <option value="large" ${itemData && itemData.water_size === 'large' ? 'selected' : ''}>Large</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Priority <span class="text-danger">*</span></label>
                        <select class="form-control template-item-priority" name="items[${index}][priority]" required>
                            <option value="low" ${itemData && itemData.priority === 'low' ? 'selected' : ''}>Low</option>
                            <option value="medium" ${itemData && itemData.priority === 'medium' ? 'selected' : ''}>Medium</option>
                            <option value="high" ${itemData && itemData.priority === 'high' ? 'selected' : ''}>High</option>
                            <option value="urgent" ${itemData && itemData.priority === 'urgent' ? 'selected' : ''}>Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Reason (Optional)</label>
                        <input type="text" class="form-control template-item-reason" name="items[${index}][reason]" placeholder="e.g., Running low" value="${itemData ? (itemData.reason || '') : ''}">
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#templateItemsContainer').append(itemRow);
}

function removeTemplateItem(index) {
    $(`.template-item-row[data-index="${index}"]`).remove();
}

function toggleTemplateWaterSize(index, category) {
    const unit = $(`.template-item-row[data-index="${index}"] .template-item-unit`).val();
    if (category === 'water' && unit === 'pcs') {
        $(`.template-water-size-${index}`).show();
    } else {
        $(`.template-water-size-${index}`).hide();
    }
}

function toggleTemplateCustomUnit(index, unit) {
    if (unit === 'custom') {
        $(`.template-custom-unit-${index}`).show();
    } else {
        $(`.template-custom-unit-${index}`).hide();
    }
}

function saveTemplate() {
    // Validate form
    if (!$('#templateName').val().trim()) {
        swal("Error!", "Please enter a template name.", "error");
        return;
    }
    
    // Collect items
    const items = [];
    let isValid = true;
    
    $('.template-item-row').each(function() {
        const unit = $(this).find('.template-item-unit').val();
        const customUnit = $(this).find('.template-item-custom-unit').val().trim();
        const finalUnit = unit === 'custom' ? customUnit : unit;
        const category = $(this).find('.template-item-category').val();
        const waterSize = $(this).find('.template-item-water-size').val();
        
        if (unit === 'custom' && !customUnit) {
            isValid = false;
            return false;
        }
        
        if (category === 'water' && unit === 'pcs' && !waterSize) {
            isValid = false;
            return false;
        }
        
        const itemName = $(this).find('.template-item-name').val().trim();
        const quantity = $(this).find('.template-item-quantity').val();
        const priority = $(this).find('.template-item-priority').val();
        const reason = $(this).find('.template-item-reason').val().trim();
        
        if (!itemName || !quantity || !finalUnit || !priority) {
            isValid = false;
            return false;
        }
        
        // Process item name (remove size suffix if exists)
        let processedItemName = itemName.replace(/\s*\(Small\)\s*/i, '').replace(/\s*\(Large\)\s*/i, '').trim();
        
        // Add size if water category
        if (category === 'water' && unit === 'pcs' && waterSize) {
            const sizeText = waterSize.charAt(0).toUpperCase() + waterSize.slice(1);
            if (processedItemName.indexOf(sizeText) === -1) {
                processedItemName = processedItemName + ' (' + sizeText + ')';
            }
        }
        
        items.push({
            item_name: processedItemName,
            category: category || null,
            quantity: parseFloat(quantity),
            unit: finalUnit,
            water_size: (category === 'water' && unit === 'pcs') ? waterSize : null,
            priority: priority,
            reason: reason || null,
        });
    });
    
    if (!isValid || items.length === 0) {
        swal("Error!", "Please fill in all required fields for all items.", "error");
        return;
    }
    
    const formData = {
        _token: '{{ csrf_token() }}',
        name: $('#templateName').val().trim(),
        description: $('#templateDescription').val().trim(),
        items: items
    };
    
    const url = editingTemplateId 
        ? '{{ route($routePrefix . ".purchase-requests.templates.update", ":id") }}'.replace(':id', editingTemplateId)
        : '{{ route($routePrefix . ".purchase-requests.templates.store") }}';
    const method = editingTemplateId ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            if (response.success) {
                swal({
                    title: "Success!",
                    text: response.message,
                    type: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Failed to save template.';
            swal("Error!", errorMsg, "error");
        }
    });
}

function viewTemplate(id) {
    $.ajax({
        url: '{{ route($routePrefix . ".purchase-requests.templates.get", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const template = response.template;
                let html = `<h5>${template.name}</h5>`;
                if (template.description) {
                    html += `<p class="text-muted">${template.description}</p>`;
                }
                html += '<hr><table class="table table-bordered"><thead><tr><th>Item Name</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Priority</th></tr></thead><tbody>';
                
                template.items.forEach(function(item) {
                    html += `<tr>
                        <td>${item.item_name}</td>
                        <td>${item.category || '-'}</td>
                        <td>${Math.round(item.quantity)}</td>
                        <td>${item.unit === 'bottles' ? 'PIC' : item.unit}</td>
                        <td><span class="badge badge-${item.priority === 'urgent' ? 'danger' : item.priority === 'high' ? 'warning' : item.priority === 'medium' ? 'info' : 'secondary'}">${item.priority}</span></td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                $('#templateItemsView').html(html);
                $('#viewTemplateModal').modal('show');
            }
        },
        error: function() {
            swal("Error!", "Failed to load template.", "error");
        }
    });
}

function editTemplate(id) {
    editingTemplateId = id;
    $('#templateModalTitle').text('Edit Template');
    $('#templateForm')[0].reset();
    $('#templateItemsContainer').empty();
    templateItemIndex = 0;
    
    $.ajax({
        url: '{{ route($routePrefix . ".purchase-requests.templates.get", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const template = response.template;
                $('#templateId').val(template.id);
                $('#templateName').val(template.name);
                $('#templateDescription').val(template.description || '');
                
                template.items.forEach(function(item) {
                    addTemplateItem(item);
                });
                
                $('#templateModal').modal('show');
            }
        },
        error: function() {
            swal("Error!", "Failed to load template.", "error");
        }
    });
}

function deleteTemplate(id) {
    swal({
        title: "Are you sure?",
        text: "This will permanently delete this template.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: '{{ route($routePrefix . ".purchase-requests.templates.delete", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: "Deleted!",
                            text: response.message,
                            type: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }
                },
                error: function() {
                    swal("Error!", "Failed to delete template.", "error");
                }
            });
        }
    });
}

// Inventory Picker Helpers
function openInventoryPicker() {
    $('#inventorySearch').val('');
    $('.inventory-row').show();
    $('#inventoryPickerModal').modal('show');
}

// Fix for stacked modals (ensures first modal remains scrollable and interactive)
$(document).on('show.bs.modal', '.modal', function() {
    const zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function() {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});

function filterInventory() {
    const term = $('#inventorySearch').val().toLowerCase();
    $('.inventory-row').each(function() {
        const name = $(this).data('name');
        const cat = $(this).data('category');
        $(this).toggle(name.includes(term) || cat.includes(term));
    });
}

function selectFromInventory(name, category, measurement, btn) {
    // Determine the descriptive name
    const finalName = measurement ? `${name} (${measurement})` : name;
    
    // Choose sensible default unit based on category/name
    let defaultUnit = 'bottles'; // Still stored as 'bottles' in DB for consistency, but shown as PIC
    if (category === 'water') defaultUnit = 'pcs';
    if (finalName.toLowerCase().includes('can')) defaultUnit = 'pcs';
    
    // Auto-detect water size
    let waterSize = null;
    if (category === 'water') {
         if (measurement.includes('500') || measurement.includes('600') || measurement.includes('350') || measurement.toLowerCase().includes('small')) {
             waterSize = 'small';
         } else if (measurement.includes('1.5') || measurement.includes('1') || measurement.toLowerCase().includes('large') || measurement.toLowerCase().includes('liter')) {
             waterSize = 'large';
         }
    }

    // Add the item to form
    addTemplateItem({
        item_name: finalName,
        category: category,
        quantity: 1,
        unit: defaultUnit,
        water_size: waterSize,
        priority: 'medium'
    });
    
    // Visual feedback on the button
    if (btn) {
        const originalHtml = $(btn).html();
        $(btn).html('<i class="fa fa-check"></i> Added!').removeClass('btn-primary').addClass('btn-success');
        setTimeout(() => {
            $(btn).html(originalHtml).removeClass('btn-success').addClass('btn-primary');
        }, 1500);
    }
    
    // Smooth UI feedback using Swal Toast
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });

    Toast.fire({
        icon: 'success',
        title: 'Added to template:',
        text: finalName
    });

    // Auto-scroll the template container to show the new item
    const container = document.getElementById('templateItemsContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}
</script>
@endsection

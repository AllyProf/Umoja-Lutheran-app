@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-cubes"></i> Registered Products</h1>
            <p>Manage products and their department assignments</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <a href="{{ route('storekeeper.products.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa fa-plus"></i> Register New Product
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile shadow-sm">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="productsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name / Brand</th>
                                    <th>Category Group</th>
                                    <th>Departments & Categories</th>
                                    <th>Variants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $product->name }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $displayType = $product->type;
                                                if ($displayType === 'drink')
                                                    $displayType = 'beverage';
                                                elseif ($displayType === 'food')
                                                    $displayType = 'kitchen';
                                            @endphp
                                            <span class="badge badge-secondary">{{ ucfirst($displayType) }}</span>
                                        </td>
                                        <td>
                                            @if($product->departments->count() > 0)
                                                @foreach($product->departments as $dept)
                                                    <span class="badge badge-info mb-1"
                                                        title="Category: {{ ucfirst(str_replace('_', ' ', $dept->pivot->category)) }}">
                                                        <i class="fa fa-sitemap"></i> {{ $dept->name }}
                                                        <small>({{ ucfirst(str_replace('_', ' ', $dept->pivot->category)) }})</small>
                                                    </span><br>
                                                @endforeach
                                            @else
                                                <span class="text-muted small"><em>No department assigned</em></span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-pill badge-dark">{{ $product->variants->count() }}
                                                sizes</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('storekeeper.products.edit', $product->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('storekeeper.products.destroy', $product->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('WARNING: This will delete the product and ALL its variants! Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <h5 class="text-muted"><i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i> No
                                                products found</h5>
                                            <p class="mb-0">Start by registering a new product to assign to departments.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($products->hasPages())
                    <div class="tile-footer border-top pt-3">
                        {{ $products->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#productsTable').DataTable({
                "paging": false,
                "info": false,
                "searching": true,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [4] }
                ]
            });
        });
    </script>
@endsection

@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-cutlery"></i> {{ $recipe->name }}</h1>
        <p>{{ $recipe->category ?? 'General' }} Recipe</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.recipes.index') }}">Recipes</a></li>
        <li class="breadcrumb-item">View</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="tile p-0">
            @if($recipe->image)
                <img src="{{ Storage::url($recipe->image) }}" class="img-fluid w-100" style="object-fit: cover; max-height: 300px;">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                    <i class="fa fa-cutlery fa-5x text-muted"></i>
                </div>
            @endif
            <div class="p-3">
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-clock-o"></i> Prep:</strong></div>
                    <div class="col-6 text-right">{{ $recipe->prep_time ?? 0 }} mins</div>
                </div>
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-fire"></i> Cook:</strong></div>
                    <div class="col-6 text-right">{{ $recipe->cook_time ?? 0 }} mins</div>
                </div>
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-money"></i> Price:</strong></div>
                    <div class="col-6 text-right font-weight-bold text-success">{{ number_format($recipe->selling_price) }} TZS</div>
                </div>
                <div class="row border-bottom pb-2 mb-2">
                    <div class="col-6"><strong><i class="fa fa-info-circle"></i> Status:</strong></div>
                    <div class="col-6 text-right">
                        @if($recipe->is_available)
                            <span class="badge badge-success">Available</span>
                        @else
                            <span class="badge badge-danger">Unavailable</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tile">
            <h4 class="tile-title border-bottom pb-2">Ingredients</h4>
            <div class="tile-body">
                <ul class="list-group list-group-flush">
                    @foreach($recipe->ingredients as $ingredient)
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-0 py-1">
                        <span>{{ $ingredient->name }}</span>
                        <span class="badge badge-primary badge-pill">{{ $ingredient->quantity }} {{ $ingredient->unit }}</span>
                    </li>
                    @if($ingredient->notes)
                        <li class="list-group-item px-0 border-0 pt-0 pb-2"><small class="text-info italic"> - {{ $ingredient->notes }}</small></li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="tile">
            <h3 class="tile-title">Preparation Steps</h3>
            <div class="tile-body" style="font-size: 1.1rem; line-height: 1.6;">
                @if($recipe->description)
                    <div class="alert alert-light border-left border-info">
                        <strong>Overview:</strong><br>
                        {{ $recipe->description }}
                    </div>
                @endif
                
                <div class="instructions-content mt-4">
                    {!! nl2br(e($recipe->instructions)) !!}
                </div>
            </div>
            <div class="tile-footer border-top mt-4 pt-3">
                <a href="{{ route('admin.recipes.edit', $recipe->id) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Edit Recipe</a>
                <a href="{{ route('admin.recipes.index') }}" class="btn btn-secondary ml-2">Back to Catalog</a>
            </div>
        </div>
    </div>
</div>
@endsection

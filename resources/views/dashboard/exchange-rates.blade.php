@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-money"></i> Currency</h1>
    <p>System currency information</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Currency</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center" style="padding: 40px;">
        <i class="fa fa-money fa-3x text-success mb-3"></i>
        <h3>All amounts use TSh only</h3>
        <p class="text-muted mb-0">
          This system uses <strong>Tanzanian Shilling (TSh)</strong> exclusively.
          All prices and payments are in TSh.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

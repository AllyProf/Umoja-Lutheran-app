@extends('dashboard.layouts.reports')

@section('reports-content')
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-cutlery"></i> Food Cost Analysis Report</h3>
      <div class="tile-body">
        <p>This report analyzes food costs and profitability. Coming soon with comprehensive data and visualizations.</p>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Reports Dashboard
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

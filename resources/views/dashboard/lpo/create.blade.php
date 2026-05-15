@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-plus-circle"></i> New LPO Budget</h1>
            <p>Create a bi-weekly budget batch for stock items</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('lpo.index') }}">LPO Budgets</a></li>
            <li class="breadcrumb-item">New Budget</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="tile shadow-sm border-0">
                <h3 class="tile-title border-bottom pb-3">LPO Budget Period</h3>
                <div class="tile-body pt-3">
                    <form action="{{ route('lpo.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label font-weight-bold">Start Date</label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label font-weight-bold">End Date (Target: 14 Days)</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-lg"
                                        value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                                    <small class="text-info"><i class="fa fa-info-circle"></i> Budgets are typically for 2
                                        weeks.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">General Notes / Purpose</label>
                            <textarea name="notes" class="form-control" rows="4"
                                placeholder="e.g. Budget for Kitchen and Store items for early May..."></textarea>
                        </div>

                        <div class="tile-footer px-0 pb-0 pt-4 mt-4 border-top">
                            <button class="btn btn-primary btn-lg btn-block" type="submit">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i> Proceed to Add Budget Items
                            </button>
                            <a class="btn btn-secondary btn-block" href="{{ route('lpo.index') }}">
                                <i class="fa fa-fw fa-lg fa-times-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#start_date').on('change', function () {
                let startDate = new Date($(this).val());
                if (!isNaN(startDate.getTime())) {
                    let endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 14);
                    $('#end_date').val(endDate.toISOString().split('T')[0]);
                }
            });
        });
    </script>
@endsection

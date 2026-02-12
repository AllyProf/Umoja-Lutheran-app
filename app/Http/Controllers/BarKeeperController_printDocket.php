    /**
     * Print walk-in docket/receipt
     */
    public function printDocket(ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        
        // Get all items for this walk-in customer (group by walk_in_name if multiple items)
        $items = collect([$serviceRequest]);
        
        // If this is part of a multi-item order, get all items
        if ($serviceRequest->is_walk_in && $serviceRequest->walk_in_name) {
            $items = ServiceRequest::where('is_walk_in', true)
                ->where('walk_in_name', $serviceRequest->walk_in_name)
                ->where('status', '!=', 'cancelled')
                ->whereDate('created_at', $serviceRequest->created_at->toDateString())
                ->get();
        }
        
        $totalAmount = $items->sum('total_price_tsh');
        $guestName = $serviceRequest->walk_in_name ?? 'General Walk-in';
        
        return view('dashboard.print-walk-in-docket', compact('items', 'totalAmount', 'guestName', 'serviceRequest'));
    }

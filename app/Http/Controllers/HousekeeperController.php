<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomCleaningLog;
use App\Models\HousekeepingInventoryItem;
use App\Models\RoomIssue;
use App\Models\InventoryStockMovement;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class HousekeeperController extends Controller
{
    /**
     * Show housekeeper dashboard
     */
    public function index()
    {
        $housekeeper = Auth::guard('staff')->user();
        
        // Get all rooms with their current bookings and cleaning status
        $allRooms = Room::with([
            'latestCleaningLog',
            'issues' => function($query) {
                $query->where('status', '!=', 'resolved')->latest();
            },
            'bookings' => function($query) {
                $query->whereIn('check_in_status', ['checked_in', 'checked_out'])
                    ->orderBy('created_at', 'desc');
            }
        ])->orderBy('room_number')->get();
        
        $today = Carbon::today();
        
        // Process rooms to get current booking info
        foreach ($allRooms as $room) {
            // Get current active booking (checked in and within date range)
            $room->currentBooking = $room->bookings->filter(function($booking) use ($today) {
                if ($booking->check_in_status !== 'checked_in') {
                    return false;
                }
                $checkIn = Carbon::parse($booking->check_in)->startOfDay();
                $checkOut = Carbon::parse($booking->check_out)->endOfDay();
                return $today->gte($checkIn) && $today->lte($checkOut);
            })->first();
            
            // Get last checkout booking
            $room->lastCheckout = $room->bookings->where('check_in_status', 'checked_out')
                ->sortByDesc('checked_out_at')
                ->first();
            
            // Get active issues
            $room->activeIssues = $room->issues->where('status', '!=', 'resolved');
        }
        
        // Get rooms needing cleaning
        $roomsNeedingCleaning = Room::where('status', 'to_be_cleaned')
            ->with(['latestCleaningLog', 'bookings' => function($query) {
                $query->where('check_in_status', 'checked_out')
                    ->orderBy('checked_out_at', 'desc')
                    ->limit(1);
            }])
            ->get();
        
        // Get inventory items with low stock
        $lowStockItems = HousekeepingInventoryItem::whereRaw('current_stock <= minimum_stock')->get();
        
        // Get recent room issues
        $recentIssues = RoomIssue::with(['room', 'reportedBy'])
            ->where('status', '!=', 'resolved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get statistics
        $stats = [
            'rooms_needing_cleaning' => $roomsNeedingCleaning->count(),
            'low_stock_items' => $lowStockItems->count(),
            'pending_issues' => RoomIssue::where('status', '!=', 'resolved')->count(),
            'cleaned_today' => RoomCleaningLog::whereDate('cleaned_at', Carbon::today())
                ->where('status', 'cleaned')
                ->count(),
        ];
        
        return view('dashboard.housekeeper-dashboard', compact(
            'allRooms',
            'roomsNeedingCleaning',
            'lowStockItems',
            'recentIssues',
            'stats'
        ));
    }

    /**
     * Show rooms needing cleaning
     */
    public function roomsNeedingCleaning()
    {
        $rooms = Room::where('status', 'to_be_cleaned')
            ->with(['latestCleaningLog', 'bookings' => function($query) {
                $query->where('check_in_status', 'checked_out')
                    ->orderBy('checked_out_at', 'desc')
                    ->limit(1);
            }])
            ->orderBy('room_number')
            ->get();
        
        return view('dashboard.housekeeper-rooms-cleaning', compact('rooms'));
    }

    /**
     * Mark room as cleaned
     */
    public function markRoomCleaned(Request $request, Room $room)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $housekeeper = Auth::guard('staff')->user();
        
        DB::beginTransaction();
        try {
            // Update room status
            $room->update(['status' => 'available']);
            
            // Update or create cleaning log
            $cleaningLog = RoomCleaningLog::where('room_id', $room->id)
                ->where('status', 'needs_cleaning')
                ->latest()
                ->first();
            
            if ($cleaningLog) {
                $cleaningLog->update([
                    'status' => 'cleaned',
                    'cleaned_by' => $housekeeper->id,
                    'cleaned_at' => now(),
                    'notes' => $request->notes,
                ]);
            } else {
                RoomCleaningLog::create([
                    'room_id' => $room->id,
                    'cleaned_by' => $housekeeper->id,
                    'status' => 'cleaned',
                    'cleaned_at' => now(),
                    'notes' => $request->notes,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Room marked as cleaned successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark room as cleaned: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show inventory management
     */
    public function inventory()
    {
        $items = HousekeepingInventoryItem::with('stockMovements')
            ->orderBy('name')
            ->get()
            ->map(function($item) {
                // Calculate total received stock (sum of all 'supply' type movements)
                $item->total_received = $item->stockMovements()
                    ->where('movement_type', 'supply')
                    ->sum('quantity');
                return $item;
            });
        
        // Get items waiting to be received (purchased but not yet received by department)
        $pendingToReceive = ShoppingListItem::where('is_received_by_department', false)
            ->where('is_purchased', true)
            ->where('is_found', true)
            ->where('transferred_to_department', 'Housekeeping')
            ->with(['purchaseRequest', 'shoppingList'])
            ->get();
            
        // Get recently received items (last 30 days)
        $recentlyReceived = ShoppingListItem::where('is_received_by_department', true)
            ->where('transferred_to_department', 'Housekeeping')
            ->where('received_by_department_at', '>=', now()->subDays(30))
            ->with(['purchaseRequest', 'shoppingList'])
            ->orderBy('received_by_department_at', 'desc')
            ->get();
        
        return view('dashboard.housekeeper-inventory', compact('items', 'recentlyReceived', 'pendingToReceive'));
    }

    /**
     * Show housekeeping inventory for managers (read-only view)
     */
    public function managerInventoryView()
    {
        $items = HousekeepingInventoryItem::with('stockMovements')
            ->orderBy('name')
            ->get()
            ->map(function($item) {
                // Calculate total received stock (sum of all 'supply' type movements)
                $item->total_received = $item->stockMovements()
                    ->where('movement_type', 'supply')
                    ->sum('quantity');
                return $item;
            });
        
        // Get recently received items (last 30 days) - all housekeeping items
        $recentlyReceived = ShoppingListItem::where('is_received_by_department', true)
            ->whereRaw('LOWER(TRIM(transferred_to_department)) = ?', ['housekeeping'])
            ->where('received_by_department_at', '>=', now()->subDays(30))
            ->with(['purchaseRequest.requestedBy', 'shoppingList'])
            ->orderBy('received_by_department_at', 'desc')
            ->get();
        
        return view('dashboard.housekeeper-inventory', compact('items', 'recentlyReceived'));
    }

    /**
     * Update inventory stock
     */
    public function updateInventoryStock(Request $request, HousekeepingInventoryItem $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
            'movement_type' => 'required|in:supply,consumption,adjustment,transfer',
            'room_id' => 'nullable', // Can be single ID or array
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $housekeeper = Auth::guard('staff')->user();
        
        DB::beginTransaction();
        try {
            $oldStock = $item->current_stock;
            
            // Handle room_id as potentially an array (from multi-select)
            $roomIdsInput = $request->room_id;
            $roomIds = is_array($roomIdsInput) ? $roomIdsInput : ($roomIdsInput ? [$roomIdsInput] : []);
            
            // Calculate total quantity to deduct/add
            // If rooms are selected, we assume 'quantity' is applied to EACH room
            $multiplier = count($roomIds) > 0 ? count($roomIds) : 1;
            $totalChangeQuantity = $request->quantity * $multiplier;
            
            // Update stock based on movement type (consumption, cleaning_use, transfer deduct stock)
            if ($request->movement_type === 'supply') {
                $item->current_stock += $totalChangeQuantity;
            } elseif (in_array($request->movement_type, ['consumption', 'cleaning_use', 'transfer'])) {
                $item->current_stock -= $totalChangeQuantity;
                if ($item->current_stock < 0) {
                    $item->current_stock = 0;
                }
            } elseif ($request->movement_type === 'adjustment') {
                $item->current_stock = $request->quantity; // Direct override
            }
            
            $item->save();
            
            // Create stock movement log(s)
            if (empty($roomIds)) {
                InventoryStockMovement::create([
                    'inventory_item_id' => $item->id,
                    'movement_type' => $request->movement_type,
                    'quantity' => $request->quantity,
                    'room_id' => null,
                    'performed_by' => $housekeeper->id,
                    'notes' => $request->notes,
                ]);
            } else {
                foreach ($roomIds as $rid) {
                    InventoryStockMovement::create([
                        'inventory_item_id' => $item->id,
                        'movement_type' => $request->movement_type,
                        'quantity' => $request->quantity,
                        'room_id' => $rid,
                        'performed_by' => $housekeeper->id,
                        'notes' => $request->notes . (count($roomIds) > 1 ? " (Part of multi-room update)" : ""),
                    ]);
                }
            }
            
            // Check if stock went below minimum and send notification
            // Only notify if stock was above minimum before and is now below minimum
            if ($item->isLowStock() && $oldStock > $item->minimum_stock) {
                $this->sendLowStockNotification($item->fresh());
            }
            
            DB::commit();
            
            // Create appropriate success message based on movement type
            $message = match($request->movement_type) {
                'supply' => "Stock added successfully. New stock: {$item->current_stock} {$item->unit}.",
                'consumption' => "Stock consumed successfully. Remaining stock: {$item->current_stock} {$item->unit}.",
                'transfer' => "Stock transferred successfully. Remaining stock: {$item->current_stock} {$item->unit}.",
                'adjustment' => "Stock adjusted successfully. New stock: {$item->current_stock} {$item->unit}.",
                default => 'Inventory updated successfully.',
            };
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'new_stock' => $item->current_stock,
                'movement_type' => $request->movement_type,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update minimum stock level for an item
     */
    public function updateMinimumStock(Request $request, HousekeepingInventoryItem $item)
    {
        $request->validate([
            'minimum_stock' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            $oldMinimum = $item->minimum_stock;
            $item->minimum_stock = $request->minimum_stock;
            $item->save();
            
            // Check if stock is now low and send notification if needed
            if ($item->isLowStock() && $oldMinimum > $item->current_stock) {
                $this->sendLowStockNotification($item);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Minimum stock level updated successfully.',
                'minimum_stock' => $item->minimum_stock,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update minimum stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Housekeeping Inventory Reports
     */
    public function reports(Request $request)
    {
        $dateType = $request->get('date_type', 'daily');
        $customDate = $request->get('date', now()->format('Y-m-d'));
        
        // Date filtering
        $startDate = now();
        $endDate = now();
        
        switch ($dateType) {
            case 'daily':
                $startDate = Carbon::parse($customDate)->startOfDay();
                $endDate = Carbon::parse($customDate)->endOfDay();
                break;
            case 'weekly':
                $startDate = Carbon::parse($customDate)->startOfWeek();
                $endDate = Carbon::parse($customDate)->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::parse($customDate)->startOfMonth();
                $endDate = Carbon::parse($customDate)->endOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::parse($customDate)->startOfYear();
                $endDate = Carbon::parse($customDate)->endOfYear();
                break;
        }
        
        // 1. Items Received (from purchase requests)
        $receivedItems = \App\Models\ShoppingListItem::where('is_received_by_department', true)
            ->whereRaw('LOWER(TRIM(transferred_to_department)) = ?', ['housekeeping'])
            ->whereBetween('received_by_department_at', [$startDate, $endDate])
            ->with(['purchaseRequest.requestedBy', 'shoppingList'])
            ->orderBy('received_by_department_at', 'desc')
            ->get();
        
        $totalReceivedItems = $receivedItems->count();
        $totalReceivedQuantity = $receivedItems->sum('purchased_quantity');
        $totalReceivedCost = $receivedItems->sum('purchased_cost');
        
        // 2. Stock Movements (Consumption, Cleaning Use & Transfers)
        $stockMovements = InventoryStockMovement::with(['inventoryItem', 'performedBy', 'room'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('movement_type', ['consumption', 'cleaning_use', 'transfer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalConsumed = $stockMovements->whereIn('movement_type', ['consumption', 'cleaning_use'])->sum('quantity');
        $totalTransferred = $stockMovements->where('movement_type', 'transfer')->sum('quantity');
        $totalUsed = $totalConsumed + $totalTransferred;
        
        // Group movements by item
        $movementsByItem = $stockMovements->groupBy('inventory_item_id');
        
        // 3. Current Inventory Status (Calculated Backwards from Current Stock to handle missing history)
        $inventoryItems = HousekeepingInventoryItem::orderBy('name')->get()->map(function($item) use ($startDate, $endDate) {
            
            // A. Determine Closing Stock at the End of Date Range
            $currentRealStock = (float)$item->current_stock;
            $periodClosingStock = $currentRealStock;

            // If report is for a past period, roll back from NOW to end of period
            if ($endDate->lt(now()->startOfDay())) {
                $futureMovements = InventoryStockMovement::where('inventory_item_id', $item->id)
                    ->where('created_at', '>', $endDate)
                    ->get();
                
                foreach ($futureMovements as $m) {
                    if ($m->movement_type === 'adjustment') {
                         // Adjustments make rollback ambiguous, but we assume continuity for now
                         continue; 
                    }
                    if (in_array($m->movement_type, ['supply', 'manual_add'])) {
                        $periodClosingStock -= $m->quantity;
                    } elseif (in_array($m->movement_type, ['consumption', 'cleaning_use', 'transfer', 'expired'])) {
                        $periodClosingStock += $m->quantity;
                    }
                }
            }

            // B. Get Movements DURING the period
            $movementsIn = InventoryStockMovement::where('inventory_item_id', $item->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $received = $movementsIn->whereIn('movement_type', ['supply', 'manual_add'])->sum('quantity');
            $consumed = $movementsIn->whereIn('movement_type', ['consumption', 'cleaning_use'])->sum('quantity');
            $expired = $movementsIn->where('movement_type', 'expired')->sum('quantity'); 
            $transferred = $movementsIn->where('movement_type', 'transfer')->sum('quantity');
            
            $totalOut = $consumed + $expired + $transferred;
            
            // C. Derive Opening Stock
            // Logic: Opening + Received - TotalOut = Closing
            // Therefore: Opening = Closing - Received + TotalOut
            $openingStock = $periodClosingStock - $received + $totalOut;
            if ($openingStock < 0) $openingStock = 0; // Safety clamp

            // Attach calculated values
            $item->opening_stock = $openingStock;
            $item->received_in_period = $received;
            $item->consumed_in_period = $consumed + $transferred;
            $item->expired_in_period = $expired;
            $item->closing_stock = $periodClosingStock;
            $item->status_label = ($periodClosingStock <= $item->minimum_stock) ? 'Low Stock' : 'Good';
            
            return $item;
        });
        
        $lowStockItems = $inventoryItems->filter(function($item) {
            return $item->isLowStock();
        });
        
        $criticalStockItems = $inventoryItems->filter(function($item) {
            return $item->current_stock <= 0;
        });
        
        // 4. Stock Movements by Room (if applicable)
        $movementsByRoom = $stockMovements->whereNotNull('room_id')
            ->groupBy('room_id')
            ->map(function($movements) {
                return [
                    'room' => $movements->first()->room,
                    'items' => $movements->groupBy('inventory_item_id'),
                    'total_quantity' => $movements->sum('quantity')
                ];
            });
        
        return view('dashboard.housekeeper-reports', compact(
            'dateType',
            'customDate',
            'startDate',
            'endDate',
            'receivedItems',
            'totalReceivedItems',
            'totalReceivedQuantity',
            'totalReceivedCost',
            'stockMovements',
            'totalConsumed',
            'totalTransferred',
            'totalUsed',
            'movementsByItem',
            'inventoryItems',
            'lowStockItems',
            'criticalStockItems',
            'movementsByRoom'
        ));
    }

    /**
     * Send low stock notification email
     */
    private function sendLowStockNotification(HousekeepingInventoryItem $item)
    {
        try {
            // Get managers and housekeeping staff
            $recipients = \App\Models\Staff::where(function($query) {
                $query->whereIn('role', ['manager', 'super_admin'])
                      ->orWhereRaw('LOWER(TRIM(role)) = ?', ['housekeeper']);
            })
            ->where('is_active', true)
            ->get();
            
            foreach ($recipients as $staff) {
                if ($staff->isNotificationEnabled('inventory')) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($staff->email)
                            ->send(new \App\Mail\LowStockNotificationMail($item));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send low stock email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send low stock notifications: ' . $e->getMessage());
        }
    }

    /**
     * Report room issue
     */
    public function reportRoomIssue(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);
        
        $housekeeper = Auth::guard('staff')->user();
        
        $issue = RoomIssue::create([
            'room_id' => $request->room_id,
            'reported_by' => $housekeeper->id,
            'issue_type' => $request->issue_type,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'reported',
        ]);

        // Fix: Automatically set room status to maintenance when an issue is reported
        if ($issue->room) {
            $issue->room->update(['status' => 'maintenance']);
        }
        
        // Create notifications for managers and reception staff
        $managersAndReception = \App\Models\Staff::whereIn('role', ['manager', 'reception'])
            ->where('is_active', true)
            ->get();
        
        foreach ($managersAndReception as $staff) {
            // Create in-app notification
            \App\Models\Notification::create([
                'user_id' => $staff->id,
                'user_type' => 'staff',
                'type' => 'room_issue_reported',
                'title' => 'New Room Issue Reported',
                'message' => "Room {$issue->room->room_number}: {$issue->issue_type} ({$issue->priority} priority)",
                'data' => json_encode([
                    'issue_id' => $issue->id,
                    'room_id' => $issue->room_id,
                    'room_number' => $issue->room->room_number,
                    'issue_type' => $issue->issue_type,
                    'priority' => $issue->priority,
                    'reported_by' => $housekeeper->name,
                ]),
                'action_url' => route('admin.rooms.issues'),
                'is_read' => false,
            ]);
            
            // Send email notification if enabled
            if ($staff->isNotificationEnabled('room_issues')) {
                try {
                    Mail::to($staff->email)->send(
                        new \App\Mail\RoomIssueReportedMail($issue)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send room issue notification: ' . $e->getMessage());
                }
            }

            // SMS notification
            if ($staff->phone) {
                try {
                    $smsService = app(\App\Services\SmsService::class);
                    $smsMessage = "Housekeeping Alert: Room {$issue->room->room_number} issue reported: {$issue->issue_type}. Priority: " . strtoupper($issue->priority);
                    $smsService->sendSms($staff->phone, $smsMessage);
                } catch (\Exception $e) {
                    \Log::error("Failed to send room issue SMS to staff: " . $e->getMessage());
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Room issue reported successfully.',
            'issue' => $issue->load(['room', 'reportedBy']),
        ]);
    }

    /**
     * Show room issues
     */
    public function roomIssues()
    {
        $issues = RoomIssue::with(['room', 'reportedBy', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $currentUser = Auth::guard('staff')->user();
        if ($currentUser->role === 'manager' || $currentUser->role === 'reception') {
            return view('dashboard.admin-room-issues', compact('issues'));
        }
        
        return view('dashboard.housekeeper-room-issues', compact('issues'));
    }

    /**
     * Update room issue status
     */
    public function updateIssueStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:reported,in_progress,resolved,cancelled',
        ]);
        
        $issue = RoomIssue::findOrFail($id);
        $issue->update([
            'status' => $request->status,
            'resolved_at' => $request->status === 'resolved' ? now() : $issue->resolved_at,
        ]);

        // Automatically update room status based on issue status
        if ($issue->room) {
            if ($request->status === 'resolved' || $request->status === 'cancelled') {
                // If resolved or cancelled, check if there are other active issues
                $activeIssuesFetch = RoomIssue::where('room_id', $issue->room_id)
                    ->whereIn('status', ['reported', 'in_progress'])
                    ->where('id', '!=', $issue->id)
                    ->count();
                
                if ($activeIssuesFetch === 0) {
                    $issue->room->update(['status' => 'available']);
                }
            } else {
                // If reported or in_progress, set room to maintenance
                $issue->room->update(['status' => 'maintenance']);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Issue status updated successfully.',
            'issue' => $issue->load(['room', 'reportedBy']),
        ]);
    }

    /**
     * Show room status overview
     */
    public function roomStatus()
    {
        $rooms = Room::with(['latestCleaningLog', 'issues' => function($query) {
            $query->where('status', '!=', 'resolved');
        }])
        ->orderBy('room_number')
        ->get();
        
        $statusCounts = [
            'available' => Room::where('status', 'available')->count(),
            'occupied' => Room::where('status', 'occupied')->count(),
            'to_be_cleaned' => Room::where('status', 'to_be_cleaned')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];
        
        return view('dashboard.housekeeper-room-status', compact('rooms', 'statusCounts'));
    }

    /**
     * Get item usage track history
     */
    public function getItemUsageTrack($id)
    {
        $item = HousekeepingInventoryItem::findOrFail($id);
        
        $movements = InventoryStockMovement::where('inventory_item_id', $id)
            ->with(['performedBy', 'room', 'inventoryItem'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
            
        $runningBalance = $item->current_stock;
        $formattedMovements = [];
        
        foreach ($movements as $m) {
            $isAddition = in_array($m->movement_type, ['supply']);
            $type = ucfirst(str_replace('_', ' ', $m->movement_type));
            
            if ($m->movement_type == 'consumption') {
                $type = $m->room_id ? "Room Distribution" : "Consumption";
            }
            
            $formattedMovements[] = [
                'date' => $m->created_at->format('M d, Y H:i'),
                'type' => $type,
                'quantity' => ($isAddition ? '+' : '-') . number_format($m->quantity, 1),
                'balance' => number_format($runningBalance, 1),
                'unit' => $m->inventoryItem->unit ?? '',
                'user' => $m->performedBy->name ?? 'System',
                'notes' => ($m->room ? "Room {$m->room->room_number} " : "") . $m->notes,
                'is_addition' => $isAddition,
                'is_price_change' => false
            ];
            
            // Calculate what the balance was BEFORE this movement to continue working backwards
            if ($m->movement_type === 'adjustment') {
                // For adjustments, we don't know the previous balance easily without more history
                // but we can assume the quantity WAS the new balance. This is imperfect if many adjustments exist.
                // However, for most common cases (supply/consumption), the math below is solid.
                $runningBalance = 0; // Fallback or we could try to guess, but skipping for safety
            } else {
                if ($isAddition) {
                    $runningBalance -= $m->quantity;
                } else {
                    $runningBalance += $m->quantity;
                }
            }
        }
            
        return response()->json([
            'success' => true,
            'movements' => $formattedMovements
        ]);
    }
}

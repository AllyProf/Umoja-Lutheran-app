<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::orderBy('created_at', 'desc')->get();
        
        // Calculate statistics
        $stats = [
            'total' => $rooms->count(),
            'available' => $rooms->where('status', 'available')->count(),
            'occupied' => $rooms->where('status', 'occupied')->count(),
            'to_be_cleaned' => $rooms->where('status', 'to_be_cleaned')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
        ];
        
        // Calculate statistics by room type
        $statsByType = [];
        $roomTypes = ['Single', 'Double', 'Twins'];
        
        foreach ($roomTypes as $type) {
            $typeRooms = $rooms->where('room_type', $type);
            $statsByType[$type] = [
                'total' => $typeRooms->count(),
                'available' => $typeRooms->where('status', 'available')->count(),
                'occupied' => $typeRooms->where('status', 'occupied')->count(),
                'to_be_cleaned' => $typeRooms->where('status', 'to_be_cleaned')->count(),
                'maintenance' => $typeRooms->where('status', 'maintenance')->count(),
            ];
        }
        
        // Detect user role
        $user = auth()->user();
        $userRole = $user->role ?? 'manager';
        $role = $userRole === 'super_admin' ? 'super_admin' : 'manager';
        $userName = $user->name ?? 'Manager';
        $userRoleDisplay = $userRole === 'super_admin' ? 'Super Administrator' : 'Manager';
        
        return view('dashboard.rooms-list', [
            'rooms' => $rooms,
            'stats' => $stats,
            'statsByType' => $statsByType,
            'role' => $role,
            'userName' => $userName,
            'userRole' => $userRoleDisplay
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Detect user role
        $user = auth()->user();
        $userRole = $user->role ?? 'manager';
        $role = $userRole === 'super_admin' ? 'super_admin' : 'manager';
        $userName = $user->name ?? 'Manager';
        $userRoleDisplay = $userRole === 'super_admin' ? 'Super Administrator' : 'Manager';
        
        return view('dashboard.rooms', [
            'role' => $role,
            'userName' => $userName,
            'userRole' => $userRoleDisplay
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Normalize empty time strings to null before validation
            $request->merge([
                'checkin_time' => $request->checkin_time === '' || $request->checkin_time === null ? null : $request->checkin_time,
                'checkout_time' => $request->checkout_time === '' || $request->checkout_time === null ? null : $request->checkout_time,
            ]);

            // Check if bulk creation is enabled
            $isBulkCreation = $request->has('enable_bulk_create') && $request->enable_bulk_create == '1';
            
            // Validate the request - room_type is always required
            $validationRules = [
                'room_type' => 'required|in:Single,Double,Twins',
                'capacity' => 'required|integer|min:1|max:10',
                'bed_type' => 'required|string',
                'floor_location' => 'nullable|string|max:255',
                'sku_code' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price_per_night' => 'required|numeric|min:0',
                'extra_guest_fee' => 'nullable|numeric|min:0',
                'peak_season_price' => 'nullable|numeric|min:0',
                'off_season_price' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'promo_code' => 'nullable|string|max:255',
                'amenities' => 'nullable|array',
                'amenities.*' => 'string',
                'bathroom_type' => 'nullable|string|max:255',
                'checkin_time' => 'nullable|date_format:H:i',
                'checkout_time' => 'nullable|date_format:H:i',
                'pet_friendly' => 'nullable',
                'smoking_allowed' => 'nullable',
                'special_notes' => 'nullable|string',
                'wifi_password' => 'nullable|string|max:255',
                'wifi_network_name' => 'nullable|string|max:255',
                'room_images' => 'nullable|array',
                'room_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max per image
            ];
            
            // Add bulk creation validation rules
            if ($isBulkCreation) {
                $validationRules['bulk_quantity'] = 'required|integer|min:2|max:50';
                $validationRules['assignment_method'] = 'required|in:auto,manual';
                
                if ($request->assignment_method == 'auto') {
                    $validationRules['starting_room_number'] = 'required|string';
                } else {
                    $validationRules['manual_room_numbers'] = 'required|string';
                }
            } else {
                $validationRules['room_number'] = 'required|string|unique:rooms,room_number';
            }
            
            $validated = $request->validate($validationRules);

            // Handle image uploads
            $imagePaths = [];
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $image) {
                    $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('rooms', $filename, 'public');
                    $imagePaths[] = $path;
                    // Sync to public directory for Windows compatibility
                    $this->syncFileToPublic($path);
                }
            }

            // Base room data (same for all rooms in bulk creation)
            $baseRoomData = [
                'room_type' => $validated['room_type'],
                'capacity' => $validated['capacity'],
                'bed_type' => $validated['bed_type'],
                'floor_location' => $validated['floor_location'] ?? null,
                'sku_code' => $validated['sku_code'] ?? null,
                'description' => $validated['description'] ?? null,
                'price_per_night' => $validated['price_per_night'],
                'extra_guest_fee' => $validated['extra_guest_fee'] ?? null,
                'peak_season_price' => $validated['peak_season_price'] ?? null,
                'off_season_price' => $validated['off_season_price'] ?? null,
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'promo_code' => $validated['promo_code'] ?? null,
                'amenities' => $validated['amenities'] ?? [],
                'bathroom_type' => $validated['bathroom_type'] ?? null,
                'checkin_time' => $validated['checkin_time'] ?? null,
                'checkout_time' => $validated['checkout_time'] ?? null,
                'pet_friendly' => $request->has('pet_friendly') && $request->pet_friendly ? true : false,
                'smoking_allowed' => $request->has('smoking_allowed') && $request->smoking_allowed ? true : false,
                'special_notes' => $validated['special_notes'] ?? null,
                'wifi_password' => $validated['wifi_password'] ?? null,
                'wifi_network_name' => $validated['wifi_network_name'] ?? null,
                'images' => $imagePaths,
                'status' => 'available',
            ];

            // Handle bulk creation
            if ($isBulkCreation) {
                return $this->handleBulkRoomCreation($validated, $baseRoomData);
            }

            // Single room creation
            $room = Room::create(array_merge($baseRoomData, [
                'room_number' => $validated['room_number'],
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully!',
                'room' => $room
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk room creation
     */
    private function handleBulkRoomCreation($validated, $baseRoomData)
    {
        try {
            $quantity = $validated['bulk_quantity'];
            $assignmentMethod = $validated['assignment_method'];
            
            // Get existing room numbers to avoid duplicates
            $existingRoomNumbers = Room::pluck('room_number')->toArray();
            
            // Generate room numbers based on assignment method
            $roomNumbers = [];
            
            if ($assignmentMethod == 'auto') {
                // Auto-generate sequential room numbers
                $startingNumber = $validated['starting_room_number'];
                
                // Extract prefix and numeric part
                if (!preg_match('/^([^0-9]*)(\d+)$/', $startingNumber, $matches)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid starting room number format. Please use format like "201" or "A201".'
                    ], 422);
                }
                
                $prefix = $matches[1];
                $startNum = (int)$matches[2];
                $currentNum = $startNum;
                $attempts = 0;
                $maxAttempts = 1000; // Prevent infinite loop
                
                while (count($roomNumbers) < $quantity && $attempts < $maxAttempts) {
                    $roomNumber = $prefix . $currentNum;
                    if (!in_array($roomNumber, $existingRoomNumbers) && !in_array($roomNumber, $roomNumbers)) {
                        $roomNumbers[] = $roomNumber;
                    }
                    $currentNum++;
                    $attempts++;
                }
                
                if (count($roomNumbers) < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Could only generate " . count($roomNumbers) . " unique room numbers. Please try a different starting number or use manual assignment."
                    ], 422);
                }
            } else {
                // Manual assignment - only accept commas as separator
                $input = trim($validated['manual_room_numbers']);
                
                // Check for invalid separators
                if (preg_match('/[.;|]/', $input)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid separator detected. Please use commas (,) to separate room numbers. Example: 100, 204, 4046"
                    ], 422);
                }
                
                $inputNumbers = array_map('trim', explode(',', $input));
                $inputNumbers = array_values(array_filter($inputNumbers, function($num) {
                    return !empty($num) && trim($num) !== '';
                }));
                
                // Validate exact quantity match in input (before checking for existing rooms)
                $inputCount = count($inputNumbers);
                if ($inputCount !== (int)$quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "You provided " . $inputCount . " room number(s), but need exactly " . (int)$quantity . ". Please provide exactly " . (int)$quantity . " room numbers separated by commas."
                    ], 422);
                }
                
                // Validate and filter room numbers
                $duplicateInInput = [];
                $existingInInput = [];
                foreach ($inputNumbers as $roomNumber) {
                    $roomNumber = trim($roomNumber);
                    if (empty($roomNumber)) {
                        continue;
                    }
                    
                    // Check for duplicates in the input itself
                    if (in_array($roomNumber, $roomNumbers)) {
                        $duplicateInInput[] = $roomNumber;
                        continue;
                    }
                    
                    // Check if room number already exists in database
                    if (in_array($roomNumber, $existingRoomNumbers)) {
                        $existingInInput[] = $roomNumber;
                        continue; // Skip existing room numbers
                    }
                    
                    $roomNumbers[] = $roomNumber;
                }
                
                // Check for duplicates in input
                if (!empty($duplicateInInput)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Duplicate room numbers found in your input: " . implode(', ', array_unique($duplicateInInput)) . ". Please provide unique room numbers."
                    ], 422);
                }
                
                // Validate we have exactly the required quantity of valid unique room numbers
                $validCount = count($roomNumbers);
                if ($validCount < (int)$quantity) {
                    $missing = (int)$quantity - $validCount;
                    $errorMsg = "Only " . $validCount . " valid unique room number(s) found.";
                    if (!empty($existingInInput)) {
                        $errorMsg .= " The following room number(s) already exist: " . implode(', ', array_unique($existingInInput)) . ".";
                    }
                    $errorMsg .= " Please provide " . (int)$quantity . " unique room numbers that don't already exist.";
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg
                    ], 422);
                }
            }
            
            // Create rooms in a transaction
            DB::beginTransaction();
            
            try {
                $createdRooms = [];
                foreach ($roomNumbers as $roomNumber) {
                    $room = Room::create(array_merge($baseRoomData, [
                        'room_number' => $roomNumber,
                    ]));
                    $createdRooms[] = $room;
                }
                
                DB::commit();
                
                // Get room type display name
                $roomTypeNames = [
                    'Single' => 'Single Room',
                    'Double' => 'Double Room',
                    'Twins' => 'Standard Twin Room'
                ];
                $roomTypeDisplay = $roomTypeNames[$baseRoomData['room_type']] ?? 'Room';
                
                return response()->json([
                    'success' => true,
                    'message' => "Successfully created " . count($createdRooms) . " " . $roomTypeDisplay . "(s)!",
                    'rooms' => $createdRooms,
                    'room_numbers' => $roomNumbers
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load('bookings');
        return response()->json([
            'success' => true,
            'room' => $room
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        // Detect user role
        $user = auth()->user();
        $userRole = $user->role ?? 'manager';
        $role = $userRole === 'super_admin' ? 'super_admin' : 'manager';
        $userName = $user->name ?? 'Manager';
        $userRoleDisplay = $userRole === 'super_admin' ? 'Super Administrator' : 'Manager';
        
        return view('dashboard.rooms', [
            'room' => $room,
            'role' => $role,
            'userName' => $userName,
            'userRole' => $userRoleDisplay
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        try {
            // Normalize empty time strings to null before validation
            $request->merge([
                'checkin_time' => $request->checkin_time === '' || $request->checkin_time === null ? null : $request->checkin_time,
                'checkout_time' => $request->checkout_time === '' || $request->checkout_time === null ? null : $request->checkout_time,
            ]);

            // Validate the request
            $validated = $request->validate([
                'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
                'room_type' => 'required|in:Single,Double,Twins',
                'capacity' => 'required|integer|min:1|max:10',
                'bed_type' => 'required|string',
                'floor_location' => 'nullable|string|max:255',
                'sku_code' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price_per_night' => 'required|numeric|min:0',
                'extra_guest_fee' => 'nullable|numeric|min:0',
                'peak_season_price' => 'nullable|numeric|min:0',
                'off_season_price' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'promo_code' => 'nullable|string|max:255',
                'amenities' => 'nullable|array',
                'amenities.*' => 'string',
                'bathroom_type' => 'nullable|string|max:255',
                'checkin_time' => 'nullable|date_format:H:i',
                'checkout_time' => 'nullable|date_format:H:i',
                'pet_friendly' => 'nullable',
                'smoking_allowed' => 'nullable',
                'special_notes' => 'nullable|string',
                'wifi_password' => 'nullable|string|max:255',
                'wifi_network_name' => 'nullable|string|max:255',
                'room_images' => 'nullable|array',
                'room_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                'remove_images' => 'nullable|array',
            ]);

            // Handle image removal
            $currentImages = $room->images ?? [];
            if ($request->has('remove_images')) {
                $imagesToRemove = $request->remove_images;
                $remainingImages = array_diff($currentImages, $imagesToRemove);
                
                // Delete files from storage
                foreach ($imagesToRemove as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
                
                $currentImages = array_values($remainingImages);
            }

            // Handle new image uploads
            if ($request->hasFile('room_images')) {
                foreach ($request->file('room_images') as $image) {
                    $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('rooms', $filename, 'public');
                    $currentImages[] = $path;
                    // Sync to public directory for Windows compatibility
                    $this->syncFileToPublic($path);
                }
            }

            // Update the room
            $room->update([
                'room_number' => $validated['room_number'],
                'room_type' => $validated['room_type'],
                'capacity' => $validated['capacity'],
                'bed_type' => $validated['bed_type'],
                'floor_location' => $validated['floor_location'] ?? null,
                'sku_code' => $validated['sku_code'] ?? null,
                'description' => $validated['description'] ?? null,
                'price_per_night' => $validated['price_per_night'],
                'extra_guest_fee' => $validated['extra_guest_fee'] ?? null,
                'peak_season_price' => $validated['peak_season_price'] ?? null,
                'off_season_price' => $validated['off_season_price'] ?? null,
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'promo_code' => $validated['promo_code'] ?? null,
                'amenities' => $validated['amenities'] ?? [],
                'bathroom_type' => $validated['bathroom_type'] ?? null,
                'checkin_time' => $validated['checkin_time'] ?? null,
                'checkout_time' => $validated['checkout_time'] ?? null,
                'pet_friendly' => $request->has('pet_friendly') && $request->pet_friendly ? true : false,
                'smoking_allowed' => $request->has('smoking_allowed') && $request->smoking_allowed ? true : false,
                'special_notes' => $validated['special_notes'] ?? null,
                'wifi_password' => $validated['wifi_password'] ?? null,
                'wifi_network_name' => $validated['wifi_network_name'] ?? null,
                'images' => $currentImages,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully!',
                'room' => $room->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        try {
            // Check if room has active bookings
            $activeBookings = $room->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('check_out', '>=', now())
                ->count();

            if ($activeBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete room with active or upcoming bookings. Please cancel bookings first.',
                ], 422);
            }

            // Delete room images
            if ($room->images && is_array($room->images)) {
                foreach ($room->images as $imagePath) {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }
            }

            // Delete the room
            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk actions on rooms
     */
    public function bulkAction(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:status,price,delete',
                'room_ids' => 'required|array|min:1',
                'room_ids.*' => 'exists:rooms,id',
                'value' => 'required_if:action,status,price',
            ]);

            $action = $validated['action'];
            $roomIds = $validated['room_ids'];
            $rooms = Room::whereIn('id', $roomIds)->get();

            if ($rooms->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No rooms found to update.'
                ], 422);
            }

            DB::beginTransaction();

            try {
                $updatedCount = 0;
                $deletedCount = 0;
                $errors = [];

                foreach ($rooms as $room) {
                    try {
                        if ($action === 'status') {
                            $room->status = $validated['value'];
                            $room->save();
                            $updatedCount++;
                        } elseif ($action === 'price') {
                            $room->price_per_night = $validated['value'];
                            $room->save();
                            $updatedCount++;
                        } elseif ($action === 'delete') {
                            // Check for active bookings
                            $activeBookings = $room->bookings()
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->where('check_out', '>=', now())
                                ->count();

                            if ($activeBookings > 0) {
                                $errors[] = "Room {$room->room_number} has active bookings and cannot be deleted.";
                                continue;
                            }

                            // Delete room images
                            if ($room->images && is_array($room->images)) {
                                foreach ($room->images as $imagePath) {
                                    if (Storage::disk('public')->exists($imagePath)) {
                                        Storage::disk('public')->delete($imagePath);
                                    }
                                }
                            }

                            $room->delete();
                            $deletedCount++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error processing room {$room->room_number}: " . $e->getMessage();
                    }
                }

                DB::commit();

                $message = '';
                if ($action === 'status') {
                    $message = "Successfully updated status for {$updatedCount} room(s).";
                } elseif ($action === 'price') {
                    $message = "Successfully updated price for {$updatedCount} room(s).";
                } elseif ($action === 'delete') {
                    $message = "Successfully deleted {$deletedCount} room(s).";
                    if (!empty($errors)) {
                        $message .= " " . count($errors) . " room(s) could not be deleted due to active bookings.";
                    }
                }

                if (!empty($errors)) {
                    $message .= " Errors: " . implode(' ', $errors);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => $updatedCount,
                    'deleted_count' => $deletedCount,
                    'errors' => $errors
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show bulk edit form
     */
    public function bulkEdit(Request $request)
    {
        $roomIds = $request->get('room_ids', []);
        $roomType = $request->get('type', null); // Optional type filter
        
        $query = Room::whereIn('id', $roomIds);
        
        // If type filter is provided, filter by room type
        if ($roomType && in_array($roomType, ['Single', 'Double', 'Twins'])) {
            $query->where('room_type', $roomType);
        }
        
        $rooms = $query->get();

        if ($rooms->isEmpty()) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'No rooms selected for editing.');
        }

        // Exchange rate logic removed as only TZS is used exclusively

        return view('dashboard.rooms-bulk-edit', [
            'rooms' => $rooms,
            'roomIds' => $roomIds,
            'filteredType' => $roomType,
            'role' => 'manager',
            'userName' => 'Manager',
            'userRole' => 'Manager'
        ]);
    }

    /**
     * Update multiple rooms
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'room_ids' => 'required|array|min:1',
                'room_ids.*' => 'exists:rooms,id',
                'update_fields' => 'required|array',
            ]);

            $roomIds = $validated['room_ids'];
            $updateFields = $validated['update_fields'];
            $rooms = Room::whereIn('id', $roomIds)->get();

            DB::beginTransaction();

            try {
                $updatedCount = 0;
                foreach ($rooms as $room) {
                    // Only update fields that are provided and allowed
                    $allowedFields = [
                        'price_per_night', 'capacity', 'bed_type', 'status',
                        'floor_location', 'description', 'extra_guest_fee',
                        'peak_season_price', 'off_season_price', 'discount_percentage',
                        'bathroom_type', 'pet_friendly', 'smoking_allowed'
                    ];

                    foreach ($updateFields as $field => $value) {
                        if (in_array($field, $allowedFields) && $value !== null && $value !== '') {
                            // Handle boolean fields
                            if ($field === 'pet_friendly' || $field === 'smoking_allowed') {
                                $room->$field = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                            } else {
                                $room->$field = $value;
                            }
                        }
                    }

                    $room->save();
                    $updatedCount++;
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Successfully updated {$updatedCount} room(s)!",
                    'updated_count' => $updatedCount
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change room type and update related fields
     */
    public function changeType(Request $request)
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'room_type' => 'required|in:Single,Double,Twins',
                'capacity' => 'required|integer|min:1|max:10',
                'bed_type' => 'required|string|max:255',
                'price_per_night' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'room_images' => 'nullable|array',
                'room_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max per image
            ]);

            $room = Room::findOrFail($validated['room_id']);
            
            // Store old room type for message
            $oldType = $room->room_type;
            $newType = $validated['room_type'];
            
            // Get type display names
            $typeNames = [
                'Single' => 'Single Room',
                'Double' => 'Double Room',
                'Twins' => 'Standard Twin Room'
            ];
            
            $oldTypeDisplay = $typeNames[$oldType] ?? $oldType;
            $newTypeDisplay = $typeNames[$newType] ?? $newType;

            DB::beginTransaction();

            try {
                // Update room type and other fields
                $room->room_type = $validated['room_type'];
                $room->capacity = $validated['capacity'];
                $room->bed_type = $validated['bed_type'];
                $room->price_per_night = $validated['price_per_night'];
                
                if (isset($validated['description'])) {
                    $room->description = $validated['description'];
                }

                // Handle image uploads if provided
                if ($request->hasFile('room_images')) {
                    $uploadedImages = [];
                    $images = $request->file('room_images');
                    
                    foreach ($images as $image) {
                        $filename = 'rooms/' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                        $image->storeAs('public', $filename);
                        $uploadedImages[] = $filename;
                        // Sync to public directory for Windows compatibility
                        $this->syncFileToPublic($filename);
                    }
                    
                    // Merge with existing images or replace
                    $existingImages = $room->images ?? [];
                    if (is_array($existingImages)) {
                        $room->images = array_merge($existingImages, $uploadedImages);
                    } else {
                        $room->images = $uploadedImages;
                    }
                }

                $room->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Room type changed from {$oldTypeDisplay} to {$newTypeDisplay} successfully!",
                    'room' => $room
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change room type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync storage file to public directory (for Windows compatibility)
     * 
     * @param string $filePath Path relative to storage/app/public
     * @return bool
     */
    private function syncFileToPublic($filePath)
    {
        $storagePath = storage_path('app/public/' . $filePath);
        $publicPath = public_path('storage/' . $filePath);
        
        // Only sync if file exists in storage and doesn't exist in public
        if (file_exists($storagePath) && !file_exists($publicPath)) {
            // Create directory if it doesn't exist
            $publicDir = dirname($publicPath);
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }
            
            // Copy the file
            return copy($storagePath, $publicPath);
        }
        
        return true;
    }
}


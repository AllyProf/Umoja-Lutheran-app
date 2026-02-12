<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // null = all users of a role
            $table->string('type'); // booking, payment, service_request, maintenance
            $table->string('title');
            $table->text('message');
            $table->string('icon')->default('fa-info-circle'); // FontAwesome icon
            $table->string('color')->default('primary'); // primary, success, danger, warning, info
            $table->string('role')->nullable(); // manager, reception, guest - null = all roles
            $table->morphs('notifiable'); // polymorphic relation (booking_id, service_request_id, etc.)
            $table->string('link')->nullable(); // URL to related resource
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['role', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

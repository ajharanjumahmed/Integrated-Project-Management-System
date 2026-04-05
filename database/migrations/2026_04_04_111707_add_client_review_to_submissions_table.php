<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Tracks the client's own review decision — separate from the PM's.
            // 'pending' = sent to client but not yet reviewed
            // 'approved' = client happy
            // 'revision_requested' = client wants changes
            $table->enum('client_status', ['pending', 'approved', 'revision_requested'])
                  ->default('pending')
                  ->after('client_submitted');

            // Client's feedback note when requesting revision
            $table->text('client_note')->nullable()->after('client_status');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['client_status', 'client_note']);
        });
    }
};
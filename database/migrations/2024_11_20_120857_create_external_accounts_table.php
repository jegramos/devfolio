<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_accounts', function (Blueprint $table) {
            $table->id();

            // A user can only have one external account linked
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('provider')->index();
            $table->string('provider_id'); // The unique ID set by the provider to the account
            $table->longText('access_token')->nullable();
            $table->longText('id_token')->nullable(); // For OpenID Connect accounts
            $table->longText('refresh_token')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // An external account cannot be bound to multiple users
            $table->unique(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_accounts');
    }
};

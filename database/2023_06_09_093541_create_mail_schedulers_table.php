<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Gammer42\LaravelMailScheduler\Models\MailScheduler;
use Gammer42\LaravelMailScheduler\Models\MailSchedulerLog;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('mailator.schedulers_table_name', 'mailator_schedulers'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 100)->nullable();
            $table->boolean('stopable')->default(false);
            $table->boolean('unique')->default(false);
            $table->string('tags', 100)->nullable();
            $table->text('mailable_class')->nullable();
            $table->nullableMorphs('targetable');
            $table->text('action')->nullable();
            $table->unsignedInteger('delay_minutes')->nullable()->comment('Number of hours/days.');
            $table->enum('time_frame_origin', [
                MailScheduler::TIME_FRAME_ORIGIN_BEFORE,
                MailScheduler::TIME_FRAME_ORIGIN_AFTER,
            ])->nullable()->comment('Before or after event.');
            $table->timestamp('timestamp_target')->nullable();
            $table->json('constraints')->nullable()->comment('Offset target.');
            $table->json('recipients')->nullable();
            $table->text('when')->nullable();
            $table->string('frequency_option')->default(MailScheduler::FREQUENCY_OPTIONS_ONCE)->comment('How often send email notification.');

            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_failed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });

        Schema::create(config('mailator.logs_table', 'mailator_logs'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->json('recipients')->nullable();
            $table->foreignId('mailator_schedule_id')->nullable();
            $table->enum('status', [
                MailSchedulerLog::STATUS_FAILED,
                MailSchedulerLog::STATUS_SENT,
            ]);
            $table->dateTime('action_at')->nullable();
            $table->text('exception')->nullable();
            $table->timestamps();

            /**
             * Foreign keys
             */
            $table->foreign('mailator_schedule_id')
                ->references('id')
                ->on(config('mailator.schedulers_table_name', 'mailator_schedulers'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_schedulers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email');
                $table->string('password');
                $table->timestamps();
            });
        }

        Schema::create('mailcoach_email_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('campaigns_feed_enabled')->default(false);

            $table->string('default_from_email')->nullable();
            $table->string('default_from_name')->nullable();

            $table->string('default_reply_to_email')->nullable();
            $table->string('default_reply_to_name')->nullable();

            $table->boolean('allow_form_subscriptions')->default(false);

            $table->string('redirect_after_subscribed')->nullable();
            $table->string('redirect_after_already_subscribed')->nullable();
            $table->string('redirect_after_subscription_pending')->nullable();
            $table->string('redirect_after_unsubscribed')->nullable();

            $table->boolean('requires_confirmation')->default(false);
            $table->foreignId('confirmation_mail_id')->nullable();
            $table->string('confirmation_mailable_class')->nullable();

            $table->string('campaign_mailer')->nullable();
            $table->string('automation_mailer')->nullable();
            $table->string('transactional_mailer')->nullable();

            $table->string('report_recipients')->nullable();
            $table->boolean('report_campaign_sent')->default(false);
            $table->boolean('report_campaign_summary')->default(false);
            $table->boolean('report_email_list_summary')->default(false);

            $table->timestamp('email_list_summary_sent_at')->nullable();
            $table->text('allowed_form_extra_attributes')->nullable();
            $table->string('honeypot_field')->nullable();

            $table->boolean('has_website')->default(false);
            $table->boolean('show_subscription_form_on_website')->default(true);
            $table->string('website_slug')->nullable();
            $table->string('website_title')->nullable();
            $table->text('website_intro')->nullable();
            $table->string('website_primary_color')->default('hsl(0, 0%, 0%)');
            $table->string('website_theme')->default('default');
            $table->text('website_subscription_description')->nullable();

            $table->timestamps();
        });

        Schema::create('mailcoach_subscribers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('email_list_id')
                ->constrained('mailcoach_email_lists')
                ->cascadeOnDelete();

            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->json('extra_attributes')->nullable();

            $table->uuid('imported_via_import_uuid')->nullable();

            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->nullableTimestamps();

            $table->index([
                'email_list_id',
                'subscribed_at',
                'unsubscribed_at',
            ],
                'email_list_subscribed_index');

            $table->index(['email_list_id', 'created_at'], 'email_list_id_created_at');
        });

        Schema::create('mailcoach_segments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->boolean('all_positive_tags_required')->default(false);
            $table->boolean('all_negative_tags_required')->default(false);
            $table
                ->foreignId('email_list_id')
                ->constrained('mailcoach_email_lists')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();

            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();

            $table->string('reply_to_email')->nullable();
            $table->string('reply_to_name')->nullable();

            $table->string('subject')->nullable();

            $table->boolean('show_publicly')->default(true);

            $table
                ->foreignId('email_list_id')
                ->nullable()
                ->constrained('mailcoach_email_lists')
                ->nullOnDelete();

            $table->unsignedBigInteger('template_id')->nullable();

            $table->string('status');

            $table->longText('html')->nullable();
            $table->longText('structured_html')->nullable();
            $table->longText('email_html')->nullable();
            $table->longText('webview_html')->nullable();

            $table->string('mailable_class')->nullable();
            $table->json('mailable_arguments')->nullable();

            $table->boolean('utm_tags')->default(false);

            $table->integer('sent_to_number_of_subscribers')->default(0);
            $table->text('segment_class')->nullable();

            $table
                ->foreignId('segment_id')
                ->nullable()
                ->constrained('mailcoach_segments')
                ->nullOnDelete();

            $table->string('segment_description')->default(0);

            $table->boolean('add_subscriber_tags')->default(false);
            $table->boolean('add_subscriber_link_tags')->default(false);

            $table->integer('open_count')->default(0);
            $table->integer('unique_open_count')->default(0);
            $table->integer('open_rate')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('unique_click_count')->default(0);
            $table->integer('click_rate')->default(0);
            $table->integer('unsubscribe_count')->default(0);
            $table->integer('unsubscribe_rate')->default(0);
            $table->integer('bounce_count')->default(0);
            $table->integer('bounce_rate')->default(0);

            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('statistics_calculated_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();

            $table->timestamp('all_sends_created_at')->nullable();
            $table->timestamp('all_sends_dispatched_at')->nullable();

            $table->timestamp('last_modified_at')->nullable();

            $table->timestamp('summary_mail_sent_at')->nullable();

            $table->timestamps();

            $table->index(['scheduled_at', 'status']);
        });

        Schema::create('mailcoach_campaign_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table
                ->foreignId('campaign_id')
                ->constrained('mailcoach_campaigns')
                ->cascadeOnDelete();

            $table->string('url', 2048);
            $table->integer('click_count')->default(0);
            $table->integer('unique_click_count')->default(0);
            $table->nullableTimestamps();
        });

        Schema::create('mailcoach_transactional_mail_log_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->text('subject');

            $table->json('from');
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->json('attachments')->nullable();
            $table->longText('body')->nullable();
            $table->longText('structured_html')->nullable();

            $table->string('mailable_class');

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_mails', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();

            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();

            $table->string('reply_to_email')->nullable();
            $table->string('reply_to_name')->nullable();

            $table->string('subject')->nullable();

            $table->unsignedBigInteger('template_id')->nullable();
            $table->longText('html')->nullable();
            $table->longText('structured_html')->nullable();
            $table->longText('email_html')->nullable();
            $table->longText('webview_html')->nullable();

            $table->string('mailable_class')->nullable();
            $table->json('mailable_arguments')->nullable();

            $table->boolean('utm_tags')->default(false);
            $table->boolean('add_subscriber_tags')->default(false);
            $table->boolean('add_subscriber_link_tags')->default(false);

            $table->integer('sent_to_number_of_subscribers')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('unique_open_count')->default(0);
            $table->integer('open_rate')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('unique_click_count')->default(0);
            $table->integer('click_rate')->default(0);
            $table->integer('unsubscribe_count')->default(0);
            $table->integer('unsubscribe_rate')->default(0);
            $table->integer('bounce_count')->default(0);
            $table->integer('bounce_rate')->default(0);
            $table->timestamp('statistics_calculated_at')->nullable();

            $table->timestamp('last_modified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_sends', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('transport_message_id')->nullable()->index();

            $table
                ->foreignId('campaign_id')
                ->nullable()
                ->constrained('mailcoach_campaigns')
                ->cascadeOnDelete();

            $table
                ->foreignId('automation_mail_id')
                ->nullable()
                ->constrained('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table
                ->foreignId('transactional_mail_log_item_id')
                ->nullable()
                ->constrained('mailcoach_transactional_mail_log_items')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->timestamp('sending_job_dispatched_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('invalidated_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->unique('transport_message_id');
            $table->index(['campaign_id', 'subscriber_id']);
            $table->index(['transactional_mail_log_item_id']);
            $table->index(['sending_job_dispatched_at', 'sent_at'], 'sent_index');
        });

        Schema::create('mailcoach_campaign_clicks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table
                ->foreignId('campaign_link_id')
                ->constrained('mailcoach_campaign_links')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->nullableTimestamps();
        });

        Schema::create('mailcoach_campaign_opens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table
                ->foreignId('campaign_id')
                ->nullable()
                ->constrained('mailcoach_campaigns')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->nullableTimestamps();
        });

        Schema::create('mailcoach_campaign_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('campaign_id')
                ->constrained('mailcoach_campaigns')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_send_feedback_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type');

            $table
                ->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table->json('extra_attributes')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->boolean('contains_placeholders')->default(false);
            $table->longText('html');
            $table->longText('structured_html')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_subscriber_imports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->longText('subscribers_csv')->nullable();
            $table->string('status');

            $table
                ->foreignId('email_list_id')
                ->nullable()
                ->constrained('mailcoach_email_lists')
                ->nullOnDelete();

            $table->boolean('subscribe_unsubscribed')->default(false);
            $table->boolean('unsubscribe_others')->default(false);
            $table->boolean('replace_tags')->default(false);
            $table->integer('imported_subscribers_count')->default(0);
            $table->text('errors')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_tags', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('type')->default('default');
            $table->boolean('visible_in_preferences')->default(false);

            $table
                ->foreignId('email_list_id')
                ->nullable()
                ->constrained('mailcoach_email_lists')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_email_list_subscriber_tags', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->nullOnDelete();

            $table
                ->foreignId('tag_id')
                ->nullable()
                ->constrained('mailcoach_tags')
                ->nullOnDelete();

            $table->index(['subscriber_id', 'tag_id'], 'subscriber_id_tag_id_index');
        });

        Schema::create('mailcoach_email_list_allow_form_subscription_tags', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('email_list_id')
                ->nullable()
                ->constrained('mailcoach_email_lists')
                ->index('tags_email_list_id')
                ->nullOnDelete();

            $table
                ->foreignId('tag_id')
                ->nullable()
                ->constrained('mailcoach_tags')
                ->index('tags_tag_id')
                ->nullOnDelete();
        });

        Schema::create('mailcoach_positive_segment_tags', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('segment_id')
                ->nullable()
                ->constrained('mailcoach_segments')
                ->nullOnDelete();

            $table
                ->foreignId('tag_id')
                ->nullable()
                ->constrained('mailcoach_tags')
                ->nullOnDelete();
        });

        Schema::create('mailcoach_negative_segment_tags', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('segment_id')
                ->nullable()
                ->constrained('mailcoach_segments')
                ->nullOnDelete();

            $table
                ->foreignId('tag_id')
                ->nullable()
                ->constrained('mailcoach_tags')
                ->nullOnDelete();
        });

        Schema::create('mailcoach_automations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('email_list_id')
                ->nullable()
                ->constrained('mailcoach_email_lists')
                ->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('interval')->nullable();
            $table->boolean('repeat_enabled')->default(false);
            $table->boolean('repeat_only_after_halt')->default(true);
            $table->string('status');

            $table->text('segment_class')->nullable();

            $table
                ->foreignId('segment_id')
                ->nullable()
                ->constrained('mailcoach_segments')
                ->nullOnDelete();

            $table->string('segment_description')->default(0);

            $table->timestamp('run_at')->nullable();
            $table->timestamp('last_ran_at')->nullable();

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('automation_id')
                ->nullable()
                ->constrained('mailcoach_automations')
                ->cascadeOnDelete();

            $table
                ->foreignId('parent_id')
                ->nullable()
                ->constrained('mailcoach_automation_actions')
                ->cascadeOnDelete();

            $table->string('key')->nullable();
            $table->text('action')->nullable();
            $table->integer('order');
            $table->timestamps();
        });

        Schema::create('mailcoach_automation_triggers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('automation_id')
                ->nullable()
                ->constrained('mailcoach_automations')
                ->cascadeOnDelete();

            $table->text('trigger')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_automation_action_subscriber', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('action_id')->index();
            $table->unsignedBigInteger('subscriber_id')->index();
            $table->timestamp('run_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('halted_at')->nullable();
            $table->timestamp('job_dispatched_at')->nullable();
            $table->timestamps();

            $table
                ->foreign('action_id')
                ->references('id')->on('mailcoach_automation_actions')
                ->onDelete('cascade');

            $table
                ->foreign('subscriber_id')
                ->references('id')->on('mailcoach_subscribers')
                ->onDelete('cascade');
        });

        Schema::create('mailcoach_automation_mail_opens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table
                ->foreignId('automation_mail_id')
                ->nullable()
                ->constrained('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_mail_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table
                ->foreignId('automation_mail_id')
                ->constrained('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table->string('url', 2048);
            $table->integer('click_count')->default(0);
            $table->integer('unique_click_count')->default(0);
            $table->nullableTimestamps();
        });

        Schema::create('mailcoach_automation_mail_clicks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table->foreignId('automation_mail_link_id')
                ->constrained('mailcoach_automation_mail_links')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->nullable()
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_automation_mail_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->unsignedBigInteger('automation_mail_id');

            $table
                ->foreign('automation_mail_id', 'auto_unsub_automation_mail_id')
                ->references('id')->on('mailcoach_automation_mails')
                ->cascadeOnDelete();

            $table
                ->foreignId('subscriber_id')
                ->constrained('mailcoach_subscribers')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_transactional_mail_opens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('mailcoach_transactional_mail_clicks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table
                ->foreignId('send_id')
                ->constrained('mailcoach_sends')
                ->cascadeOnDelete();

            $table->longText('url');

            $table->timestamps();
        });

        Schema::create('mailcoach_transactional_mails', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->json('cc')->nullable();
            $table->string('label')->nullable();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('from')->nullable();
            $table->json('to')->nullable();
            $table->json('bcc')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->longText('body')->nullable();
            $table->longText('structured_html')->nullable();
            $table->string('type'); // html, blade, markdown
            $table->json('replacers')->nullable();
            $table->boolean('store_mail')->default(false);
            $table->text('test_using_mailable')->nullable();
            $table->timestamps();
        });

        Schema::create('mailcoach_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('welcome_valid_until')->nullable();
        });

        Schema::create('mailcoach_settings', function (Blueprint $table) {
            $table->string('key')->index();
            $table->longText('value')->nullable();
        });

        Schema::create('mailcoach_mailers', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->string('config_key_name')->index();
            $table->string('transport');
            $table->longText('configuration')->nullable();
            $table->boolean('default')->default(false);
            $table->boolean('ready_for_use')->default(false);
            $table->timestamps();
        });

        Schema::create('mailcoach_webhook_configurations', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->text('url');
            $table->string('secret');
            $table->boolean('use_for_all_lists')->default(true);
            $table->timestamps();
        });

        Schema::create('mailcoach_webhook_configuration_email_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_configuration_id');
            $table->unsignedBigInteger('email_list_id');
            $table->timestamps();

            $table
                ->foreign('webhook_configuration_id', 'wc_idx')
                ->references('id')->on('mailcoach_webhook_configurations')
                ->cascadeOnDelete();

            $table
                ->foreign('email_list_id', 'mel_idx')
                ->references('id')->on('mailcoach_email_lists')
                ->cascadeOnDelete();
        });
    }
};

<div class="grid grid-cols-3 gap-6 justify-start items-end max-w-xl">
    @if ($mail->open_count)
        <x-mailcoach::statistic :href="route('mailcoach.automations.mails.opens', $mail)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($mail->unique_open_count)" :label="__mc('Unique Opens')"/>
        <x-mailcoach::statistic :stat="number_format($mail->open_count)" :label="__mc('Opens')"/>
        <x-mailcoach::statistic :stat="$mail->open_rate / 100" :label="__mc('Open Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __mc('No opens tracked') }}</div>
        </div>
    @endif

    @if($mail->click_count)
        <x-mailcoach::statistic :href="route('mailcoach.automations.mails.clicks', $mail)" class="col-start-1"
                     numClass="text-4xl font-semibold" :stat="number_format($mail->unique_click_count)" :label="__mc('Unique Clicks')"/>
        <x-mailcoach::statistic :stat="number_format($mail->click_count)" :label="__mc('Clicks')"/>
        <x-mailcoach::statistic :stat="$mail->click_rate / 100" :label="__mc('Click Rate')" suffix="%"/>
    @else
        <div class="col-start-1 col-span-3">
            <div class="text-4xl font-semibold">–</div>
            <div class="text-sm">{{ __mc('No clicks tracked') }}</div>
        </div>
    @endif

    <x-mailcoach::statistic :href="route('mailcoach.automations.mails.unsubscribes', $mail)" numClass="text-4xl font-semibold"
                 :stat="number_format($mail->unsubscribe_count)" :label="__mc('Unsubscribes')"/>
    <x-mailcoach::statistic :stat="$mail->unsubscribe_rate / 100" :label="__mc('Unsubscribe Rate')" suffix="%"/>

    <x-mailcoach::statistic :href="route('mailcoach.automations.mails.outbox', $mail) . '?filter[type]=bounced'"
                 class="col-start-1" numClass="text-4xl font-semibold" :stat="number_format($mail->bounce_count)"
                 :label="__mc('Bounces')"/>
    <x-mailcoach::statistic :stat="$mail->bounce_rate / 100" :label="__mc('Bounce Rate')" suffix="%"/>
</div>

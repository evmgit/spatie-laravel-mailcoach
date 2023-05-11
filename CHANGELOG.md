# Changelog

All notable changes to `laravel-mailcoach` will be documented in this file

## 6.7.0 - 2022-12-12

### What's Changed

- Add support for PHP 8.2 by @Nielsvanpach in https://github.com/spatie/laravel-mailcoach/pull/1178

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.6.2...6.7.0

## 6.6.2 - 2022-12-09

- Fix double password forgot link on login screen

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.6.1...6.6.2

## 6.6.1 - 2022-12-08

- Fix an issue where the SendTransactionalEmail API endpoint would not call the correct policy method
- Don't mark sends as failed when it's a "Send test" mail
- Add the email list id when sending a campaign test
- Set Mailcoach defaults in all of Mailcoach's routes
- Fixes the default password reset flow that Mailcoach provides

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.6.0...6.6.1

## 6.6.0 - 2022-12-06

Add support for sending transactional mails using the new [spatie/mailcoach-mailer](https://github.com/spatie/mailcoach-mailer) and [spatie/laravel-mailcoach-mailer](https://spatie/laravel-mailcoach-mailer) packages.

## 6.5.0 - 2022-12-02

### What's new

- Bulk actions! The subscribers table now has selectable rows and bulk actions for deleting and unsubscribing

![image](https://user-images.githubusercontent.com/3626559/205249805-a12e0240-7469-44d0-bd1f-bea9d272cb46.png)

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.4.2...6.5.0

## 6.4.2 - 2022-11-30

- Use the correct config key for campaign mailer
- The transactional mail api endpoint can now accept attachments

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.4.1...6.4.2

## 6.4.1 - 2022-11-30

- Sending a test now tries to find a matching subscriber, or creates a temporary in-memory subscriber to handle replacements. It also uses the exact same SendMailAction now that the non-test mails will use.

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.4.0...6.4.1

## 6.4.0 - 2022-11-28

### What's Changed

- Spanish translation by @chargoy in https://github.com/spatie/laravel-mailcoach/pull/1159

### New Contributors

- @chargoy made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1159

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.3.0...6.4.0

## 6.3.0 - 2022-11-28

- Re-enable Unlayer as a template editor

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.5...6.3.0

## 6.2.5 - 2022-11-28

- Clean sendinblue-feedback webhooks
- Make sure campaigns use UTF-8 encoding
- Retain subject & from when sending a transactional mail through the API

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.4...6.2.5

## 6.2.4 - 2022-11-25

- Set the default guard to the Mailcoach guard in Mailcoach middleware + add it to persistent Livewire middleware

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.3...6.2.4

## 6.2.3 - 2022-11-23

- Use the configured guard when checking auth

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.2...6.2.3

## 6.2.2 - 2022-11-23

- Fix an issue with multiple root elements on the email list onboarding page
- Longer timeout for hiding a notification when the notification is an error

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.1...6.2.2

## 6.2.1 - 2022-11-22

- Use model from config in campaign content controller
- Add email list defaults as placeholder to the new "Sender" fields
- Add precedence bulk header to test mail
- Query & load time improvements with very large lists

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.2.0...6.2.1

## 6.2.0 - 2022-11-21

### What's new

- Add a way to change Campaign & Automation Mail sender fields from the UI

### What's fixed

- Fix an issue with the subscriber search query #1142
- Fix an issue where the reply to was being set twice
- Fix an issue when importing Mailchimp CSVs

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.1.2...6.2.0

## 6.1.2 - 2022-11-15

### What's Changed

- Include missing website_theme column in upgrade docs by @flashadvocate in https://github.com/spatie/laravel-mailcoach/pull/1127
- Fixing the incorrect URL to view the purchases on the spatie website by @abishekrsrikaanth in https://github.com/spatie/laravel-mailcoach/pull/1128
- Add missing __mc() translation function calls and german translation by @StefanSchuechl in https://github.com/spatie/laravel-mailcoach/pull/1132
- Adding a trailing slash to the input group by @abishekrsrikaanth in https://github.com/spatie/laravel-mailcoach/pull/1129
- Disable autosave when the model was saved somewhere else

### New Contributors

- @flashadvocate made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1127

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.1.1...6.1.2

## 6.1.1 - 2022-11-11

- Update migrations so they don't get published again when upgrading from a v5 project
- Improve the upgrading documentation

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.1.0...6.1.1

## 6.1.0 - 2022-11-10

### What's new

- Added API endpoints for retrieving sends

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.0.1...6.1.0

## 6.0.1 - 2022-11-10

- Only enable autosave on Sendable models

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/6.0.0...6.0.1

## 6.0.0 - 2022-11-09

### Mailcoach v6 is here!

Some notable features:

- Completely new design with improved UX
- Add & manage multiple mailers with different providers with automatic setup
- Show a website archive of your email list's campaigns
- A full-featured template system
- A new & improved Markdown editor
- Send outgoing webhooks
- Improved list insights & charts
- A new "Manage preferences" screen where subscribers can manage the (public) tags attached to them
- A command palette
- Automations can be configured to run more than once for a subscriber
- The ability to override, replace and extend every page of Mailcoach

Check out our [upgrade guide](https://mailcoach.app/docs/self-hosted/v6/upgrading) for more details.

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.11.2...6.0.0

## 5.11.2 - 2022-11-09

- Add the Sendgrid SMTP header when a transactional mail gets stored

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.11.1...5.11.2

## 5.11.1 - 2022-11-07

### What's Changed

- Fix command description by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1115
- Add send uuid to transactional mail message headers

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.11.0...5.11.1

## 5.11.0 - 2022-10-19

### What's new

- Added a `Precedence: Bulk` header to prevent out of office replies

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.7...5.11.0

## 5.10.7 - 2022-10-14

### What's Changed

- Update german translations by @StefanSchuechl in https://github.com/spatie/laravel-mailcoach/pull/1100
- Fix script for retry pending sends by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/1099
- Add maxexceptions https://github.com/spatie/laravel-mailcoach/commit/17daf8339949c068c554083363308f44aae4e0c4

### New Contributors

- @StefanSchuechl made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/1100

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.6...5.10.7

## 5.10.6 - 2022-10-10

- Fix edge case where a subscriber that unsubscribes during campaign sending would not remove the send

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.5...5.10.6

## 5.10.5 - 2022-10-10

- Fix an issue where the user agent wasn't available on the debug page

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.4...5.10.5

## 5.10.4 - 2022-10-04

### What's Changed

- Update SendCampaignMailJob.php - fix incorrect use of ?? by @pokmot in https://github.com/spatie/laravel-mailcoach/pull/1094
- Update CreateCampaignSendJob.php - fix so identifier is unique by @pokmot in https://github.com/spatie/laravel-mailcoach/pull/1093

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.3...5.10.4

## 5.10.3 - 2022-09-27

### What's Changed

- Fixes an exception that occurs while exporting by @staninova in https://github.com/spatie/laravel-mailcoach/pull/1077

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.2...5.10.3

## 5.10.2 - 2022-09-27

- make methods protected by default

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.1...5.10.2

## 5.10.1 - 2022-09-01

- Remove Mailcoach overriding the Livewire upload limit

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.10.0...5.10.1

## 5.10.0 - 2022-08-29

### What's Changed

- Improve security of exports by @freekmurze in https://github.com/spatie/laravel-mailcoach/pull/1058

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.9.0...5.10.0

## 5.9.0 - 2022-08-23

### What's Changed

- Add filesystem configuration section to debug page by @AlexVanderbist in https://github.com/spatie/laravel-mailcoach/pull/1055
- Change default filesystem disks to local by @AlexVanderbist in https://github.com/spatie/laravel-mailcoach/pull/1056

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.8.2...5.9.0

## 5.8.2 - 2022-08-15

- Don't throttle the campaign job when the campaign is cancelled
- Require 2.2.4 of rate-limited-job-middleware

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.8.1...5.8.2

## 5.8.1 - 2022-08-13

### What's Changed

- Only export user defined tags in subscriber export by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/1050
- Use a rate limiter on the send jobs as well to prevent them being processed too quickly

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.8.0...5.8.1

## 5.8.0 - 2022-08-07

- Adds a new `mailcoach.timezone` config option for when you want to change Mailcoach's display timezone but not your app's
- Fix a throttling issue when more than 1 queue worker was set up for the send-campaign queue
- Update to spatie/simple-excel v2 as v1 uses an abandoned dependency

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.7.2...5.8.0

## 5.7.2 - 2022-07-28

### What's Changed

- Fix an issue when scoping on extra attributes
- [Docs] Add vapor .env config details by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1017
- [Docs] Update version num in postmark by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1026

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.7.1...5.7.2

## 4.17.1 - 2022-07-21

- Fix a bug where child actions in automations were being recreated

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.17.0...4.17.1

## 5.7.1 - 2022-07-11

### What's Changed

- [Docs] Fix imports in docs for v5 by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1011
- Tag: Allow id or model in scoping EmailList by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/1013

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.7.0...5.7.1

## 5.7.0 - 2022-07-01

- Improve imports & exports to be able to work with s3 disks

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.6.0...5.7.0

## 5.6.0 - 2022-06-24

### What's Changed

- Import & export by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/1002

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.14...5.6.0

## 5.5.14 - 2022-06-24

### What's Changed

- Fix broken link by @LorenzoSapora in https://github.com/spatie/laravel-mailcoach/pull/998
- Fix an n+1 issue on the segment subscriber index

### New Contributors

- @LorenzoSapora made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/998

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.13...5.5.14

## 5.5.13 - 2022-06-15

### What's changed

- Fix a bug where bounces would calculate statistics too early and cause wrong statistics to be displayed

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.12...5.5.13

## 5.5.12 - 2022-06-15

### What's Changed

- Catch throwable resolving a segmentClass by @masterix21 in https://github.com/spatie/laravel-mailcoach/pull/994
- Fix an issue where statistics recalculating caused invalid data to be displayed

### New Contributors

- @masterix21 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/994

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.11...5.5.12

## 5.5.11 - 2022-06-02

### What's changed

- Fix replacers not replacing url encoded texts #987

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.10...5.5.11

## 5.5.10 - 2022-06-02

### What's Changed

- factories should use custom models when configured by @adamthehutt in https://github.com/spatie/laravel-mailcoach/pull/989

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.9...5.5.10

## 5.5.9 - 2022-05-30

### What's Changed

- Update name of set to be `newsletters` by @jplhomer in https://github.com/spatie/laravel-mailcoach/pull/985
- Make UUID not crash on unsupported databases by @potsky in https://github.com/spatie/laravel-mailcoach/pull/982
- Add "Halted" count to automation overview by @riasvdv

### New Contributors

- @jplhomer made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/985
- @potsky made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/982

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.8...5.5.9

## 5.5.8 - 2022-05-13

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.7...5.5.8

## 5.5.7 - 2022-05-12

## What's Changed

- Optimise removeTags() performance by @JackWH in https://github.com/spatie/laravel-mailcoach/pull/975

## New Contributors

- @JackWH made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/975

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.6...5.5.7

## 5.5.6 - 2022-05-11

## What's changed

- We now dispatch the first action for the subscriber right away when an automation is triggered

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.5...5.5.6

## 5.5.5 - 2022-04-21

- Improve campaign summary screen performance for large campaigns

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.4...5.5.5

## 5.5.4 - 2022-04-20

- Don't mark as sent until all sends are actually sent

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.3...5.5.4

## 5.5.3 - 2022-04-20

## What's Changed

- Update getting-a-license.md by @WouterBrouwers in https://github.com/spatie/laravel-mailcoach/pull/953
- Sort subscribers by updated_at or subscribed_at by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/954
- Fix translations using trans_choice by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/955

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.2...5.5.3

## 5.5.2 - 2022-04-16

## What's Changed

- Fix Redis payload for the CreateSendJob being too large
- Update audience.md by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/943
- Docs & links improvements by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/944

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.1...5.5.2

## 5.5.1 - 2022-04-12

- Fix summary progress based on created sends

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.5.0...5.5.1

## 5.5.0 - 2022-04-11

## What's Changed

- Fix typo in `CampaignLink` docs by @anselmh in https://github.com/spatie/laravel-mailcoach/pull/938
- Dispatch the send right after creating it by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/939

## New Contributors

- @anselmh made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/938

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.7...5.5.0

## 5.4.7 - 2022-04-08

- Move singletons to scoped for better Octane compatibility

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.6...5.4.7

## 5.4.6 - 2022-04-07

- Don't create sends if the campaign is cancelled

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.5...5.4.6

## 5.4.5 - 2022-04-05

## What's Changed

- Make filter fields configurable for transactional mails and campaigns by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/933

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.4...5.4.5

## 5.4.4 - 2022-04-05

## What's Changed

- Refactor creating of sends to jobs for faster processing by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/932

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.3...5.4.4

## 5.4.3 - 2022-04-02

- Fix #918  segment_id in API request
- Fix #916 filter not working with "+"

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.2...5.4.3

## 5.4.2 - 2022-04-01

- Automation actions now also verify that subscribers match the segment before going to the next step

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.1...5.4.2

## 5.4.1 - 2022-03-31

## What's Changed

- Update getting-a-license.md by @WouterBrouwers in https://github.com/spatie/laravel-mailcoach/pull/920
- Add missing translations by @cretueusebiu in https://github.com/spatie/laravel-mailcoach/pull/930

## New Contributors

- @WouterBrouwers made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/920

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.4.0...5.4.1

## 5.4.0 - 2022-03-21

- Rework how campaigns are sent for better reliability
- Show a schedule overview on the debug screen

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.5...5.4.0

## 5.3.5 - 2022-03-21

- Improve command that dispatches sending campaigns again if the job fails for some reason

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.4...5.3.5

## 5.3.4 - 2022-03-20

- Improve SendCampaignAction query to not include subscribers that already have a send

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.3...5.3.4

## 5.3.3 - 2022-03-18

## What's Changed

- Fix functions using Carbon type declaration instead of CarbonInterface by @zupolgec in https://github.com/spatie/laravel-mailcoach/pull/911
- Hide the create button if the user has no right to create by @nessor in https://github.com/spatie/laravel-mailcoach/pull/910

## New Contributors

- @zupolgec made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/911
- @nessor made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/910

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.2...5.3.3

## 5.3.2 - 2022-03-14

- Fix https://github.com/spatie/laravel-mailcoach/issues/908 - Allow eager loading of tags

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.1...5.3.2

## 5.3.1 - 2022-03-10

- Fix the webhook trigger throwing an exception when you have globally namespaced controllers - #907

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.3.0...5.3.1

## 5.3.0 - 2022-03-09

## What's Changed

- Add TransactionalMailStored Event to the Docs by @LooxisDev in https://github.com/spatie/laravel-mailcoach/pull/893
- Update upgrading.md by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/895
- Define new configs to allow default opens/clicks tracking and utm_tag… by @electronick86 in https://github.com/spatie/laravel-mailcoach/pull/900
- Search for uuid first - fix #896
- Don't automatically confirm unsubscribe if send is too recent - fix #905
- Turbo doesn't handle downloads well - fix #903

## New Contributors

- @LooxisDev made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/893
- @electronick86 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/900

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.2.1...5.3.0

## 5.2.1 - 2022-02-21

## What's Changed

- Remove stray ray call
- Fix broken link by @timothyasp in https://github.com/spatie/laravel-mailcoach/pull/889
- Update subscribers.md by @shopbox-support in https://github.com/spatie/laravel-mailcoach/pull/890

## New Contributors

- @timothyasp made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/889
- @shopbox-support made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/890

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.2.0...5.2.1

## 5.2.0 - 2022-02-15

- Use hotwired/turbo instead of turbolinks

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.1.0...5.2.0

## 5.1.0 - 2022-02-14

## What's new

- Allow campaigns to be filtered on email_list_id

## What's Changed

- Upgrade guide showing wrong table name by @bashgeek in https://github.com/spatie/laravel-mailcoach/pull/886

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.4...5.1.0

## 4.17.0 - 2022-02-14

- Allow campaigns to be filtered on email_list_id

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.2...4.17.0

## 5.0.4 - 2022-02-11

- Fixes some small translation issue

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.3...5.0.4

## 5.0.3 - 2022-02-11

- Minify css & js

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.2...5.0.3

## 5.0.2 - 2022-02-11

## What's Changed

- No longer publish the job_batches migration by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/884
- Update NL translations for V5 by @mbardelmeijer in https://github.com/spatie/laravel-mailcoach/pull/883

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.1...5.0.2

## 5.0.1 - 2022-02-11

## What's Changed

- fix typo in docs by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/880
- Remove ray call by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/882
- docs: small improvements by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/881

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/5.0.0...5.0.1

## 5.0.0 - 2022-02-10

## What's Changed

- Add support for Laravel 9
- Rework throttling of campaign & automation sends for performance
- Performance improvements
- Drop support for Laravel 8 & below

> **Read the [upgrade guide](https://mailcoach.app/docs/v5/mailcoach/upgrading)!**

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.2...5.0.0

## 4.16.2 - 2022-02-10

- Dispatch calculate statistics when there's a new open or click as well

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.1...4.16.2

## 4.16.1 - 2022-02-08

- Don't dispatch a recalculate statistics job when there are no new sends

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.16.0...4.16.1

## 4.16.0 - 2022-02-07

## What's Changed

- Lazy load segments relation by @jpeters8889 in https://github.com/spatie/laravel-mailcoach/pull/874
- Fix subscriber request not resolving a Subscriber in every case #804
- Fix schedule_at not being set on Campaigns through the API #860
- Add uuid to searchable fields #871

## New Contributors

- @jpeters8889 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/874

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.7...4.16.0

## 4.15.7 - 2022-02-03

- Allow for league/html-to-markdown ^5.0

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.6...4.15.7

## 4.15.6 - 2022-02-03

## What's Changed

- fix typo in campaign casts by @StevePorter92 in https://github.com/spatie/laravel-mailcoach/pull/865
- Wrong version removed [docs] by @mertasan in https://github.com/spatie/laravel-mailcoach/pull/867
- Use configuration for action subscriber model by @anyb1s in https://github.com/spatie/laravel-mailcoach/pull/869

## New Contributors

- @StevePorter92 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/865
- @mertasan made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/867
- @anyb1s made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/869

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.5...4.15.6

## 4.15.5 - 2022-01-07

## What's Changed

- Add Turkish Translations by @sineld in https://github.com/spatie/laravel-mailcoach/pull/831

## New Contributors

- @sineld made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/831

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.4...4.15.5

## 4.15.4 - 2022-01-03

## What's Changed

- Fix broken link in postmark.md by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/810
- Update Postmark coupon by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/812
- update documentation by @caneco in https://github.com/spatie/laravel-mailcoach/pull/813
- Add localization function to two strings by @hexation-srl in https://github.com/spatie/laravel-mailcoach/pull/821
- Update nl.json by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/814
- Fixes #793 by @ryanito in https://github.com/spatie/laravel-mailcoach/pull/807
- Fix nested automation actions by @LukeAbell in https://github.com/spatie/laravel-mailcoach/pull/816

## New Contributors

- @caneco made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/813
- @ryanito made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/807

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.3...4.15.4

## 4.15.3 - 2021-12-04

## What's Changed

- Fix schedule command list by @Fabstilelook in https://github.com/spatie/laravel-mailcoach/pull/798
- Automation performance warnings by @riasvdv in https://github.com/spatie/laravel-mailcoach/pull/806

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.2...4.15.3

## 4.15.2 - 2021-12-01

## What's Changed

- Support for using factories in 3rd party apps by @lukeraymonddowning in https://github.com/spatie/laravel-mailcoach/pull/794
- fix: remove useless space in filename by @zhanang19 in https://github.com/spatie/laravel-mailcoach/pull/789

## New Contributors

- @lukeraymonddowning made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/794
- @zhanang19 made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/789

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.1...4.15.2

## 4.15.1 - 2021-11-16

## What's Changed

- Add method to code example by @willemvb in https://github.com/spatie/laravel-mailcoach/pull/788

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.15.0...4.15.1

## 4.15.0 - 2021-11-16

## What's Changed

- Remove double Redis requirement in Vapor section by @dennisvandalen in https://github.com/spatie/laravel-mailcoach/pull/764
- Add missing campaign show controller method

## New Contributors

- @dennisvandalen made their first contribution in https://github.com/spatie/laravel-mailcoach/pull/764

**Full Changelog**: https://github.com/spatie/laravel-mailcoach/compare/4.14.3...4.15.0

## 4.14.3 - 2021-10-11

- fix syntax error (#753)

## 4.14.2 - 2021-10-11

- improve compatibility with Vapor

## 4.14.1 - 2021-10-11

- improve compatibility with Vapor

## 4.14.0 - 2021-10-11

## What's new

- Add trigger model to config (#750)

## 4.13.0 - 2021-10-09

## What's new

- Add Campaign & Automation Mail Link/Click/Open/Unsubscribe to config

## 4.12.0 - 2021-09-30

## What's new

- Added a config option to switch the rate limit driver to either `redis` (default) or `cache`

## 4.11.0 - 2021-09-30

## What's fixed

- Fix the ConditionAction breaking on different locales (#715)
- Fix the subscribed landing view not being accessible without being logged in
- The subject of transactional mail templates will now run replacers (#636)

## What's new

- Use protected props for import subscribers action (#741)
- Add an option to append tags when updating subscribers (#739)

## 4.10.2 - 2021-09-24

- Automation mail from & reply to were being set unexpectedly (#713)
- Fix an incorrect table status message (#692)

## 4.10.1 - 2021-09-20

- Fix an issue with the WaitAction when loading old data.

## 4.10.0 - 2021-09-15

- Automations now continue when they weren't halted for a subscriber and new actions get added.

## 4.9.6 - 2021-09-13

- The `TagRemovedEvent` wasn't being triggered when deleting a tag or syncing tags on a subscriber

## 4.9.5 - 2021-09-06

- bug fix: add missing return type in `registerDeprecatedApiGuard()`

## 4.9.4 - 2021-09-03

- fix actions with seconds (#696)

## 4.9.3 - 2021-09-03

- fix API errors (#707)

## 4.9.2 - 2021-09-03

- Fix email list summary stats

## 4.9.1 - 2021-08-25

- Fix email list summary stats (#691)

## 4.9.0 - 2021-08-23

- Actions can now also receive the `ActionSubscriber` model as a second parameter in their `run` method. The signature of the method has to be `run(Subscriber $subscriber, ?ActionSubscriber $actionSubscriber = null)`.

## 4.8.6 - 2021-08-18

- Fix first_name and last_name in form subscription (#680)

## 4.8.5 - 2021-08-16

- Fix an issue with pagination on queries when `created_at` has the same value. (#671)
- Fix the WaitAction throwing errors with Carbon localization (#657)

## 4.8.4 - 2021-08-06

- Fix an issue with the template edit view

## 4.8.3 - 2021-08-06

- Fix $campaign being referenced instead of $template

## 4.8.2 - 2021-08-06

- Add missing modals to template edit screen

## 4.8.1 - 2021-08-04

- Fix `Action` model not being able to be replaced

## 4.8.0 - 2021-08-04

- Add an `attachSubscriber` to the `Action` model which allows overriding how Subscribers move to the next action

## 4.7.1 - 2021-08-04

- Use the correct table name for ActionSubscriber

## 4.7.0 - 2021-08-03

- Allow multiple automation runs by overwriting an action (#666)

## 4.6.2 - 2021-08-03

- Fix double ampersand encoding (#665)

## 4.6.1 - 2021-07-29

- Remove a stray dd()
- Fix an issue on the automation views when using a custom segment

## 4.6.0 - 2021-07-22

- Allow filter by `email` on the subscribers API endpoint
- Fixed the unsubscribeUrl replacer replacing more than intended (#644)
- Fix segmentSubscriberCount executing too many queries in the same view (#648)

## 4.5.3 - 2021-07-15

- Add missing resolveRouteBinding methods to overridable models (#652)
- Fix various models not pulling from config (#653)

## 4.5.2 - 2021-07-14

- fix incorrect model references (#650)

## 4.5.1 - 2021-06-21

- Dutch translation fix (#643)
- Fix allowed sorts of automation emails (#637)

## 4.5.0 - 2021-06-21

- Add a view for a subscriber's custom attributes (#621)

## 4.4.6 - 2021-06-17

- Remove redundant hasTable check (#620)
- Re-add webview URL to summary page (#613)
- Removed space after translatable string + typo's (#610)
- Call toArray() instead of casting to array (#607)
- Update NL translations (#606)
- Add some missing props to the dateField blade component (#596)
- Fetch models for match from config (#595)
- Fix duplicate transactional template (#593)
- Fix incorrect model references (#591)
- Cache Event based triggers so they don't need to be queried each request

## 4.4.5 - 2021-06-14

- allow schemaless-attributes v2

## 4.4.4 - 2021-06-11

- Fix an issue where automations would keep running for unsubscribed subscribers

## 4.4.3 - 2021-05-31

- upgrade spatie/feed

## 4.4.2 - 2021-05-25

- Automation mails will now use the list from & reply to fields

## 4.4.1 - 2021-05-14

- Fix an issue with empty html throwing an error on the send page
- Fix mails rendering html

## 4.4.0 - 2021-05-12

- add `CheckLicense` command

## 4.3.4 - 2021-05-04

- fix link to docs

## 4.3.3 - 2021-04-27

## 4.3.2 - 2021-04-27

- Fix assets

## 4.3.1 - 2021-04-23

- Fixed an issue when deleting an automationMail that's inside an automation

## 4.3.0 - 2021-04-23

- Only add a `USE INDEX` statement when using MySQL
- Fix FontAwesome Free icons
- Fix an issue on the subscriber received mails view
- Added an extra Tag added trigger when subscribers are confirmed
- Optimized some queries

## 4.2.1 - 2021-04-09

- Fix a MySQL query issue with order by on the email list summary
- Adds missing table prefixes for raw queries #520

## 4.2.0 - 2021-04-07

- save extra attributes via api (#508)
- Fix LinkHasher bug when using custom Campaign model (#505)
- Use v2 of spatie/temporary-directory (#499)
- Make sure subscriber summary on email list is sorted (#516)

## 4.1.0 - 2021-04-06

- add `TransactionalMailStored` event (#507)

## 4.0.13 - 2021-03-29

- fix more mail rendering issues

## 4.0.12 - 2021-03-29

- Fix an issue where html tags were being shown in emails

## 4.0.11 - 2021-03-26

- Fix UTM tags throwing an exception on mailto, tel or other non-url urls

## 4.0.10 - 2021-03-25

- Remove some stray Ray calls

## 4.0.9 - 2021-03-24

- improved config file

## 4.0.8 - 2021-03-24

- Fix links on the automation mail statistics screen

## 4.0.7 - 2021-03-24

- Fix subscribers being added twice to the next action

## 4.0.6 - 2021-03-24

- Fix incorrect link hashes being added as tags

## 4.0.5 - 2021-03-24

- Fix stripping UTM tags from urls without query parameters

## 4.0.4 - 2021-03-24

- Fix an issue with automation counts being incorrect

## 4.0.3 - 2021-03-24

- Fix UTM tags & subscriber tags handling

## 4.0.2 - 2021-03-24

- Fix duplicating automations

## 4.0.1 - 2021-03-24

- display tags that get created for links

## 4.0.0 - 2021-03-24

- added automations
- added transactional mail log
- added transactional mail templates
- refine campaign sending
- revamp of UI
- refactor to domain oriented structure
- internal cleanup
- rewritten docs
- drop support for PHP 7

## 3.10.4 - 2021-02-19

- HTML errors should not prevent loading of links in HTML

## 3.10.3 - 2021-01-26

- Fix stray closing tag

## 3.10.2 - 2021-01-26

- Fix an issue on the campaign details when a subscriber was deleted

## 3.10.1 - 2021-01-18

- Fix welcome mail to use latest subscriber details #426

## 3.10.0 - 2021-01-15

- Allow filling the subject in the UpdateCampaignAction
- Fix an issue on the campaign details when a list was deleted

## 3.9.8 - 2021-01-08

- Fix php constraint

## 3.9.7 - 2021-01-06

- fixed an issue with large segments

## 3.9.6 - 2020-12-28

- fix for empty campaign

## 3.9.5 - 2020-12-17

- Refactor import subscribers action (#395)

## 3.9.4 - 2020-12-16

- Fix an issue with HTML loading in the delivery tab (#384)

## 3.9.3 - 2020-12-16

- trim values from import source (#392)

## 3.9.2 - 2020-12-15

- use laravel-mailcoach for support

## 3.9.1 - 2020-12-15

- improvement for large exports

## 3.9.0 - 2020-12-14

- add `Send` model to config file

## 3.8.1 - 2020-12-10

- Fix a display issue with timezones

## 3.8.0 - 2020-12-10

- Test emails are now prefixed with "[Test]"
- Test emails have a X-Entity-Ref-ID header to prevent threading
- The delivery screen now shows a warning if your message is above 102kb, which could cause clipping in Gmail
- The delivery screen now shows the links found in your campaign, so you can verify they are correct.

## 3.7.0 - 2020-12-09

- add update method on SubscribersController.php (#383)

## 3.6.5 - 2020-12-09

- Debug database version now works on postgres
- Add subject of campaign to preview modal

## 3.6.4 - 2020-12-08

- Make sure the email list graph is scoped by email list

## 3.6.3 - 2020-12-07

- Fix issue with index in multi-tenant setup

## 3.6.2 - 2020-12-07

- ensure index exists when using MySQL

## 3.6.1 - 2020-12-07

- improve number formatting

## 3.6.0 - 2020-12-07

- add list level metrics

## 3.5.0 - 2020-11-30

- add partial for tags (#375)

## 3.4.0 - 2020-11-30

- add support for PHP 8.0

## 3.3.0 - 2020-11-19

- `extra_attributes` & `tags` can now be passed to the create subscribers API endpoint.
- `tags` are now included in Subscriber responses from the API
- Campaign graph now starts from first open if there are opens while the campaign is still sending

## 3.2.13 - 2020-11-17

- fix mails being sent on default queue for campaign batch (#368)

## 3.2.12 - 2020-11-16

- fix variable name in NL translations (#367)

## 3.2.11 - 2020-11-12

- allow medialibrary v9

## 3.2.10 - 2020-11-11

- use `,` as a delimiter for `allowed_form_extra_attributes`

## 3.2.9 - 2020-10-29

- use a custom error message when sending a campaign test email

## 3.2.8 - 2020-10-28

- fix for #358

## 3.2.7 - 2020-10-28

- remove duplicate error message

## 3.2.6 - 2020-10-21

- save attributes on list (#356)

## 3.2.5 - 2020-10-21

- disable autocomplete on search inputs (#353)

## 3.2.4 - 2020-10-19

- fix open and click rates on campaign summery mail (#343)

## 3.2.3 - 2020-10-13

- fix subscription confirmation mail copy

## 3.2.2 - 2020-10-10

- translate the sent settings screen (#338)

## 3.2.1 - 2020-10-05

- fix: only send a welcome mail if the user wasn't already subscribed

## 3.2.0 - 2020-10-05

- add Dutch translations

## 3.1.3 - 2020-10-05

- fix some timezone issues
- fix <html> being added on the campaign html

## 3.1.2 - 2020-10-04

- format other numbers on campaign index view

## 3.1.1 - 2020-10-04

- format send count on campaign index screen

## 3.1.0 - 2020-10-04

- add German translations

## 3.0.6 - 2020-09-30

- improve debug page

## 3.0.5 - 2020-09-30

- pass send to unsubscribe and complaint methods

## 3.0.4 - 2020-09-29

- margin tweak on reply-to

## 3.0.3 - 2020-09-29

- improve campaign index spacing & styles

## 3.0.2 - 2020-09-29

- show segment in campaign overview

## 3.0.1 - 2020-09-29

- don't limit exception message on failed sends

## 3.0.0 - 2020-09-27

- add API
- add used configuration screen
- add reply-to email and name
- add debug page
- improved subscriber import
- use Laravel 8 queued job batches
- use class based factories
- stability improvements
- drop support for Laravel 7

## 2.23.17 - 2020-09-30

- pass send to unsubscribe and complaint methods

## 2.23.16 - 2020-09-29

- don't limit exception message on failed sends

> > > > > > > v2

## 2.23.15 - 2020-09-27

- add `$tries = 1` to `SendCampaignJob`
- add index on `campaign_id, subscriber_id` on the sends table

(see #284)

## 2.23.14 - 2020-09-24

- make sure no unserialize notices are thrown

## 2.23.13 - 2020-09-17

- detach tags when deleting subscriber

## 2.23.12 - 2020-09-08

- fix support for Laravel 8

## 2.23.11 - 2020-09-08

- add support for Laravel 8

## 2.23.10 - 2020-09-06

- allow Guzzle 7

## 2.23.9 - 2020-09-04

- fix an issue where the original doctype was not being kept

## 2.23.8 - 2020-08-28

- fix custom mailable campaign not set

## 2.23.7 - 2020-08-28

- fix extra subscriber attributes that couldn't be null

## 2.23.6 - 2020-08-25

- fix scheduling options

## 2.23.5 - 2020-08-12

- fix Carbon macro to also work on CarbonImmutable

## 2.23.4 - 2020-08-11

- fix Carbon typehints to CarbonInterface

## 2.23.3 - 2020-08-06

- require report recipients if reports are to be sent (#278)

## 2.23.2 - 2020-07-30

- fix a bug with Custom Mailables with subjects that did not send

## 2.23.1 - 2020-07-28

- fix bug that prevents replacement strings from working with a custom mailable (#274)

## 2.23.0 - 2020-07-22

- use POST request to process unsubscribe (#273)

## 2.22.0 - 2020-07-22

- use model cleanup v3

## 2.21.1 - 2020-07-10

- Testing fixes for Custom Mailables

## 2.21.0 - 2020-07-10

- When subscribing an existing subscriber, tags will still be updated.

## 2.20.1 - 2020-07-01

- Fix another regression in the campaign summary

## 2.20.0 - 2020-07-01

- Add a `retry_until_hours` setting to the throttling config

## 2.19.4 - 2020-07-01

- Fix a regression where the campaign summary was not showing the correct messaging

## 2.19.3 - 2020-07-01

- Show a label in the footer when environment isn't `production` or debugging is on

## 2.19.2 - 2020-06-28

- make sure very long error messages from SMTP get processed (#268)

## 2.19.1 - 2020-06-25

- fix getting subscribers by tag (#264)

## 2.19.0 - 2020-06-25

- add config setting to register (or not) blade components (#266)

## 2.18.0 - 2020-06-25

- add German translation (#267)

## 2.17.0 - 2020-06-21

- add support for private filesystems for the import subscribers disk (#263)

## 2.16.0 - 2020-06-21

- add dutch translations (#260)

## 2.15.7 - 2020-06-18

- fix icon alignment in dropdowns (#262)

## 2.15.6 - 2020-06-18

- prevent Stripping of Email Body Element (#261)

## 2.15.5 - 2020-06-17

- translation fixes (#256)

## 2.15.4 - 2020-06-16

- fix theme

## 2.15.2 - 2020-06-14

- fix campaign sending progress bar (#251)

## 2.15.1 - 2020-06-10

- fix typo in one of the translations

## 2.15.0 - 2020-06-10

- add translations (#247)

## 2.14.2 - 2020-06-08

- fix relationship definitions to support custom models (#245)

## 2.14.1 - 2020-06-04

- use custom models in route-model binding (#244)

## 2.14.0 - 2020-06-02

- add support for custom/configurable models (#241)

## 2.13.0 - 2020-05-27

- choose the email list when creating a campaign
- fix error when viewing a campaign with a deleted segment

## 2.12.0 - 2020-05-23

- add support for defining the welcome mail job queue (#238)

## 2.11.8 - 2020-05-14

- Allow both `Carbon` and `CarbonImmutable` to be used (#235)

## 2.11.7 - 2020-05-14

- fix display of campaign summary when the list of the campaign has been deleted

## 2.11.6 - 2020-05-14

- fix for sql_mode=only_full_group_by issue
- fix campaign not being marked as sent with a custom segment

## 2.11.5 - 2020-05-07

- fix for `PrepareEmailHtmlAction` breaking html

## 2.11.4 - 2020-05-06

- fix display of custom segment classes

## 2.11.3 - 2020-05-04

- wrong route in subscribers (#231)

## 2.11.2 - 2020-04-30

- use default action is action class not set in config

## 2.11.1 - 2020-04-30

- fix all filters being active all at once on list pages

## 2.11.0 - 2020-04-30

- add `CampaignReplacer` (#226)

## 2.10.1 - 2020-04-30

- Fix Error htmlspecialchars() in delivery tab
- Fix custom segment display

## 2.10.0 - 2020-04-30

- refactor to Tailwind grid (#228)

## 2.9.1 - 2020-04-29

- fix subjects not getting replaced correctly

## 2.9.0 - 2020-04-27

- add `WebhookCallProcessedEvent` for cleaning up old webhook calls

## 2.8.0 - 2020-04-24

- make models extendible

## 2.7.4 - 2020-04-24

- allow chronos v2

## 2.7.2 - 2020-04-18

- remove links in import confirmation mail

## 2.7.1 - 2020-04-09

- make campaign on mailable nullable (#147)

## 2.7.0 - 2020-04-09

- accept time in register feedback functions

## 2.6.4 - 2020-04-08

- fix custom mailable content

## 2.6.3 - 2020-04-07

- fix broken horses image on confirmation dialog

## 2.6.2 - 2020-04-06

- fix for sending campaigns using custom mailables

## 2.6.1 - 2020-04-06

- format number of mails on confirmation dialog

## 2.6.1 - 2020-04-06

- add view `mailcoach::app.emailLists.layouts.partials.afterLastTab`

## 2.6.1 - 2020-04-06

- add ability to use replacers in the subject of a campaign

## 2.6.1 - 2020-04-06

- fix sorting on email on the outbox screen

## 2.4.6 - 2020-04-03

- fix malformed ampersands when sending

## 2.4.5 - 2020-04-03

- fix malformed ampersands in HTML validation

## 2.4.4 - 2020-04-02

- fix sorting tags by subscriber_count

## 2.4.3 - 2020-04-02

- send campaign sent confirmation only after all mails have been sent

## 2.4.2 - 2020-04-02

- fix invalid route action

## 2.4.1 - 2020-04-01

- improve modal texts

## 2.4.0 - 2020-03-30

- add duplicate segment action

## 2.3.0 - 2020-03-30

- add duplicate template action

## 2.2.2 - 2020-03-30

- improve config file comments

## 2.2.1 - 2020-03-25

- fix js asset url

## 2.2.0 - 2020-03-25

- add `import_subscribers_disk` config option

## 2.1.3 - 2020-03-25

- version assets in blade views

## 2.1.2 - 2020-03-25

- fix icons

## 2.1.1 - 2020-03-23

- fix `ConfirmSubscriberController` not defined when using route caching

## 2.1.0 - 2020-03-22

- add `queue_connection` config option
- add `perform_on_queue.import_subscribers_job` config option

## 2.0.4 - 2020-03-20

- fix error with groupBy in `CampaignOpensQuery`

## 2.0.3 - 2020-03-13

- fix `CreateSubscriberRequest`

## 2.0.2 - 2020-03-11

- Make sure referrer is always set

## 2.0.1 - 2020-03-11

- use `booted` functions instead of `boot` in models
- fix bug where the campaign settings screen did not work when using a custom segment class

## 2.0.0 - 2020-03-10

- add support for Laravel 7
- 
- add support for custom editors
- 
- add ability to use multiple mail configurations
- 
- add ability to send confirmation and welcome mails with a separate mail configuration
- 
- add option to delay welcome mail
- 
- drop support for Laravel 6
- 

## 1.8.0 - 2020-02-27

- add support for Postmark

## 1.7.2 - 2020-02-21

- fix the default from name for campaigns

## 1.7.1 - 2020-02-17

- add support for instantiated segments

## 1.7.0 - 2020-02-17

- add support for instantiated segments. EDIT: due to a merging error, this functionality was not added.

## 1.6.13 - 2020-02-17

- add unique tag on `email_list_id` and `email` in the `mailcoach_subscribers` table

## 1.6.12 - 2020-02-16

- change the mail content fields to have the `text` type in the db

## 1.6.11 - 2020-02-12

- fix encoding of plain text part of sent mails

## 1.6.10 - 2020-02-11

- The `ConvertHtmlToTextAction` will now suppress errors and warnings and try to deliver plain text at all times
- Added the plain text version to the `SendTestMailAction`

## 1.6.9 - 2020-02-10

- `UpdateSubscriberRequest` will now handle lists that have a common email properly

## 1.6.8 - 2020-02-10

- fix `subscribers` view

## 1.6.7 - 2020-02-09

- prevent hidden search field when there are no search results

## 1.6.6 - 2020-02-09

- fix caching latest version

## 1.6.5 - 2020-02-09

- show exception message when html rule fails

## 1.6.4 - 2020-02-08

- fix `SendTypeFilter` file name

## 1.6.3 - 2020-02-07

- make the `url` field of the `mailcoach_campaign_links` table bigger

## 1.6.2 - 2020-02-07

- make latest version checking more robust

## 1.6.1 - 2020-02-06

- change `failure_reason` type from string to text

## 1.6.0 - 2020-02-05

- Add an X-MAILCOACH header to messages sent by Mailcoach

## 1.5.1 - 2020-02-05

- make sure the Mailcoach service provider publishes the medialibrary migration

## 1.5.0 - 2020-02-04

- add `endHead` partial

## 1.4.3 - 2020-02-03

- lower required version for package-versions to ^1.2

## 1.4.2 - 2020-02-03

- fix exception when trying to replace an attribute that is null

## 1.4.1 - 2020-02-03

- make events properties public

## 1.4.0 - 2020-02-02

- add `BounceRegisteredEvent` and `ComplaintRegisteredEvent`

## 1.3.1 - 2020-02-02

- fix `CampaignSend` query class names

## 1.3.0 - 2020-02-01

- add `middleware` config key

## 1.2.3 - 2020-02-01

- fix closing of `strong` tag in numerous views

## 1.2.2 - 2020-01-31

- send mails using default email on email list

## 1.2.1 - 2020-01-31

- fix bug in `ConfirmSubscriberController` (#16)

## 1.2.0 - 2020-01-31

- add `guard` config option

## 1.1.1 - 2020-01-29

- fix FOUC bug in Firefox

## 1.1.0 - 2020-01-29

- move factories to src, so tests of feedback packages can use them

## 1.0.0 - 2020-01-29

- initial release

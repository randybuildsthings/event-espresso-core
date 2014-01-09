<h3><?php _e('Transactions Table Column Headings', 'event_espresso'); ?></h3>
<ul>
<li>
<p><strong><?php _e('ID', 'event_espresso'); ?></strong><br />
<?php _e('This is the transaction ID is that is used throughout the payment process.', 'event_espresso'); ?>
</p>
</li>
<li><strong><?php _e('Transaction Date', 'event_espresso'); ?></strong><br />
<?php _e('This is the date that the transaction occurred on. Clicking the date will take you to another page where you can view the transaction details.', 'event_espresso'); ?>
</li>
<li><strong><?php _e('Status', 'event_espresso'); ?></strong> <br />
<?php _e('The status helps you understand if the transaction was successful or not. Below are available statuses.', 'event_espresso'); ?>
<ol>
<li>
<?php _e('Complete: the payment was successful.', 'event_espresso'); ?>
</li>
<li>
<?php _e('Open: the payment has not yet been attempted. This is the status for incomplete offline payments such as invoices.', 'event_espresso'); ?>
</li>
<li>
<?php _e('Incomplete: the payment has not yet been completed. This is the status for online payments that have yet to be processed.', 'event_espresso'); ?>
</li>
</ol>
</li>
<li><strong><?php _e('Total', 'event_espresso'); ?></strong><br />
<?php _e('This is the total amount for that transaction. It will include the total of every ticket purchased even if from separate events.', 'event_espresso'); ?>
</li>
<li><strong><?php _e('Paid', 'event_espresso'); ?></strong><br />
<?php _e('This shows much has been paid. If this column matches the amount in the total column, then the transaction has been paid in full.', 'event_espresso'); ?>
</li>
<li><strong><?php _e('Primary Registrant', 'event_espresso'); ?></strong><br />
<?php _e('The name of the primary registrant. Clicking the name will take you to the registration details page.', 'event_espresso'); ?>
</li>
<li><strong><?php _e('Email Address', 'event_espresso'); ?></strong><br />
<?php _e('This is the email address for the primary registrant. Clicking the email address will open your default email client.', 'event_espresso'); ?>
</li>
<li><strong><?php _e('Event', 'event_espresso'); ?></strong><br />
<?php _e('The name of the events are shown here. Clicking the name will take you the edit event page.', 'event_espresso'); ?>
</li>
<li><strong><?php _e('Actions', 'event_espresso'); ?></strong><br />
<?php _e('There are several actions that can be done by clicking the icons:', 'event_espresso'); ?>
<ol>
<li>
<?php _e('View Transaction Details <span class="dashicons dashicons-search"></span>: Takes you to the individual transaction page. Clicking the date also takes you to the individual transaction page.', 'event_espresso'); ?>
</li>
<li>
<?php _e('Download Invoice for Transaction <span class="ee-icon ee-icon-PDF-file-type"></span>: downloads the invoice PDF.', 'event_espresso'); ?>
</li>
<li><?php echo sprintf(__('Send Payment Reminder <span class="ee-icon ee-icon-payment-reminder"></span>: Emails the primary registrant the Payment Reminder message. This is set up in the %sMessages page%s.', 'event_espresso'),'<a href="admin.php?page=espresso_messages">','</a>'); ?> </li>
<li><?php echo sprintf(__('View Registration Details <span class="ee-icon ee-icon-user-edit"></span>: Clicking this icon will take you to the registration page for this transaction. You can also get there via the %sRegistrations page%s.', 'event_espresso'), '<a href="admin.php?page=espresso_registrations">','</a>'); ?> </li>
</ol>
</li>
</ul>
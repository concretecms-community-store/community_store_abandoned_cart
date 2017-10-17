<?php
/*
 * Abandoned cart settings page
 * Created by Jos De Berdt
 * github @Jozzeh
 */
?>

<form method="post" action="<?= $this->action('save'); ?>">
  <div class="row">
    <div class="col-sm-12">
      <label>
          <input type="checkbox" id="enabled" name="enabled" value="1" <?php if($enabled == 1){
            echo 'checked="checked"';
          } ?> />
          <?= t("Enabled"); ?>
      </label>
      <hr/>
    </div>
  </div>

  <div class="row">
      <div class="col-md-6">
          <div class="form-group">
              <label for="reminder_days"><?= t('Send reminder after X days') ?></label>
              <?= $form->number('reminder_days', $reminder_days, array('required' => 'required', 'placeholder'=>t('Number'))); ?>
          </div>
      </div>
      <div class="col-md-6">
          <div class="form-group">
              <label for="send_to"><?= t('Send reminder to') ?></label>
              <?= $form->select('send_to', array('0' => 'Everyone', '1' => 'Registered users'), $send_to); ?>
          </div>
      </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <h4>From...</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <div class="form-group">
          <label for="from_mail"><?= t('From e-mail') ?></label>
          <?= $form->email('from_mail', $from_mail, array('required' => 'required', 'placeholder'=>t('From: yyyy@zzz.com'))); ?>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
          <label for="from_name"><?= t('From name') ?></label>
          <?= $form->text('from_name', $from_name, array('placeholder'=>t('From: Your store'))); ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <h4>Subject & Content</h4>
      <p class="small">
        <?= t('Use of replacement tags is allowed in subject and content. See below which tags are allowed.'); ?>
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <div class="form-group">
          <label for="mail_subject"><?= t('Subject') ?></label>
          <?= $form->text('mail_subject', $mail_subject, array('placeholder'=>t('Subject'))); ?>
      </div>
      <div class="tags">
        <p class="small" style="color: #777;">
          <?= t('Allowed tags'); ?> :<br/>
          %billing_first_name%<br/>
          %billing_last_name%<br/>
          %email%<br/>
          %cart_start%<br/>
          %cart_end%<br/>
        </p>
        <p class="small" style="color: #777;">
          <?= t('Tags %cart_start% and %cart_end% are mandatory.'); ?>
          <br/>
          <?= t('These tags will create a link to recover the cart. (Standard a-href link)'); ?>
          <br/>
          <?= t('Below you can set the (css) style of the recover link.'); ?>
        </p>
      </div>
      <div class="form-group">
          <label for="link_style"><?= t('Link CSS Style') ?></label>
          <?= $form->textarea('link_style', $link_style, array('placeholder'=>t('link_style'))); ?>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
          <label for="mail_content"><?= t('Content') ?></label>
          <?php $editor = Core::make('editor'); ?>
          <?= $editor->outputStandardEditor('mail_content', $mail_content); ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <h4>Header & Footer</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <div class="form-group">
          <label for="mail_header"><?= t('Mail header') ?></label>
          <?= $form->textarea('mail_header', $mail_header, array('placeholder'=>t('header (html)'))); ?>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
          <label for="mail_footer"><?= t('Mail footer') ?></label>
          <?= $form->textarea('mail_footer', $mail_footer, array('placeholder'=>t('footer (html)'))); ?>
      </div>
    </div>
  </div>
  <div class="ccm-dashboard-form-actions-wrapper">
      <div class="ccm-dashboard-form-actions">
          <?= $form->submit('save', 'Save', array("class" => "pull-right btn btn-success")); ?>
      </div>
  </div>
</form>

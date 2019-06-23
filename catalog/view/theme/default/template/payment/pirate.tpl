
<center>
<?php if ($address) { ?>
  <h2><?php echo $text_instruction; ?></h2><br>
      <p><b><?php echo $text_description; ?></b></p>
  <div>
    <div>
      <?php if ($qr) { ?>
      <img src="<?php echo $qr; ?>" alt="<?php echo $address; ?>" title="<?php echo $address; ?>">
      <?php } ?>
    </div>    <div>
      <p>&nbsp;</p>
      <p><?php echo $address; ?></p>
    </div>
  </div>
  <div class="buttons">
    <div>
      <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" />
    </div>
  </div></center>
  <script type="text/javascript"><!--
    $('#button-confirm').on('click', function() {
      $.ajax({
        type: 'get',
        url: 'index.php?route=payment/pirate/confirm',
        cache: false,
        beforeSend: function() {
          $('#button-confirm').button('loading');
        },
        complete: function() {
          $('#button-confirm').button('reset');
        },
        success: function() {
          location = '<?php echo $continue; ?>';
        }
      });
    });
    //--></script>
<?php } ?>
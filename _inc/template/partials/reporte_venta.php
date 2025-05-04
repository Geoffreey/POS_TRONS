<?php $hide_colums = "";?>
<div class="table-responsive">                     
  <table id="profit-profit-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
  <thead>
      <tr class="bg-gray">
      <th class="w-5"><?php echo trans('label_serial_no'); ?></th> <!-- serial_no -->
      <th class="w-20"><?php echo trans('label_invoice_id'); ?></th> <!-- invoice_id -->
      <th class="w-20"><?php echo trans('label_created_at'); ?></th> <!-- created_at -->
      <th class="w-20"><?php echo trans('label_title'); ?></th> <!-- title (VENTA) -->
      <th class="w-20"><?php echo trans('label_this_month'); ?></th> <!-- this_month -->
      <th class="w-20"><?php echo trans('label_this_year'); ?></th> <!-- this_year -->
      <th class="w-20"><?php echo trans('label_till_now'); ?></th> <!-- till_now -->
    </thead>
    <tfoot>
  <tr class="bg-gray">
    <th class="text-right"><?php echo trans('label_total'); ?></th> <!-- serial_no -->
    <th></th> <!-- invoice_id -->
    <th></th> <!-- created_at -->
    <th></th> <!-- title -->
    <th><?php echo trans('label_this_month'); ?></th> <!-- this_month -->
    <th><?php echo trans('label_this_year'); ?></th> <!-- this_year -->
    <th><?php echo trans('label_till_now'); ?></th> <!-- till_now -->
  </tr>
</tfoot>
  </table>    
</div>
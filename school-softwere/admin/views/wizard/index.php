<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;
?>
<div class="gma-wrap">
<div class="gma-page-content">
<div style="max-width:700px;margin:40px auto;">
  <div style="text-align:center;margin-bottom:32px;">
    <h1 style="font-family:'Nunito',sans-serif;font-size:28px;font-weight:800;color:var(--gma-primary);">🏫 Welcome to GMA School</h1>
    <p style="color:var(--gma-text-muted);">Let's set up your school management system in just a few steps.</p>
  </div>

  <!-- Progress -->
  <div style="display:flex;gap:0;margin-bottom:32px;">
    <?php
    $steps = array('School Info', 'Academic Session', 'Classes & Sections', 'Done!');
    foreach($steps as $i => $lbl):
      $n = $i+1;
      $active = $step === $n;
      $done   = $step > $n;
    ?>
    <div style="flex:1;text-align:center;">
      <div style="width:36px;height:36px;border-radius:50%;margin:0 auto 6px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;background:<?php echo $done?'#10B981':($active?'#4F46E5':'#E5E7EB'); ?>;color:<?php echo $done||$active?'#fff':'#6B7280'; ?>;"><?php echo $done?'✓':$n; ?></div>
      <div style="font-size:12px;font-weight:<?php echo $active?'700':'500'; ?>;color:<?php echo $active?'var(--gma-primary)':'var(--gma-text-muted)'; ?>;"><?php echo esc_html($lbl); ?></div>
    </div>
    <?php if($n < count($steps)): ?><div style="flex:1;height:2px;background:<?php echo $step>$n?'#10B981':'#E5E7EB'; ?>;margin-top:18px;align-self:flex-start;"></div><?php endif; ?>
    <?php endforeach; ?>
  </div>

  <?php if($step === 1): ?>
  <div class="gma-card">
    <h3 style="margin-bottom:20px;">Step 1: School Information</h3>
    <form id="gma-wizard-school" class="gma-ajax-form" data-redirect="<?php echo esc_url(add_query_arg('step',2)); ?>">
      <input type="hidden" name="action" value="gma_save_school">
      <div class="gma-form-row">
        <div class="gma-form-group"><label>School Name <span class="req">*</span></label><input type="text" name="label" value="Guidance Modern Academy" required></div>
        <div class="gma-form-group"><label>Registration Number</label><input type="text" name="registration_number"></div>
      </div>
      <div class="gma-form-row">
        <div class="gma-form-group"><label>Phone</label><input type="tel" name="phone"></div>
        <div class="gma-form-group"><label>Email</label><input type="email" name="email"></div>
      </div>
      <div class="gma-form-group"><label>Address</label><textarea name="address" rows="2"></textarea></div>
      <div style="display:flex;justify-content:flex-end;margin-top:20px;">
        <button type="submit" class="gma-btn gma-btn-primary">Next: Session Setup →</button>
      </div>
    </form>
  </div>

  <?php elseif($step === 2): ?>
  <div class="gma-card">
    <h3 style="margin-bottom:20px;">Step 2: Academic Session</h3>
    <form id="gma-wizard-session" class="gma-ajax-form" data-redirect="<?php echo esc_url(add_query_arg('step',3)); ?>">
      <input type="hidden" name="action" value="gma_save_session">
      <input type="hidden" name="is_active" value="1">
      <div class="gma-form-row">
        <div class="gma-form-group"><label>Session Label <span class="req">*</span></label><input type="text" name="label" value="<?php echo esc_attr(date('Y').'-'.(date('Y')+1)); ?>" required></div>
      </div>
      <div class="gma-form-row">
        <div class="gma-form-group"><label>Start Date</label><input type="text" name="start_date" class="gma-datepicker" value="<?php echo date('Y-04-01'); ?>"></div>
        <div class="gma-form-group"><label>End Date</label><input type="text" name="end_date" class="gma-datepicker" value="<?php echo date('Y+1-03-31', strtotime('+1 year')); ?>"></div>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:20px;">
        <a href="<?php echo esc_url(add_query_arg('step',1)); ?>" class="gma-btn gma-btn-secondary">← Back</a>
        <button type="submit" class="gma-btn gma-btn-primary">Next: Classes →</button>
      </div>
    </form>
  </div>

  <?php elseif($step === 3): ?>
  <div class="gma-card">
    <h3 style="margin-bottom:20px;">Step 3: Add Classes</h3>
    <p style="color:var(--gma-text-muted);font-size:13px;margin-bottom:16px;">Add classes like Nursery, KG, Class 1, Class 2… etc.</p>
    <div id="gma-class-wizard-list" style="margin-bottom:16px;"></div>
    <form id="gma-wizard-class" style="display:flex;gap:10px;align-items:flex-end;">
      <div class="gma-form-group" style="flex:1;"><label>Class Name</label><input type="text" id="gma-new-class-name" placeholder="e.g. Class 1"></div>
      <button type="button" class="gma-btn gma-btn-primary" id="gma-add-class-btn">➕ Add</button>
    </form>
    <div style="margin-top:24px;display:flex;justify-content:space-between;">
      <a href="<?php echo esc_url(add_query_arg('step',2)); ?>" class="gma-btn gma-btn-secondary">← Back</a>
      <a href="<?php echo esc_url(add_query_arg('step',4)); ?>" class="gma-btn gma-btn-success">Finish Setup ✓</a>
    </div>
  </div>
  <script>
  jQuery(function($){
    var classes=[];
    $('#gma-add-class-btn').on('click',function(){
      var name=$('#gma-new-class-name').val().trim();
      if(!name) return;
      GMA_Ajax.post('gma_save_class',{label:name},function(data){
        classes.push({id:data.id,name:name});
        var html='<span style="display:inline-block;background:var(--gma-primary-light);color:var(--gma-primary);padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;margin:4px;">'+name+'</span>';
        $('#gma-class-wizard-list').append(html);
        $('#gma-new-class-name').val('');
        GMA_UI.toast('Class added!','success');
      });
    });
  });
  </script>

  <?php else: ?>
  <div class="gma-card" style="text-align:center;padding:40px;">
    <div style="font-size:64px;margin-bottom:16px;">🎉</div>
    <h2 style="font-family:'Nunito',sans-serif;font-weight:800;color:var(--gma-primary);">Setup Complete!</h2>
    <p style="color:var(--gma-text-muted);margin-bottom:24px;">Your school management system is ready. Start adding students, staff, and more.</p>
    <a href="<?php echo esc_url(admin_url('admin.php?page=gma-school')); ?>" class="gma-btn gma-btn-primary" style="font-size:16px;padding:14px 32px;">Go to Dashboard 🚀</a>
  </div>
  <?php endif; ?>
</div>
</div>
</div>

<?php defined( 'ABSPATH' ) || exit;
global $wpdb;
$school_id  = GMA_Helper::get_current_school_id();
$session_id = GMA_Helper::get_active_session_id( $school_id );
$classes    = $wpdb->get_results( $wpdb->prepare(
    "SELECT cs.ID, c.label FROM " . GMA_TABLE_CLASS_SCHOOL . " cs INNER JOIN " . GMA_TABLE_CLASSES . " c ON cs.class_id=c.ID WHERE cs.school_id=%d AND cs.session_id=%d ORDER BY c.label",
    $school_id, $session_id
) );
$filter_class = isset( $_GET['class_school_id'] ) ? (int) $_GET['class_school_id'] : 0;
$filter_exam  = isset( $_GET['exam_id'] )         ? (int) $_GET['exam_id']         : 0;
$exams        = $filter_class ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_EXAMS . " WHERE class_school_id=%d ORDER BY created_at DESC", $filter_class ) ) : array();
?>
<div class="gma-wrap">
  <?php include GMA_PLUGIN_DIR . 'admin/views/partials/header.php'; ?>
  <div class="gma-page-content">
    <div class="gma-page-title-row">
      <div>
        <h1 class="gma-page-title">📈 Academic Report</h1>
        <p class="gma-breadcrumb"><a href="<?php echo esc_url( admin_url( 'admin.php?page=gma-school' ) ); ?>">Dashboard</a> › Academic Report</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="gma-card" style="margin-bottom:20px;">
      <form method="get" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <input type="hidden" name="page" value="gma-academic-report">
        <div class="gma-form-group" style="min-width:180px;"><label>Class</label>
          <select name="class_school_id" class="gma-select2" onchange="this.form.submit()">
            <option value="">— Select Class —</option>
            <?php foreach ( $classes as $c ) : ?><option value="<?php echo esc_attr($c->ID); ?>" <?php selected($filter_class,$c->ID); ?>><?php echo esc_html($c->label); ?></option><?php endforeach; ?>
          </select>
        </div>
        <?php if ( $exams ) : ?>
        <div class="gma-form-group" style="min-width:200px;"><label>Exam</label>
          <select name="exam_id" onchange="this.form.submit()">
            <option value="">— Select Exam —</option>
            <?php foreach ( $exams as $e ) : ?><option value="<?php echo esc_attr($e->ID); ?>" <?php selected($filter_exam,$e->ID); ?>><?php echo esc_html($e->label); ?></option><?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=gma-academic-report' ) ); ?>" class="gma-btn gma-btn-secondary">Reset</a>
      </form>
    </div>

    <?php if ( $filter_class && $filter_exam ) :
      // Load students + their results for this exam
      $students = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . GMA_TABLE_STUDENT_RECORDS . " WHERE class_school_id=%d AND is_active=1 ORDER BY roll_number, first_name", $filter_class ) );
      $papers   = $wpdb->get_results( $wpdb->prepare( "SELECT ep.*, sub.label AS subject FROM " . GMA_TABLE_EXAM_PAPERS . " ep INNER JOIN " . GMA_TABLE_SUBJECTS . " sub ON ep.subject_id=sub.ID WHERE ep.exam_id=%d ORDER BY sub.label", $filter_exam ) );
    ?>
    <div class="gma-table-wrap">
      <div style="padding:14px 18px;border-bottom:1px solid var(--gma-border);display:flex;justify-content:space-between;align-items:center;">
        <strong><?php echo esc_html( count($students) ); ?> students &nbsp;|&nbsp; <?php echo esc_html( count($papers) ); ?> subjects</strong>
        <button class="gma-btn gma-btn-secondary gma-btn-sm" onclick="window.print()">🖨️ Print Results</button>
      </div>
      <div style="overflow-x:auto;">
        <table class="gma-table">
          <thead>
            <tr>
              <th>#</th><th>Student</th><th>Adm. No</th>
              <?php foreach ( $papers as $p ) : ?><th><?php echo esc_html($p->subject); ?><br><small style="font-weight:400;"><?php echo esc_html($p->total_marks); ?>M</small></th><?php endforeach; ?>
              <th>Total</th><th>%</th><th>Result</th>
            </tr>
          </thead>
          <tbody>
          <?php $n = 1; foreach ( $students as $s ) :
            $total_obtained = 0; $total_max = 0; $failed = false;
            $subject_marks  = array();
            foreach ( $papers as $p ) {
              $result = $wpdb->get_row( $wpdb->prepare( "SELECT obtained_marks FROM " . GMA_TABLE_EXAM_RESULTS . " WHERE exam_paper_id=%d AND student_record_id=%d", $p->ID, $s->ID ) );
              $marks  = $result ? (float)$result->obtained_marks : null;
              $subject_marks[$p->ID] = $marks;
              if ( $marks !== null ) { $total_obtained += $marks; $total_max += (float)$p->total_marks; if ( $marks < (float)$p->pass_marks ) $failed = true; }
            }
            $pct = $total_max > 0 ? round( ($total_obtained / $total_max) * 100, 1 ) : 0;
          ?>
          <tr>
            <td><?php echo $n++; ?></td>
            <td><strong><?php echo esc_html($s->first_name.' '.$s->last_name); ?></strong></td>
            <td><code><?php echo esc_html($s->admission_number); ?></code></td>
            <?php foreach ( $papers as $p ) :
              $m = $subject_marks[$p->ID];
              $color = $m === null ? '#999' : ($m < (float)$p->pass_marks ? 'var(--gma-danger)' : 'inherit');
            ?><td style="color:<?php echo $color; ?>;font-weight:600;"><?php echo $m !== null ? esc_html($m) : '—'; ?></td><?php endforeach; ?>
            <td><strong><?php echo esc_html($total_obtained.'/'.$total_max); ?></strong></td>
            <td><?php echo esc_html($pct); ?>%</td>
            <td><?php echo wp_kses_post( GMA_Helper::status_badge( $failed ? 'absent' : 'present' ) ); // using present=pass, absent=fail ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php else : ?>
    <div class="gma-notice gma-notice-info">Please select a class and exam above to generate the academic report.</div>
    <?php endif; ?>
  </div>
</div>

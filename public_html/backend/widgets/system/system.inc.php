<?php

  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') return;

// CPU

  $cpu_usage = function_exists('sys_getloadavg') ? sys_getloadavg()[0] : false;

// Memory

  $fh = fopen('/proc/meminfo','r');
    $ram_usage = 0;
    while ($line = fgets($fh)) {
      $pieces = array();
      if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
        $ram_usage = $pieces[1];
        continue;
      }
      if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
        $ram_free = $pieces[1];
        continue;
      }
    }
    fclose($fh);

    $ram_total = $ram_usage + $ram_free;

// Uptime

  $raw_uptime = (int)file_get_contents('/proc/uptime');

  $uptime = [
    'days' => intval($raw_uptime / (60*60*24)),
    'hours' => intval(($raw_uptime / (60*60)) % 24),
    'minutes' => intval(($raw_uptime / 60) % 60),
    'seconds' => intval($raw_uptime % 60),
  ];

// Software

  $version_query = database::query(
    "SHOW VARIABLES LIKE 'version'"
  );

  $mysql_version = database::fetch($version_query, 'Value');

?>
<style>
meter {
  width: 100%;
  height: 1em;
}
.uptime span + span::before {
  content: ', ';
}
</style>

<section id="widget-system" class="card card-default">
  <div class="card-body">
    <div class="row">
      <div class="col-md-3">
        <h3><?php echo language::translate('title_cpu_usage', 'CPU Usage'); ?></h3>
        <meter value=<?php echo $cpu_usage; ?> max=100 min=0 high=30 low=10 optimum=5></meter>
      </div>

      <div class="col-md-3">
        <h3><?php echo language::translate('title_ram_usage', 'RAM Usage'); ?></h3>
        <meter value=<?php echo round($ram_usage / $ram_total * 100); ?> max=100 min=0 high=30 low=10 optimum=5></meter>
      </div>

      <div class="col-md-3">
        <h3><?php echo language::translate('title_uptime', 'Uptime'); ?></h3>
        <div class="uptime">
          <?php if ($uptime['days']) echo '<span>' . $uptime['days'] .' '. language::translate('text_days', 'day(s)') .'</span>'; ?>
          <?php if ($uptime['hours']) echo '<span>' . $uptime['hours'] .' '. language::translate('text_hours', 'hour(s)') .'</span>'; ?>
          <?php if ($uptime['minutes']) echo '<span>' . $uptime['minutes'] .' '. language::translate('text_minutes', 'minute(s)') .'</span>'; ?>
          <?php if ($uptime['seconds']) echo '<span>' . $uptime['seconds'] .' '. language::translate('text_seconds', 'second(s)') .'</span>'; ?>
        </div>
      </div>

      <div class="col-md-3">
        <h3><?php echo language::translate('title_software', 'Software'); ?></h3>
        <div class="software">
          <span><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
          <span>MySQL/<?php echo $mysql_version; ?></span>
        </div>
      </div>
    </div>
  </div>
</section>
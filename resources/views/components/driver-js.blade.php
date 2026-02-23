@php
$merged = array_merge(config('driver-js'), $config ?? []);
$filtered = $merged;
unset($filtered['cdn_js'], $filtered['cdn_css']);
@endphp
<link rel="stylesheet" href="{{ $merged['cdn_css'] }}">
<script src="{{ $merged['cdn_js'] }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const config = <?php echo json_encode($filtered); ?>;
  const driver = new Driver(config);
  const steps = <?php echo json_encode($steps ?? []); ?>;
  if (config.callbacks) {
    Object.keys(config.callbacks).forEach(function (key) {
      var fnName = config.callbacks[key];
      if (typeof fnName === 'string' && typeof window[fnName] === 'function') {
        config.callbacks[key] = window[fnName];
      }
    });
  }
  if (Array.isArray(steps) && steps.length > 0) {
    driver.defineSteps(steps);
  }
  driver.drive();
});
</script>

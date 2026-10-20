/* laravel-driver-js runtime: registers tours rendered by <x-driver-js> and starts them. */
(function () {
  if (window.LaravelDriverJs) {
    return;
  }

  var PREFIX = 'laravel-driver-js:';
  var tours = {};
  var active = null;

  function warn(message) {
    if (window.console) {
      console.warn('[laravel-driver-js] ' + message);
    }
  }

  function lookup(path) {
    return String(path).split('.').reduce(function (target, key) {
      return target == null ? undefined : target[key];
    }, window);
  }

  function storage(action, key) {
    try {
      if (action === 'get') return window.localStorage.getItem(PREFIX + key);
      if (action === 'set') return window.localStorage.setItem(PREFIX + key, '1');
      return window.localStorage.removeItem(PREFIX + key);
    } catch (e) {
      return null;
    }
  }

  function seen(tour) {
    return Boolean(tour.once) && storage('get', tour.once) === '1';
  }

  function build(tour) {
    var factory = window.driver && window.driver.js && window.driver.js.driver;
    if (typeof factory !== 'function') {
      warn('Driver.js is not loaded (window.driver.js.driver is missing). Check config/driver-js.php "assets".');
      return null;
    }

    var config = Object.assign({}, tour.config);

    Object.keys(tour.hooks || {}).forEach(function (hook) {
      var fn = lookup(tour.hooks[hook]);
      if (typeof fn === 'function') {
        config[hook] = fn;
      } else {
        warn('hook ' + hook + ': window.' + tour.hooks[hook] + ' is not a function, skipped.');
      }
    });

    var userDestroyed = config.onDestroyed;
    config.onDestroyed = function (element, step, opts) {
      if (active && active.name === tour.name) active = null;
      if (userDestroyed) userDestroyed(element, step, opts);
    };

    return factory(config);
  }

  function start(name, stepIndex) {
    var tour = tours[name];
    if (!tour) {
      warn('no tour named "' + name + '" on this page.');
      return null;
    }

    if (active && active.instance.isActive()) {
      active.instance.destroy();
    }

    var instance = build(tour);
    if (!instance) return null;

    active = { name: name, instance: instance };

    // Recorded on start, not on destroy: Driver.js skips onDestroyed when a tour is
    // closed before its first highlight transition settles.
    if (tour.once) storage('set', tour.once);

    if (tour.highlight) {
      instance.highlight(tour.highlight);
    } else {
      instance.setSteps(tour.steps || []);
      instance.drive(stepIndex || 0);
    }

    return instance;
  }

  function boot() {
    Object.keys(tours).forEach(function (name) {
      var tour = tours[name];
      if (tour.autoStart && !tour.started && !seen(tour)) {
        tour.started = true;
        start(name);
      }
    });
  }

  function whenReady(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, { once: true });
    } else {
      setTimeout(callback, 0);
    }
  }

  // True between Livewire's livewire:navigate and livewire:navigated events. Tours
  // registered by the incoming page wait for the swap to finish before measuring.
  var navigating = false;

  function register(tour) {
    tours[tour.name] = tour;
    if (!navigating) whenReady(boot);
  }

  // <button data-driver-start="welcome">Take the tour</button>
  document.addEventListener('click', function (event) {
    var trigger = event.target && event.target.closest && event.target.closest('[data-driver-start]');
    if (trigger) {
      event.preventDefault();
      start(trigger.getAttribute('data-driver-start'));
    }
  });

  // Alpine: $dispatch('driver-js:start', { tour: 'welcome' })
  // Livewire: $this->dispatch('driver-js:start', tour: 'welcome')
  window.addEventListener('driver-js:start', function (event) {
    var detail = event.detail || {};
    if (Array.isArray(detail)) detail = detail[0] || {};
    start(typeof detail === 'string' ? detail : (detail.tour || detail.name), detail.step);
  });

  // Livewire wire:navigate swaps the page without a DOMContentLoaded event.
  document.addEventListener('livewire:navigate', function () {
    navigating = true;
    if (active && active.instance.isActive()) active.instance.destroy();
    Object.keys(tours).forEach(function (name) { delete tours[name]; });
  });
  document.addEventListener('livewire:navigated', function () {
    navigating = false;
    setTimeout(boot, 0);
  });

  window.LaravelDriverJs = {
    register: register,
    start: start,
    reset: function (key) { storage('remove', key); },
    active: function () { return active && active.instance.isActive() ? active.instance : null; },
    tours: tours
  };
})();

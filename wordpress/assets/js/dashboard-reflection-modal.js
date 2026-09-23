/**
 * Reflection journal signup/signin modal.
 *
 * @package reci-media-hub
 */

document.addEventListener('DOMContentLoaded', function () {
  var modal = document.getElementById('reci-reflection-modal');
  if (!modal) return;

  var currentResolve = null;

  // Each element carries its reflection-journal wording as data-default, so
  // another caller can say why IT is asking and the modal still returns to the
  // original copy afterwards.
  function applyCopy(options) {
    [
      ['reci-modal-title', 'title'],
      ['reci-modal-signin-copy', 'signinText'],
      ['reci-modal-signup-copy', 'signupText'],
      ['reci-modal-skip', 'skipText']
    ].forEach(function (pair) {
      var el = document.getElementById(pair[0]);
      if (!el) return;
      var override = options && options[pair[1]];
      el.textContent = override || el.getAttribute('data-default') || el.textContent;
    });
  }

  window.reciShowAuthModal = function (options) {
    return new Promise(function (resolve) {
      if (modal.getAttribute('data-logged-in') === '1' || window.reciIsLoggedIn) {
        resolve(true);
        return;
      }
      applyCopy(options);
      currentResolve = resolve;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    });
  };

  document.getElementById('reci-modal-show-signup').addEventListener('click', function () {
    document.getElementById('reci-modal-signin').classList.add('hidden');
    document.getElementById('reci-modal-signup').classList.remove('hidden');
  });
  document.getElementById('reci-modal-show-signin').addEventListener('click', function () {
    document.getElementById('reci-modal-signup').classList.add('hidden');
    document.getElementById('reci-modal-signin').classList.remove('hidden');
  });

  document.getElementById('reci-modal-close').addEventListener('click', function() { close(false); });
  document.getElementById('reci-modal-skip').addEventListener('click', function() { close(false); });
  modal.addEventListener('click', function (e) {
    if (e.target === modal) close(false);
  });

  function close(success) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (currentResolve) {
      currentResolve(!!success);
      currentResolve = null;
    }
  }

  document.getElementById('reci-modal-signin-form').addEventListener('submit', function (e) {
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', 'reci_modal_signin');
    fd.append('nonce', reciDashboard.nonce);
    submitAuth(fd);
  });

  document.getElementById('reci-modal-signup-form').addEventListener('submit', function (e) {
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', 'reci_modal_signup');
    fd.append('nonce', reciDashboard.nonce);
    submitAuth(fd);
  });

  function submitAuth(fd) {
    fetch(reciDashboard.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          window.reciIsLoggedIn = true;
          if (data.data && data.data.rest_nonce) {
            reciDashboard.restNonce = data.data.rest_nonce;
          }
          // Nonces are tied to the user. The one printed for a signed-out
          // visitor stops verifying the moment they sign in, so every
          // admin-ajax call after this would fail without a fresh one.
          if (data.data && data.data.dashboard_nonce) {
            reciDashboard.nonce = data.data.dashboard_nonce;
          }
          document.querySelectorAll('[data-requires-auth]').forEach(function (el) {
            el.removeAttribute('data-requires-auth');
          });
          modal.setAttribute('data-logged-in', '1');
          close(true);
        } else {
          alert(data.data.message || 'Something went wrong.');
        }
      });
  }
});

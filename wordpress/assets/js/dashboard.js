/**
 * Dashboard frontend JS — bookmark and like toggles.
 *
 * @package reci-media-hub
 */

document.addEventListener('DOMContentLoaded', function () {
  
  // Bookmark Toggles
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.reci-bookmark-btn');
    if (!btn) return;

    e.preventDefault();
    const postId = btn.getAttribute('data-post-id');

    const formData = new FormData();
    formData.append('action', 'reci_toggle_bookmark');
    formData.append('post_id', postId);
    formData.append('nonce', reciDashboard.nonce);

    fetch(reciDashboard.ajaxUrl, {
      method: 'POST',
      body: formData,
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          const isBookmarked = data.data.bookmarked;
          btn.setAttribute('data-bookmarked', isBookmarked ? '1' : '0');
          
          if (isBookmarked) {
            btn.classList.remove('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
            btn.classList.add('bookmarked', 'bg-amber-50', 'text-amber-700', 'border-amber-200');
            btn.querySelector('svg').classList.add('fill-current');
          } else {
            btn.classList.add('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
            btn.classList.remove('bookmarked', 'bg-amber-50', 'text-amber-700', 'border-amber-200');
            btn.querySelector('svg').classList.remove('fill-current');
          }
          
          const labelSpan = btn.querySelector('.bookmark-label');
          if (labelSpan) labelSpan.textContent = isBookmarked ? 'Saved' : 'Save';
        }
      });
  });

  // Like Toggles
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.reci-like-btn');
    if (!btn) return;

    e.preventDefault();
    const postId = btn.getAttribute('data-post-id');

    const formData = new FormData();
    formData.append('action', 'reci_toggle_like');
    formData.append('post_id', postId);
    formData.append('nonce', reciDashboard.nonce);

    fetch(reciDashboard.ajaxUrl, {
      method: 'POST',
      body: formData,
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          const isLiked = data.data.liked;
          btn.setAttribute('data-liked', isLiked ? '1' : '0');
          
          if (isLiked) {
            btn.classList.remove('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
            btn.classList.add('liked', 'bg-red-50', 'text-red-600', 'border-red-200');
            btn.querySelector('svg').classList.add('fill-current');
          } else {
            btn.classList.add('bg-zinc-50', 'text-zinc-600', 'border-zinc-200');
            btn.classList.remove('liked', 'bg-red-50', 'text-red-600', 'border-red-200');
            btn.querySelector('svg').classList.remove('fill-current');
          }
          
          const labelSpan = btn.querySelector('.like-label');
          if (labelSpan) labelSpan.textContent = isLiked ? 'Liked' : 'Like';
        }
      });
  });

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.reci-mark-notification-read');
    if (!btn) return;

    e.preventDefault();

    const notificationId = btn.getAttribute('data-notification-id');
    const formData = new FormData();
    formData.append('action', 'reci_mark_notification_read');
    formData.append('notification_id', notificationId);
    formData.append('nonce', reciDashboard.nonce);

    fetch(reciDashboard.ajaxUrl, {
      method: 'POST',
      body: formData,
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) return;

        const item = btn.closest('li');
        if (item) {
          item.classList.remove('bg-amber-50');
          item.classList.add('bg-white');
        }

        btn.remove();
      });
  });
});

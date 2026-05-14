function toggleMob() {
  var m = document.getElementById('mobMenu');
  if (m) m.classList.toggle('open');
}
function closeMob() {
  var m = document.getElementById('mobMenu');
  if (m) m.classList.remove('open');
}
(function () {
  var c = document.getElementById('mobClose');
  if (c) c.onclick = closeMob;
})();

// Subtle reveal on scroll
const observer = new IntersectionObserver(
  function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  },
  { threshold: 0.05, rootMargin: '0px 0px -40px 0px' }
);

document.querySelectorAll('.ec, .case, .post, .di, .trig-item').forEach(function (el, i) {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition =
    'opacity 0.6s ease ' + i * 0.04 + 's, transform 0.6s ease ' + i * 0.04 + 's';
  observer.observe(el);
});

(function initInsightsTags() {
  var section = document.getElementById('insights');
  if (!section) return;
  var posts = section.querySelectorAll('.posts a.post[data-tags]');
  if (!posts.length) return;

  function activeTags() {
    var ids = [];
    section.querySelectorAll('button.ins-tag[data-tag].on').forEach(function (b) {
      ids.push(b.getAttribute('data-tag'));
    });
    return ids;
  }

  function applyFilter() {
    var sel = activeTags();
    posts.forEach(function (post) {
      if (!sel.length) {
        post.classList.remove('ins-filter-hidden');
        return;
      }
      var raw = post.getAttribute('data-tags') || '';
      var tags = raw.split(/\s+/).filter(Boolean);
      var show = sel.some(function (t) {
        return tags.indexOf(t) !== -1;
      });
      if (show) post.classList.remove('ins-filter-hidden');
      else post.classList.add('ins-filter-hidden');
    });
  }

  section.addEventListener('click', function (ev) {
    var btn = ev.target.closest('button.ins-tag[data-tag]');
    if (!btn || !section.contains(btn)) return;
    ev.preventDefault();
    btn.classList.toggle('on');
    btn.setAttribute('aria-pressed', btn.classList.contains('on') ? 'true' : 'false');
    applyFilter();
  });

  applyFilter();
})();

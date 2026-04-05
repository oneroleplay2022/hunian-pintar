/* ============================================
   APLIKASI TRANSPARANSI WARGA - Core JS
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  initDarkMode();
  initAnimations();
  initDropdowns();
  initModals();
  initTabs();
});

/* ---- Sidebar ---- */
function initSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const toggle = document.querySelector('.btn-sidebar-toggle');
  const overlay = document.querySelector('.sidebar-overlay');

  if (toggle) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      overlay?.classList.toggle('active');
    });
  }

  if (overlay) {
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    });
  }

  // Mark active menu item based on current page
  const currentPage = window.location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.menu-item').forEach(item => {
    const href = item.getAttribute('href');
    if (href && href === currentPage) {
      item.classList.add('active');
    }
  });
}

/* ---- Dark Mode ---- */
function initDarkMode() {
  const toggle = document.getElementById('darkModeToggle');
  const savedTheme = localStorage.getItem('theme') || 'light';

  document.documentElement.setAttribute('data-theme', savedTheme);
  updateDarkModeIcon(savedTheme);

  if (toggle) {
    toggle.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('theme', next);
      updateDarkModeIcon(next);
    });
  }
}

function updateDarkModeIcon(theme) {
  const toggle = document.getElementById('darkModeToggle');
  if (toggle) {
    toggle.innerHTML = theme === 'dark' ? '☀️' : '🌙';
    toggle.title = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
  }
}

/* ---- Scroll Animations ---- */
function initAnimations() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fadeIn');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
  });
}

/* ---- Dropdown Toggle ---- */
function initDropdowns() {
  document.querySelectorAll('[data-dropdown]').forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      const target = document.getElementById(trigger.dataset.dropdown);
      if (target) {
        // Close all others
        document.querySelectorAll('.dropdown-menu.active').forEach(m => {
          if (m !== target) m.classList.remove('active');
        });
        target.classList.toggle('active');
      }
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu.active').forEach(m => {
      m.classList.remove('active');
    });
  });
}

/* ---- Modal ---- */
function initModals() {
  document.querySelectorAll('[data-modal]').forEach(trigger => {
    trigger.addEventListener('click', () => {
      const modal = document.getElementById(trigger.dataset.modal);
      if (modal) openModal(modal);
    });
  });

  document.querySelectorAll('.modal-close, .modal-cancel').forEach(btn => {
    btn.addEventListener('click', () => {
      const overlay = btn.closest('.modal-overlay');
      if (overlay) closeModal(overlay);
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal(overlay);
    });
  });
}

function openModal(overlay) {
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeModal(overlay) {
  overlay.classList.remove('active');
  document.body.style.overflow = '';
}

/* ---- Tabs ---- */
function initTabs() {
  document.querySelectorAll('.tabs').forEach(tabGroup => {
    const buttons = tabGroup.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        // Remove active from all buttons
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Show target panel
        const targetId = btn.dataset.tab;
        if (targetId) {
          const parent = tabGroup.parentElement;
          parent.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.remove('active');
            panel.style.display = 'none';
          });
          const target = document.getElementById(targetId);
          if (target) {
            target.classList.add('active');
            target.style.display = '';
          }
        }
      });
    });
  });
}

/* ---- Toast Notifications ---- */
function showToast(message, type = 'info', duration = 4000) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = {
    success: '✅',
    error: '❌',
    warning: '⚠️',
    info: 'ℹ️'
  };

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <span class="toast-icon">${icons[type] || icons.info}</span>
    <span class="toast-message">${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(120%)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

/* ---- Number Formatting (Indonesian) ---- */
function formatRupiah(number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(number);
}

function formatNumber(number) {
  return new Intl.NumberFormat('id-ID').format(number);
}

/* ---- Date Formatting ---- */
function formatDate(dateStr) {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
  const d = new Date(dateStr);
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

/* ---- Confirm Dialog ---- */
function confirmAction(message) {
  return new Promise((resolve) => {
    // For now using native confirm, can be replaced with custom modal
    resolve(window.confirm(message));
  });
}

/* ---- Debounce Utility ---- */
function debounce(fn, wait = 300) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), wait);
  };
}

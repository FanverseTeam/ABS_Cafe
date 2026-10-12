/**
 * ABS CAFE - JAVASCRIPT ADMIN INTERACTION (JS ONLY)
 * Fitur:
 *  - Modal trigger & toggle
 *  - Toast alert otomatis
 *  - Konfirmasi dialog hapus
 *  - Format mata uang Rupiah
 *  - Mobile drawer toggle
 *  - Cetak nota kasir
 */

// Format Rupiah Helper
function formatRupiah(angka) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(angka);
}

// Format Tanggal Indonesia
function formatTanggalIndo(dateStr) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date) + ' WIB';
}

// Toast Notification
function showToast(pesan, tipe = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  const bgClass = tipe === 'success' ? 'bg-[#4F7A4A]' : (tipe === 'error' ? 'bg-[#B03A2E]' : 'bg-[#4A1F14]');
  toast.className = `${bgClass} text-[#F6EDE0] px-4 py-3 rounded-2xl shadow-xl flex items-center justify-between gap-3 text-xs md:text-sm font-medium transform transition-all duration-300 translate-y-3 opacity-0`;
  toast.innerHTML = `
    <span>${pesan}</span>
    <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">&times;</button>
  `;

  container.appendChild(toast);

  // Trigger animation
  setTimeout(() => {
    toast.classList.remove('translate-y-3', 'opacity-0');
  }, 10);

  // Auto remove
  setTimeout(() => {
    toast.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

// Modal Handlers
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }
}

// Confirm Delete Dialog Helper
function confirmAction(pesan, formElement) {
  if (confirm(pesan || 'Apakah Anda yakin ingin melanjutkan tindakan ini?')) {
    formElement.submit();
  }
  return false;
}

// Print Receipt
function printNota() {
  window.print();
}

// Auto Initialize Lucide Icons on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) {
    window.lucide.createIcons();
  }

  // Handle flash messages from Laravel session
  const flashSuccess = document.querySelector('[data-flash-success]');
  if (flashSuccess) {
    showToast(flashSuccess.getAttribute('data-flash-success'), 'success');
  }
  const flashError = document.querySelector('[data-flash-error]');
  if (flashError) {
    showToast(flashError.getAttribute('data-flash-error'), 'error');
  }
});

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
  const bgClass = tipe === 'success' ? 'bg-[#775533]' : (tipe === 'error' ? 'bg-[#775533]' : (tipe === 'warning' ? 'bg-[#D4954D]' : 'bg-[#290024]'));
  const textClass = tipe === 'warning' ? 'text-midnight' : 'text-beige';
  const btnClass = tipe === 'warning' ? 'text-midnight/70 hover:text-midnight' : 'text-beige/80 hover:text-beige';
  toast.className = `${bgClass} ${textClass} px-4 py-3 rounded-2xl shadow-xl flex items-center justify-between gap-3 text-xs md:text-sm font-medium transform transition-all duration-300 translate-y-3 opacity-0`;
  toast.innerHTML = `
    <span>${pesan}</span>
    <button onclick="this.parentElement.remove()" class="${btnClass}">&times;</button>
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

  // Intercept file inputs with HEIC/HEIF to auto-convert them to JPEG in the browser
  document.addEventListener('change', async (e) => {
    const input = e.target;
    if (!input || input.type !== 'file' || !input.files || input.files.length === 0) return;

    const file = input.files[0];
    const isHeic = file && (
      /\.(heic|heif)$/i.test(file.name) ||
      file.type === 'image/heic' ||
      file.type === 'image/heif'
    );

    if (!isHeic) return;

    if (typeof window.heic2any === 'function') {
      const form = input.closest('form');
      const submitBtn = form?.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
        submitBtn.innerHTML = '<span>Mengonversi HEIC...</span>';
      }

      try {
        const convertedBlob = await window.heic2any({
          blob: file,
          toType: 'image/jpeg',
          quality: 0.9
        });

        const blobResult = Array.isArray(convertedBlob) ? convertedBlob[0] : convertedBlob;
        const newName = file.name.replace(/\.(heic|heif)$/i, '.jpg');
        const convertedFile = new File([blobResult], newName, { type: 'image/jpeg' });

        const dt = new DataTransfer();
        dt.items.add(convertedFile);
        input.files = dt.files;

        // Panggil preview jika ada elemen preview terkait
        const previewImg = input.closest('form, div')?.querySelector('img[id*="preview"]') ||
                           document.getElementById('create-preview-img') ||
                           document.getElementById('edit-preview-img');
        const previewContainer = input.closest('form, div')?.querySelector('[id*="preview"]') ||
                                document.getElementById('create-image-preview') ||
                                document.getElementById('edit-image-preview');

        if (previewImg && previewContainer) {
          const reader = new FileReader();
          reader.onload = function(evt) {
            previewImg.src = evt.target.result;
            previewContainer.classList.remove('hidden');
          };
          reader.readAsDataURL(convertedFile);
        }
      } catch (err) {
        console.warn('Konversi client-side dilewati, file akan diproses oleh server:', err);
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.style.opacity = '1';
          submitBtn.innerHTML = originalBtnText;
        }
      }
    }
  });
});

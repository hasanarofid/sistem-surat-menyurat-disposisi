import Swal from 'sweetalert2';

const Alert = {
  success(message, title = 'Berhasil!') {
    return Swal.fire({
      icon: 'success',
      title: title,
      text: message,
      timer: 2000,
      showConfirmButton: false,
      timerProgressBar: true,
      customClass: {
        popup: 'rounded-3xl border-none shadow-premium',
        title: 'text-slate-800 font-black',
        htmlContainer: 'text-slate-500 font-medium'
      }
    });
  },

  error(message, title = 'Opps!') {
    return Swal.fire({
      icon: 'error',
      title: title,
      text: message,
      customClass: {
        popup: 'rounded-3xl border-none shadow-premium',
        title: 'text-slate-800 font-black',
        htmlContainer: 'text-slate-500 font-medium',
        confirmButton: 'bg-primary px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest'
      }
    });
  },

  warning(message, title = 'Peringatan!') {
    return Swal.fire({
      icon: 'warning',
      title: title,
      text: message,
      customClass: {
        popup: 'rounded-3xl border-none shadow-premium',
        title: 'text-slate-800 font-black'
      }
    });
  },

  confirm(message, title = 'Apakah Anda yakin?', confirmText = 'Ya, Hapus!') {
    return Swal.fire({
      title: title,
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3b82f6',
      cancelButtonColor: '#ef4444',
      confirmButtonText: confirmText,
      cancelButtonText: 'Batal',
      customClass: {
        popup: 'rounded-3xl border-none shadow-premium',
        title: 'text-slate-800 font-black',
        htmlContainer: 'text-slate-500 font-medium',
        confirmButton: 'bg-primary px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest',
        cancelButton: 'bg-danger px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest'
      }
    });
  }
};

export default Alert;

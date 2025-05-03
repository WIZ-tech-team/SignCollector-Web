import SweetAlert, { SweetAlertOptions } from 'sweetalert2';

// Questionned Swal Obj
export const QSwal = SweetAlert.mixin({
    heightAuto: false,
    confirmButtonText: "نعم, تأكيد",
    cancelButtonText: "لا, إلغاء",
    customClass: {
        cancelButton: "btn btn-danger",
        confirmButton: "btn btn-primary"
    },
    showCancelButton: true
});

// Messaged Swal Obj
export const MSwal = SweetAlert.mixin({
    heightAuto: false,
    confirmButtonText: "حسنا",
    showCancelButton: false,
    customClass: {
        cancelButton: "btn btn-info",
        confirmButton: "btn btn-primary"
    }
});

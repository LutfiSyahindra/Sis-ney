 <!-- core:js -->
 <script src="{{ asset("template/assets/vendors/core/core.js") }}"></script>
 <!-- endinject -->

 <!-- Plugin js for this page -->
 <script src="{{ asset("template/assets/vendors/flatpickr/flatpickr.min.js") }}"></script>
 <script src="{{ asset("template/assets/vendors/apexcharts/apexcharts.min.js") }}"></script>
 <!-- End plugin js for this page -->

 <!-- inject:js -->
 <script src="{{ asset("template/assets/vendors/feather-icons/feather.min.js") }}"></script>
 <script src="{{ asset("template/assets/js/template.js") }}"></script>
 <!-- endinject -->

 <!-- Custom js for this page -->
 <script src="{{ asset("template/assets/js/dashboard-light.js") }}"></script>
 <!-- End custom js for this page -->
 <!-- Plugin js for this page -->
 <script src="{{ asset("template/assets/vendors/prismjs/prism.js") }}"></script>
 <script src="{{ asset("template/assets/vendors/clipboard/clipboard.min.js") }}"></script>
 <!-- End plugin js for this page -->

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

 <!-- Feather init manual -->
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         if (window.feather) {
             feather.replace();
         }
     });
 </script>

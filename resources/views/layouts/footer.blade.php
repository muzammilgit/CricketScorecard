<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
<script src="{{URL::asset('js/app.js')}}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>

<!-- simplebar js -->
<script src="{{ asset('assets/plugins/simplebar/js/simplebar.js') }}"></script>
<!-- waves effect js -->
<script src="{{ asset('assets/js/waves.js') }}"></script>
<!-- sidebar-menu js -->
<script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
<!-- Custom scripts -->
<script src="{{ asset('assets/js/app-script.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-touchspin/js/bootstrap-touchspin-script.js') }}"></script>

<!--Select Plugins Js-->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<!--Inputtags Js-->
<script src="{{ asset('assets/plugins/inputtags/js/bootstrap-tagsinput.js') }}"></script>
<!-- Vector map JavaScript -->
<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- Chart js -->
<script src="{{ asset('assets/plugins/Chart.js/Chart.min.js') }}"></script>
<!-- Index js -->
{{--<script src="{{ asset('assets/js/index.js') }}"></script>--}}

<script src="{{ asset('assets/plugins/bootstrap-datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-multi-select/jquery.multi-select.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-multi-select/jquery.quicksearch.js') }}"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.10.2/validator.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.0/dist/jquery.validate.min.js"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validation-unobtrusive/3.2.11/jquery.validate.unobtrusive.min.js"></script>--}}
<script>
    $(document).ready(function () {
        $('.multiple-select').select2();
        // $('#my_multi_select1').multiSelect();
        /*$('#my_multi_select1').multiSelect({
            selectableOptgroup: true
        });
        $('#my_multi_select2').multiSelect({
            selectableOptgroup: true
        });*/
        //Default data table
        $('#teams-datatable').DataTable();


        /*var table = $('#example').DataTable( {
            lengthChange: false,
            buttons: [ 'copy', 'excel', 'pdf', 'print', 'colvis' ]
        } );

        table.buttons().container()
            .appendTo( '#example_wrapper .col-md-6:eq(0)' );*/

    });

</script>

<footer class="footer">
    <div class="container">
        <div class="text-center">
            Copyright © 3iology
        </div>
    </div>
</footer>
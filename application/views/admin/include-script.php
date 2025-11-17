<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- Bootstrap 4 -->


<script src="<?= base_url('assets/admin/js/bootstrap.bundle.min.js') ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('assets/admin/jquery-ui/jquery-ui.min.js') ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Ekko Lightbox -->
<!-- google translate library -->
<script src="<?= base_url('assets/admin/custom/translate.js') ?>"></script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script src=<?= base_url('assets/admin/ekko-lightbox/ekko-lightbox.min.js') ?>></script>
<!-- ChartJS -->
<script src="<?= base_url('assets/admin/chart.js/Chart.min.js') ?>"></script>
<!-- Sparkline -->
<script src="<?= base_url('assets/admin/js/sparkline.js') ?>"></script>
<!-- JQVMap -->
<script src="<?= base_url('assets/admin/js/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/js/jquery.vmap.usa.js') ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url('assets/admin/js/jquery.knob.min.js') ?>"></script>
<!-- daterangepicker -->
<script src="<?= base_url('assets/admin/js/moment.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/js/daterangepicker.js') ?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('assets/admin/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<!-- Summernote -->
<script src="<?= base_url('assets/admin/summernote/summernote-bs4.min.js') ?>"></script>
<!-- Toastr -->
<script src="<?= base_url('assets/admin/js/iziToast.min.js') ?>"></script>
<!-- Select -->
<script src="<?= base_url('assets/admin/js/select2.full.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('assets/admin/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/admin/dist/js/adminlte.js') ?>"></script>
<!-- Bootstrap Switch -->
<script src="<?= base_url('assets/admin/js/bootstrap-switch.min.js') ?>"></script>
<!-- Bootstrap Table -->
<script src="<?= base_url('assets/admin/js/bootstrap-table.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/js/tableExport.js') ?>"></script>
<script src="<?= base_url('assets/admin/js/bootstrap-table-export.min.js"') ?>"></script>
<!-- Jquery Fancybox -->
<script src="<?= base_url('assets/admin/js/jquery.fancybox.min.js') ?>"></script>
<!-- Sweeta Alert 2 -->
<script src="<?= base_url('assets/admin/js/sweetalert2.min.js') ?>"></script>
<!-- Block UI -->
<script src="<?= base_url('assets/admin/js/jquery.blockUI.js') ?>"></script>
<!-- JS tree -->
<script src="<?= base_url('assets/admin/js/jstree.min.js') ?>"></script>
<!-- Chartist -->
<script src="<?= base_url('assets/admin/js/chartist.js') ?>"></script>
<!-- Tool Tip -->
<script src="<?= base_url('assets/admin/js/tooltip.js') ?>"></script>
<!-- Loader Js -->
<script type="text/javascript" src="<?= base_url('assets/admin/js/loader.js') ?>"></script>
<!-- Dropzone -->
<script type="text/javascript" src="<?= base_url('assets/admin/js/dropzone.js') ?>"></script>

<script type="text/javascript" src="<?= base_url('assets/admin/js/tagify.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/admin/js/jquery.validate.min.js') ?>"></script>

<!-- Custom -->
<script src="<?= base_url('assets/admin/custom/custom.js') ?>"></script>
<script src="<?= base_url('assets/admin/custom/pos.js') ?>"></script>
<!-- Demo -->
<script src="<?= base_url('assets/admin/dist/js/demo.js') ?>"></script>



<?php if ($this->session->flashdata('message')) { ?>
    <script>
        Swal.fire('<?= $this->session->flashdata('message_type') ?>', "<?= $this->session->flashdata('message') ?>", '<?= $this->session->flashdata('message_type') ?>');
    </script>
<?php }
$uris = ['manage-partner', 'manage-cities', "sign_up", 'profile', '/partner/auth/sign_up', '/partner/home/profile'];
// if(in_array($_SERVER['SCRIPT_URL'], $uris)){
if (in_array(basename($_SERVER['PHP_SELF']), $uris)) {
?>
    <script>(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
        ({key: "<?= $google_map_api_key ?>", v: "weekly"});</script>
<?php

}
if ($this->session->flashdata('authorize_flag')) { ?>
    <script>
        Swal.fire('Warning', "<?= $this->session->flashdata('authorize_flag') ?>", 'warning');
    </script>

<?php }
$this->session->set_flashdata('authorize_flag', "");

?>
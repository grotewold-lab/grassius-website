<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<script src="/js/domain_canvas.js"></script>

<h2 class="wiki-top-header">Listing Maize v5 protein transcripts</h2>

<div class="familypage_dom_hovermenu"><ul><lh id="familypage_dom_hovermenu_title">title</lh></ul><p id="familypage_dom_hovermenu_desc">description</p></div>

<br>
<br>

<?php echo $datatable; ?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $("#nav_access").addClass("active");
    })
</script>


<?= $this->endSection() ?>

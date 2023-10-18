<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
<?php require APPPATH."/Views/common/subdomain_urls.php"; ?>

<br>
<h2 class="wiki-top-header">Contact Us</h2>
    <br>       

<p>
    <b>Grotewold Lab</b>
    <br>
    <br>
    GRASSIUS is maintained by the <a href="https://grotewold-lab.com/">Grotewold Lab</a> of <a href="https://msu.edu/">Michigan State University</a>. 
    <br>
    General comments and inquiries may be directed to <a href="mailto:grotewoldlab@gmail.com">grotewoldlab@gmail.com</a>
</p>


<p>
    <b>Contact GRASSIUS Team Members</b>
    <ul>
        <li><a href="mailto:grotewol@msu.edu">Erich Grotewold</a> (Michigan State University) principal investigator</li>
        <li><a href="mailto:John.Gray5@utoledo.edu">John Gray</a> (University of Toledo) protein family descriptions</li>
        <li><a href="mailto:oliver.tessmer@gmail.com">Oliver Tessmer</a> (Michigan State University) webmaster</li>
    </ul>

    <a href="/people">More GRASSIUS people</a>
</p>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_help').addClass("active")
    })
</script>

<?= $this->endSection() ?>
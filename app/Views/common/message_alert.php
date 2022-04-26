<?php
    if( user_is_admin() ) {
        $message='you are logged in as admin!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/logout">logout</a>'; 
        $warning=true;
    }else {
        $message = \Config\Services::session()->getFlashdata('message');
        $warning=false;
    }

    

    if( $message )
    {
?>
    <!-- 
    This element should have bootstrap style using: <div class="alert alert-success" role="alert">
    however this site was developed without bootstrap css
    adding bootstrap css would change the appearance of many other elements
    so, this element has hard-coded style that matches bootstrap
    -->

    <div style="
    font-size: 12;
    font-weight: 400;
    line-height: 1.5;
    text-align: left;
    box-sizing: border-box;
    position: absolute;
    top: 10px;
    left: 400px;
    width: 300px;
    padding: 12 20;
    margin-bottom: 10;
    border: 1px solid transparent;
    border-radius: 10;
            
    <?php if($warning){ ?>
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeeba;
    <?php } else { ?>
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    <?php } ?>
    margin-top: 3rem;">
        <?php echo $message; ?>
    </div>      
<?php
    }
?>
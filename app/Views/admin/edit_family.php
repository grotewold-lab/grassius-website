
<html lang="en">
    
   <?php require_once "header.php"; ?>

    <body>
        <div class="container">
            <div class="row justify-content-md-center">
                <form method="POST" class="form" action="/post_edit_family/<?php echo $familyname; ?>">
                    <h1 class="h3 mb-3 font-weight-normal">edit description for family <?php echo $familyname; ?></h1>

                    <textarea name="description" class="form-control" id="exampleFormControlTextarea1" rows="30"><?php echo trim($description); ?></textarea>

                    <button class="col-1 btn btn-primary btn-block" type="submit">Save</button>
                </form>
            </div>
        </div>
    </body>
</html>
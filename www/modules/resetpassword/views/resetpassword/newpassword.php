<?php
$form = new Helper_Appform();
    if(isset($errors)){
        $form->post = $post;
        $form->errors = $errors;
    }
?>
<div class="widget container-narrow">
    <div class="widget-header">
        <i class="icon-user"></i>
        <h5>Set new password</h5>
    </div> 

    <div style="padding:25px;" class="widget-body clearfix">
        <form action="<?=URL::base()?>resetpassword/setnewpassword" method="POST">
            <?=$form->password('password', '',
                    array('type' => 'password', 'class' => 'required'))?>

           <?=$form->password('password_confirm', '',
                    array('type' => 'password', 'class' => 'required'), 'Confirm Password')?>

            <input type="submit" id="send" name="submit" class="btn pull-right">
        </form>
    </div>  
</div>  
    

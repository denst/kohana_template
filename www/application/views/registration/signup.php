<div>
    <form action="/signup" class="form-horizontal" method="POST">
        
        <?=$form->input('username', '',
                array('class' => 'required', 'placeholder' => 'username'))?>

       <?=$form->input('email', '',
                array('class' => 'required', 'placeholder' => 'email'))?>

       <?=$form->password('password', '',
                array('type' => 'password', 'class' => 'required'))?>

       <?=$form->password('password_confirm', '',
                array('type' => 'password', 'class' => 'required'), 'Confirm Password')?>
        
        <div class="control-group<?=isset($errors['terms'])? ' error': '';?>">
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" name="terms"> I agree with terms
                    <span class="help-block"><?=isset($errors['terms'])? $errors['terms']: '';?></span>
                </label>
                <button type="submit" id="send" class="btn">Register</button>
            </div>
        </div>
        
    </form>
</div>
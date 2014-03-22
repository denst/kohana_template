<div class="control-group <?=$error_class?>">
    <label class="control-label" for="<?=$name?>"><?=$label?>:</label>
    <div class="controls">
        <?=Kohana_Form::password($name, $value, $attributes)?>
        <span class="help-inline error-message"><?=$error_message?></span>
    </div>
</div>
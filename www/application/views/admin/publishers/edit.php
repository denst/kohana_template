<div class="row-fluid" id="settings_anchor">
    <div class="widget widget-padding span12">
        <div class="widget-header">
            <i class="icon-cogs"></i><h5>Settings</h5>
            <div class="widget-buttons">
                <a data-original-title="" href="#" data-title="Collapse" data-collapsed="false" class="tip collapse"></a>
            </div>
        </div>
        <div class="widget-body">
            <div class="widget-forms clearfix">
                <form action="<?=URL::base()?>admin/publishers/edit" 
                      class="form-horizontal" method="POST">
                    <input type="hidden" name="edit" value="true">
                    <input type="hidden" name="publisher_id" value="<?=$publisher->id?>">
                    <legend>Publisher info</legend>

                    <div class="control-group">
                        <label class="control-label">New password:</label>
                        <div class="controls">
                            <input type="password" name="password" class="span4 inputs
                                <?=(isset($errors['password']))? 'error': ''?>" placeholder="New Password">
                        </div>
                        <? if(isset($errors['password'])):?>
                            <div class="label_error"><?=$errors['password']?></div>
                        <? endif?>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Re-type new password:</label>
                        <div class="controls">
                            <input type="password" name="password_confirm" placeholder="Re-type new password" 
                                class="span4 inputs
                                <?=(isset($errors['password_confirm']))? 'error': ''?>">
                        </div>
                        <? if(isset($errors['password_confirm'])):?>
                            <div class="label_error"><?=$errors['password_confirm']?></div>
                        <? endif?>
                    </div>

                    <div class="control-group">
                        <? $email = (isset($email))? $email: $publisher->email?>
                        <label class="control-label">Update email:</label>
                        <div class="controls">
                            <input type="text" name="email" placeholder="Email" class="span4 inputs
                                <?=(isset($errors['email']))? 'error': ''?>"
                                   value="<?=$email?>">
                        </div>
                        <? if(isset($errors['email'])):?>
                            <div class="label_error"><?=$errors['email']?></div>
                        <? endif?>
                    </div>

                    <div class="control-group">
                        <? $terms = (isset($terms))? $terms: $publisher->terms?>
                        <label class="control-label">Terms of use:</label>
                        <div class="controls">
                            <input type="text" name="terms" placeholder="example.com/terms" class="span4 inputs"
                                   value="<?=$terms?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <? $host = (isset($host))? $host: $publisher->host?>
                        <label class="control-label">Website link:</label>
                        <div class="controls">
                            <div id="prepends" class="input-prepend">
                               <select name="scheme" class="selectpicker">
                                   <option value="http://" 
                                        <?=($publisher->scheme == 'http://')? 'checked="checked"': ''?>
                                           >http://</option>
                                   <option value="https://"
                                        <?=($publisher->scheme == 'https://')? 'checked="checked"': ''?>
                                           >https://</option>
                               </select>
                               <input type="text" 
                                    name="host" class="span7 inputs required 
                                    <?=(isset($errors['host']))? 'error': ''?>" 
                                    placeholder="example.com" 
                                    value="<?=(isset($host))? $host: ''?>">
                            </div>
                            <? if(isset($errors['host'])):?>
                                 <div class="label_error"><?=$errors['host']?></div>
                            <? endif?>
                        </div>
                    </div>
                    <button class="btn btn-primary" name="submit" type="submit" value="submit">save</button>
                    <button class="btn" name="submit" type="submit" value="cancel">cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
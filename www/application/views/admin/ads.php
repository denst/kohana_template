<div class="row-fluid">
    <div class="widget widget-padding span12">
        <div class="widget-header">
            <i class="icon-bar-chart"></i>
            <h5>Ads</h5>
        
        </div>
        <div class="widget-body">
                <table></table>
            <table id="users" class="table table-striped table-bordered dataTable all_ads">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Advertiser Email</th>
                        <th>Ad</th>
                        <th>Ad Website Link</th>
                        <th>Publisher Email</th>
                        <th>Clicks</th>
                        <th>Impressions</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <? $index = 1?>
                    <? $all_advertisers_email = array();?>
                    <? foreach ($ads as $ad):?>
                        <tr>
                            <td>
                                <?=$index++?>
                            </td>
                            <? if(! in_array($ad->advertiser->email, $all_advertisers_email))
                                $all_advertisers_email[] = $ad->advertiser->email;
                            ?>
                            <? $bucket = Settings::instance()->get_setting('s3bucket');?>
                            <td id="<?=$ad->id?>" class="advertiser_email"><?=$ad->advertiser->email?></td>
                            <td><a class="fancybox" href="http://<?=$bucket?>.s3.amazonaws.com/<?=$ad->banner_path?>">show</a></td>
                            <td><a href="<?=$ad->scheme.$ad->host.$ad->path.$ad->fragment?>" target="_blank"><?=$ad->scheme.$ad->host?></a></td>
                            <td><?=$ad->ad_zone->publisher->email?></td>
                            <? if(key_exists($ad->id, $ads_сlicks_impress)):?>
                                <td><?=$ads_сlicks_impress[$ad->id]['clicks']?></td>
                                <td><?=$ads_сlicks_impress[$ad->id]['impressions']?></td>
                            <? else:?>
                                <td>0</td>
                                <td>0</td>
                            <? endif?>
                            <? 
                            $status_text = '';
                            $class = '';
                            switch($ad->status){
                                case '0':
                                    $status_text = 'witting<div>approval</div>';
                                    $class = '';
                                    break;
                                case '1':
                                    $status_text = 'active';
                                    $class = "label-success";
                                    break;
                                case '2':
                                    $status_text = 'paused';
                                    $class = 'label-warning';
                                    break;
                                case '3':
                                    $status_text = 'suspend';
                                    $class = 'label-important';
                                    break;
                                case '4':
                                    $status_text = 'expired';
                                    $class = 'label-important';
                                    break;
                                case '5':
                                    $status_text = 'paused';
                                    $class = 'label-warning';
                                    break;
                                case '6':
                                    $status_text = 'paused';
                                    $class = 'label-warning';
                                    break;
                              }?>
                            <td>
                              <span  id="<?=$ad->id?>"  style="text-align: center" 
                                    class="label label-status <?=$class?>">
                                  <?=$status_text?>
                              </span>
                            </td>
                            <td>
                            <div class="btn-group">
                                <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                    more
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a id="<?=$ad->id.'&1'?>" class="admin_ad_status_button"><i class="icon-edit"></i> active</a></li>
                                    <li><a id="<?=$ad->id.'&5'?>" class="admin_ad_status_button"><i class="icon-edit"></i> paused</a></li>
                                    <li><a id="<?=$ad->id?>" class="send_email_to_advertiser_button" data-toggle="modal" href="#sendAdvertiserModal"><i class="icon-edit"></i> send email to avertiser</a></li>
                                </ul>
                            </div>
                            </td>
                        </tr>
                    <? endforeach;?>
                </tbody>
            </table>
            <input type="hidden" id="all_advertisers_email" value="<?=implode(',', $all_advertisers_email)?>">
            <button id="send_to_all_advertisers_button" class="btn btn-primary" data-toggle="modal" href="#sendAllModal">Send Email To All Advertisers</button>
        </div> <!-- /widget-body -->
    </div> <!-- /widget -->
</div>
<script type="text/javascript" src="/js/library/datefilter.js"></script>

<div class="widget widget-padding span12">
    <div style="display: none;" id="sendAllModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">✕</button>
          <h3>Send Email To All Advertisers:</h3>
        </div>
        
        <form action="<?=URL::base()?>admin/ads/sendemail" method="POST">
            <input type="hidden" id="advertisers_email" name="advertisers_email" value="">
            <input type="hidden" name="all_advertisers" value="true">

            <div class="modal-body">
                <div class="control-group" style="text-align: left">
                    <label class="control-label">Subject:</label>
                    <input name="subject" style="width: 275px;" type="text" class="inputs">
                </div>

                <div class="control-group" style="text-align: left">
                    <label class="control-label">Message:</label>
                    <textarea name="message" style="width: 530px; height: 200px;"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Send">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </form>
    </div>
</div>

<div class="widget widget-padding span12">
    <div style="display: none;" id="sendAdvertiserModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">✕</button>
          <h3>Send Email To Advertiser:</h3>
        </div>

        <form action="<?=URL::base()?>admin/ads/sendemail" method="POST">
            <input type="hidden" name="advertiser" value="true">
            <div class="modal-body">
                <div class="control-group" style="text-align: left">
                    <label class="control-label">To:</label>
                    <input id="to_advertiser" name="advertiser_email" style="width: 275px;" type="text" class="inputs">
                </div>

                <div class="control-group" style="text-align: left">
                    <label class="control-label">Subject:</label>
                    <input style="width: 275px;" name="subject" type="text" class="inputs">
                </div>

                <div class="control-group" style="text-align: left">
                    <label class="control-label">Message:</label>
                    <textarea name="message" style="width: 530px; height: 150px;"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Send">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </form>
    </div>
</div>


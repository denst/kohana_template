<div class="row-fluid">
    <div class="widget widget-padding span12">
        <div class="widget-header">
            <i class="icon-user"></i>
            <h5>Publishers</h5>
        </div>
        <div class="widget-body">
                <table id="users" class="table table-striped table-bordered dataTable all_ads">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>Total Ads</th>
                            <th>Total Income</th>
                            <th>Current Plan</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <? $index = 1?>
                        <? $all_publishers_email = array();?>
                        <? foreach ($publishers as $publisher):?>
                            <tr>
                                <td><?=$index++?></td>
                                <? if(! in_array($publisher->email, $all_publishers_email))
                                    $all_publishers_email[] = $publisher->email;
                                ?>
                                <td><?=$publisher->email?></td>
                                <td><?=$publisher->host?></td>
                                <? $ads = Model::factory('ad')
                                        ->get_ads_by_publisher_id($publisher->id)?>
                                <td><?=count($ads)?></td>
                                <? if(Valid::not_empty($publisher->total_income)):?>
                                    <td><?=number_format($publisher->total_income)?>$</td>
                                <? else:?>
                                    <td>0$</td>
                                <? endif?>
                                <? if($publisher->impressions != 0):?>
                                    <input type="hidden" id="<?=$publisher->id?>" class="plan_id" 
                                           value="<?=$publisher->plan_id?>">
                                    <td><?=number_format($publisher->impressions)?> impressions</td>
                                <? else:?>
                                    <td>Free Trial</td>
                                <? endif?>
                                <? switch($publisher->account_status){
                                    case '0':
                                        $status_text = 'pending';
                                        $class = "";
                                        break;
                                    case '1':
                                        $status_text = 'active';
                                        $class = "label-success";
                                        break;
                                    case '2':
                                        $status_text = 'paused';
                                        $class = 'label-warning';
                                        break;
                                }?>
                                <td>
                                  <span  id="<?=$publisher->id?>"  style="text-align: center" 
                                        class="label label-status <?=$class?>">
                                      <?=$status_text?>
                                  </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                      <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                      More
                                        <span class="caret"></span>
                                      </a>
                                      <ul class="dropdown-menu pull-right">
                                          <form class="formEdit" id="<?=$publisher->id?>" 
                                                action="<?=URL::base()?>admin/publishers/edit" method="POST" style="margin: 0">
                                              <input type="hidden" name="publisher_id" value="<?=$publisher->id?>">
                                            <li><a href="#" class="submit_edit_form_button" id="<?=$publisher->id?>">
                                                    <i class="icon-edit"></i> edit</a>
                                            </li>
                                        </form>
                                        <? if($publisher->account_status == 1):?>
                                            <li><a id="<?=$publisher->id.'&2'?>" class="publisher_ad_status_button"><i class="icon-edit"></i> paused</a></li>
                                        <? else:?>
                                            <li><a id="<?=$publisher->id.'&1'?>" class="publisher_ad_status_button"><i class="icon-edit"></i> active</a></li>
                                        <? endif?>
                                        <li><a id="<?=$publisher->id?>" class="change_plan_button" data-toggle="modal" 
                                               href="#changePlanModal"><i class="icon-edit"></i> change plan</a></li>
                                        <input type="hidden" id="<?=$publisher->id?>" class="publisher_email" 
                                             value="<?=$publisher->email?>">
                                        <li><a id="<?=$publisher->id?>" class="send_email_button" data-toggle="modal" href="#sendPublisherModal"><i class="icon-edit"></i> send email</a></li>
                                        <li><a id="<?=$publisher->id?>" class="delete_account_button" data-toggle="modal" href="#deletePublisherAccount"><i class="icon-edit"></i> delete account</a></li>
                                      </ul>
                                    </div>
                                </td>
                            </tr>
                        <? endforeach;?>
                    </tbody>
                </table>
                <input type="hidden" id="all_advertisers_email" value="<?=implode(',', $all_publishers_email)?>">
                <button id="send_to_all_publishers_button" class="btn btn-primary" data-toggle="modal" href="#sendAllModal">
                    Send Email To All Publishers</button>
        </div> <!-- /widget-body -->
    </div> <!-- /widget -->
</div>

<div class="widget widget-padding span12">
    <div style="display: none;" id="sendAllModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">✕</button>
          <h3>Send Email To All Publishers:</h3>
        </div>
        
        <form action="<?=URL::base()?>admin/publishers/sendemail" method="POST">
            <input type="hidden" id="publishers_email" name="publishers_email" value="">
            <input type="hidden" name="all_publishers" value="true">

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
    <div style="display: none;" id="sendPublisherModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">✕</button>
          <h3>Send Email To Publisher:</h3>
        </div>

        <form action="<?=URL::base()?>admin/publishers/sendemail" method="POST">
            <input type="hidden" name="publisher" value="true">
            <div class="modal-body">
                <div class="control-group" style="text-align: left">
                    <label class="control-label">To:</label>
                    <input id="to_publisher" name="publisher_email" style="width: 275px;" type="text" class="inputs">
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

<div class="widget widget-padding span12">
    <div style="display: none;" id="changePlanModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">✕</button>
      <h3>Change Plan:</h3>
    </div>
    <form action="<?=  URL::base()?>admin/publishers/changeplan" method="post">
        <div class="modal-body">
            <div class="controls">
                    <input type="hidden" name="publisher_id" id="publisher_id" value="">
                    <select tabindex="1" name="plan" class="span6" style="width: 315px"
                            id="plans">
                        <? foreach ($plans as $plan):?>
                            <option value="<?=$plan->id?>"
                                <? //($publisher_plan->plan->id == $plan->id)? 'selected': ''?>><?=$plan->description?></option>
                        <? endforeach;?>
                    </select>
            </div>
        </div>

        <div class="modal-footer">
            <input type="submit" class="btn btn-success" value="Submit">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </form>
    </div>
</div>
<div class="modal hide" id="deletePublisherAccount">
  <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">✕</button>
      <h3>Are you sure you want to delete publisher account?</h3>
  </div>
  <div class="modal-body" style="text-align:center;">
      <div class="row-fluid">
          <div class="span10 offset1">
              <div id="modalTab">
                  <div class="tab-content">
                      <div class="tab-pane active">
                          <form method="post" action="<?=URL::base()?>admin/publishers/deleteaccount" name="completed-form">
                              <p>
                                  <input type="hidden" id="delete_publisher_id" name="publisher_id" value="">
                                  <button type="submit" class="btn btn-primary">Ok</button>
                                  <button class="btn btn-primaryclose" data-dismiss="modal">Cancel</button>
                              </p>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
<script type="text/javascript" src="/js/library/datefilter.js"></script>
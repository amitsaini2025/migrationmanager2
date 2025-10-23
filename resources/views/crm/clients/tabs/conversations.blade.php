            <!-- Emails Tab -->
            <div class="tab-pane" id="conversations-tab">
                <div class="card full-width emails-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs">
                        <button class="subtab-button active" data-subtab="inbox">Inbox</button>
                        <button class="subtab-button" data-subtab="sent">Sent</button>
                        <!--<button class="subtab-button" data-subtab="sms">SMS</button>-->
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab-content" id="subtab-content">
                        <!-- Inbox Subtab -->
                        <div class="subtab-pane active" id="inbox-subtab">
                            <div class="email-header">
                                <h3>Inbox Emails</h3>
                                <div class="email-actions">
                                    <button class="btn btn-primary btn-sm uploadAndFetchMail" id="new-email-btn">
                                        <i class="fas fa-plus"></i> Upload Inbox Mail
                                    </button>
                                </div>
                            </div>
                            <div class="email-filters">
                                <select  class="filter-select" id="filter-status">
                                    <option value="">Filter by Status</option>
                                    <option value="2">Unread</option>
                                    <option value="1">Read</option>
                                </select>
                                <input type="text" name="search_communication" class="search-input" id="search-communication" placeholder="Search Communication...">
                            </div>
                            <div class="email-list" id="email-list">
                                <?php
                                //inbox mail
                                $mailreports = \App\Models\MailReport::where('client_id',$fetchedData->id)->where('type','client')->where('mail_type',1)->where('conversion_type','conversion_email_fetch')->where('mail_body_type','inbox')->orderby('created_at', 'DESC')->get();
                                //dd($mailreports);
                                foreach($mailreports as $mailreport)
                                {
                                    $DocInfo = \App\Models\Document::select('id','doc_type','myfile','myfile_key','mail_type')->where('id',$mailreport->uploaded_doc_id)->first();
                                    $AdminInfo = \App\Models\Admin::select('client_id')->where('id',$mailreport->client_id)->first();
                                    ?>
                                    <div class="email-card" data-matterid="{{$mailreport->client_matter_id}}">
                                        <div class="email-meta">
                                            <span class="author-initial">{{ strtoupper(substr(@$mailreport->from_mail, 0, 1)) }}</span>
                                            <div class="email-info">
                                                <span class="author-name">From: {{@$mailreport->from_mail}}</span>
                                                <span class="email-timestamp">
                                                    <?php if(isset($mailreport->fetch_mail_sent_time) && $mailreport->fetch_mail_sent_time != "") { ?>
                                                        <span>{{$mailreport->fetch_mail_sent_time}}</span>
                                                    <?php }  else {?>
                                                        <span></span>
                                                    <?php } ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="email-body">
                                            <h4>{{ substr(@$mailreport->subject, 0, 50) }}</h4>
                                            <p>To:{{@$mailreport->to_mail}}</p>
                                        </div>
                                        <div class="email-actions">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            if($DocInfo)
                                            { ?>
                                                <?php if( isset($DocInfo->myfile_key) && $DocInfo->myfile_key != ""){ ?>
                                                <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $DocInfo->myfile; ?>" >Preview</a>
                                                <?php } else { ?>
                                                    <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank" href="<?php echo $url.$AdminInfo->client_id.'/'.$DocInfo->doc_type.'/'.$DocInfo->mail_type.'/'.$DocInfo->myfile; ?>">Preview</a>
                                                <?php } ?>
                                            <?php
                                            }?>
                                            <button class="btn btn-link create_note" datamailid="{{$mailreport->id}}" datasubject="{{@$mailreport->subject}}" datatype="mailnote">Create Note</button>
                                            <button class="btn btn-link inbox_reassignemail_modal" memail_id="{{@$mailreport->id}}" user_mail="{{@$mailreport->to_mail}}" uploaded_doc_id="{{@$mailreport->uploaded_doc_id}}" href="javascript:;">Reassign</button>

                                        </div>
                                    </div>
                                <?php
                                }  //end foreach ?>
                            </div>
                        </div>

                        <!-- Sent Subtab -->
                        <div class="subtab-pane" id="sent-subtab">
                            <div class="email-header">
                                <h3>Sent Emails</h3>
                                <div class="email-actions">
                                    <a class="btn btn-primary btn-sm clientemail" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Compose Email</a>
                                    <a class="btn btn-primary btn-sm uploadSentAndFetchMail" id="new-email-btn-sent" href="javascript:;"><i class="fas fa-plus"></i> Upload Sent Mail</a>
                                </div>
                            </div>
                            <div class="email-filters">
                                <select class="filter-select" id="filter-type1">
                                    <option value="">Filter by Type</option>
                                    <option value="1">Assigned</option>
                                    <option value="2">Delivered</option>
                                </select>

                                <select  class="filter-select" id="filter-status1">
                                    <option value="">Filter by Status</option>
                                    <option value="2">Unread</option>
                                    <option value="1">Read</option>
                                </select>


                                <input type="text" class="search-input" id="search-communication1" placeholder="Search Communication...">
                            </div>
                            <div class="email-list1" id="email-list1">
                                <?php
                                //Sent Mail after assign user and Compose email
                                $mailreports = \App\Models\MailReport::where('client_id', $fetchedData->id)
                                ->where('type', 'client')
                                ->where('mail_type', 1)
                                ->where(function($query) {
                                    $query->whereNull('conversion_type')
                                    ->orWhere(function($subQuery) {
                                        $subQuery->where('conversion_type', 'conversion_email_fetch')
                                        ->where('mail_body_type', 'sent');
                                    });
                                })
                                ->orderBy('created_at', 'DESC')
                                ->get();
                                foreach($mailreports as $mailreport)
                                {
                                    $admin = \App\Models\Admin::where('id', $mailreport->user_id)->first();
                                    $client = \App\Models\Admin::Where('id', $fetchedData->id)->first();
                                    $subject = str_replace('{Client First Name}',$client->first_name, $mailreport->subject);
                                    $message = $mailreport->message;
                                    $message = str_replace('{Client First Name}',$client->first_name, $message);
                                    $message = str_replace('{Client Assignee Name}',$client->first_name, $message);
                                    $message = str_replace('{Company Name}',Auth::user()->company_name, $message);
                                    ?>
                                    <div class="email-card" data-matterid="{{$mailreport->client_matter_id}}">
                                        <div class="email-meta">
                                            <span class="author-initial">{{ strtoupper(substr($admin->first_name, 0, 1)) }}</span>
                                            <div class="email-info">
                                                <span class="author-name">Sent by:
                                                    <strong>{{@$admin->first_name}}</strong> [{{$mailreport->from_mail}}]

                                                    <?php
                                                if( isset($mailreport->conversion_type) && $mailreport->conversion_type != ""){ ?>
                                                    <span style="background: #21ba45;color: #fff;font-size: 12px;line-height: 16px;padding: 3px 8px;border-radius: 4px;">Assigned</span>
                                                <?php } else { ?>
                                                    <span style="background: #FCCD02;color: #fff;font-size: 12px;line-height: 16px;padding: 3px 8px;border-radius: 4px;">Delivered</span>
                                                <?php } ?>
                                                </span>



                                                <span class="email-timestamp">
                                                    <?php
                                                    if( isset($mailreport->conversion_type) && $mailreport->conversion_type == "conversion_email_fetch")
                                                    {?>
                                                        <div class="date">
                                                            <?php if(isset($mailreport->fetch_mail_sent_time) && $mailreport->fetch_mail_sent_time != "") { ?>
                                                                <span>{{ $mailreport->fetch_mail_sent_time}}</span>
                                                            <?php }  else {?>
                                                                <span></span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    }
                                                    else
                                                    { ?>
                                                        <div class="date">
                                                            <?php if(isset($mailreport->created_at) && $mailreport->created_at != "") { ?>
                                                                <span>{{@date('d/m/Y h:i A',strtotime($mailreport->created_at))}}</span>
                                                            <?php }  else {?>
                                                                <span></span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    }?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="email-body">
                                            <h4>{{$subject}}</h4>
                                            <p>Sent To: {{$client->email}}</p>
                                        </div>
                                        <div class="email-actions">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            $AdminInfo = \App\Models\Admin::select('client_id')->where('id',$fetchedData->id)->first();
                                            if( isset($mailreport->uploaded_doc_id) && $mailreport->uploaded_doc_id != "")
                                            {
                                                $DocInfo = \App\Models\Document::select('id','doc_type','myfile','myfile_key','mail_type')->where('id',$mailreport->uploaded_doc_id)->first();
                                                if($DocInfo)
                                                { ?>
                                                    <?php if( isset($DocInfo->myfile_key) && $DocInfo->myfile_key != ""){ ?>
                                                        <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $DocInfo->myfile; ?>" ><i class="fas fa-eye"></i> Preview</a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-link mail_preview_modal" memail_id="{{@$mailreport->id}}" target="_blank"  href="<?php echo $url.$AdminInfo->client_id.'/'.$DocInfo->doc_type.'/'.$DocInfo->mail_type.'/'.$DocInfo->myfile; ?>" ><i class="fas fa-eye"></i> Preview</a>
                                                    <?php } ?>
                                                <?php
                                                }
                                            }
                                            else
                                            { ?>
                                                <a class="btn btn-link sent_mail_preview_modal" memail_message="{{@$mailreport->message}}" memail_subject="{{@$mailreport->subject}}"><i class="fas fa-eye"></i> Preview Mail</a>
                                            <?php
                                            } ?>

                                            <button class="btn btn-link create_note" datamailid="{{$mailreport->id}}" datasubject="{{@$mailreport->subject}}" datatype="mailnote">Create Note</button>
                                            <?php
                                            if( isset($mailreport->conversion_type) && $mailreport->conversion_type != "")
                                            { ?>
                                                <button class="btn btn-link sent_reassignemail_modal" memail_id="{{@$mailreport->id}}" user_mail="{{@$mailreport->to_mail}}" uploaded_doc_id="{{@$mailreport->uploaded_doc_id}}" href="javascript:;">Reassign</button>
                                            <?php
                                            } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
